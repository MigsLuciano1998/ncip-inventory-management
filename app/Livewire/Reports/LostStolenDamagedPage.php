<?php

namespace App\Livewire\Reports;

use App\Models\Equipment;
use App\Models\LostStolenDamagedEquipment;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LostStolenDamagedPage extends Component
{
    use WithPagination;

    public $search = '';

    public $showAddModal = false;

    public $property_search = '';

    public $equipmentId;

    public $status = '';

    public $remarks = '';

    public $addError = '';

    public $statusFilter = '';

    public array $selectedIds = [];

    public $printError = '';

    public array $printIds = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->printIds = [];
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
        $this->printIds = [];
        $this->printError = '';
    }

    public function openAdd(): void
    {
        $this->resetAddForm();
        $this->showAddModal = true;
        $this->printIds = [];
    }

    public function closeAdd(): void
    {
        $this->showAddModal = false;
        $this->resetAddForm();
    }

    public function updatedPropertySearch(): void
    {
        $this->addError = '';

        if ($this->equipmentId) {
            $current = Equipment::query()->whereKey($this->equipmentId)->value('property_no');

            if ($current !== null && strcasecmp(trim((string) $this->property_search), trim((string) $current)) === 0) {
                return;
            }
        }

        $this->equipmentId = null;
    }

    public function selectProperty(string $propertyNo): void
    {
        $this->property_search = $propertyNo;
        $this->addError = '';

        $equipment = Equipment::query()
            ->where('property_no', $propertyNo)
            ->first();

        $this->equipmentId = $equipment?->id;
    }

    public function save(): void
    {
        $this->addError = '';
        $term = trim($this->property_search);

        if ($term === '') {
            $this->addError = 'Enter a property number.';

            return;
        }

        $equipment = $this->equipmentId
            ? Equipment::query()->find($this->equipmentId)
            : Equipment::query()->where('property_no', $term)->first()
                ?? Equipment::query()->where('property_no', 'like', "%{$term}%")->orderBy('property_no')->first();

        if (! $equipment) {
            $this->addError = 'No equipment found for that property number.';

            return;
        }

        if (! in_array($this->status, array_keys(LostStolenDamagedEquipment::statuses()), true)) {
            $this->addError = 'Select Lost, Stolen, Damaged, or Destroyed.';

            return;
        }

        LostStolenDamagedEquipment::create([
            'equipment_id' => $equipment->id,
            'status' => $this->status,
            'date' => now()->toDateString(),
            'remarks' => $this->blankToNull($this->remarks),
        ]);

        $this->closeAdd();
        $this->resetPage();
        session()->flash('message', 'PPE property report saved.');
    }

    public function viewRecord(int $id): void
    {
        $record = LostStolenDamagedEquipment::query()->whereKey($id)->first();

        if (! $record) {
            return;
        }

        $this->showAddModal = false;
        $this->printIds = [$record->id];
        $this->printError = '';
    }

    public function printSelected(): void
    {
        $this->printError = '';

        if ($this->statusFilter === '') {
            return;
        }

        $ids = array_values(array_filter(array_map('intval', $this->selectedIds)));

        if ($ids === []) {
            $this->printError = 'Select at least one item to print.';

            return;
        }

        $this->showAddModal = false;
        $this->printIds = $ids;
    }

    public function closeView(): void
    {
        $this->printIds = [];
        $this->printError = '';
    }

    public function render()
    {
        $records = LostStolenDamagedEquipment::query()
            ->with([
                'equipment.equipmentType',
                'equipment.ppeCategory',
                'equipment.equipmentCategory',
                'equipment.office.province',
                'equipment.activeAssignment.employee',
            ])
            ->when($this->statusFilter !== '', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($query) use ($term) {
                    $query->where('remarks', 'like', $term)
                        ->orWhere('status', 'like', $term)
                        ->orWhereHas('equipment', function ($equipment) use ($term) {
                            $equipment->where('property_no', 'like', $term)
                                ->orWhere('description', 'like', $term)
                                ->orWhere('brand', 'like', $term)
                                ->orWhere('model', 'like', $term)
                                ->orWhereHas('equipmentType', fn ($type) => $type->where('name', 'like', $term));
                        });
                });
            })
            ->latest('date')
            ->latest('id')
            ->paginate(10);

        $printRecords = $this->printIds !== []
            ? LostStolenDamagedEquipment::with([
                'equipment.equipmentType',
                'equipment.ppeCategory',
                'equipment.equipmentCategory',
                'equipment.office.province',
                'equipment.activeAssignment.employee',
            ])
                ->whereIn('id', $this->printIds)
                ->orderBy('id')
                ->get()
            : collect();

        $printRecord = $printRecords->first();

        return view('livewire.reports.lost-stolen-damaged-page', [
            'records' => $records,
            'printRecord' => $printRecord,
            'printRecords' => $printRecords,
            'printEquipment' => $printRecord?->equipment,
            'suggestions' => $this->propertySuggestions(),
            'selectedEquipment' => $this->equipmentId ? Equipment::find($this->equipmentId) : null,
            'formData' => $printRecords->isNotEmpty() ? $this->formDataFromRecords($printRecords) : null,
            'selectionEnabled' => $this->statusFilter !== '',
        ]);
    }

    protected function propertySuggestions()
    {
        if (! $this->showAddModal) {
            return collect();
        }

        $term = trim($this->property_search);

        if (strlen($term) < 2 || $this->equipmentId) {
            return collect();
        }

        return Equipment::query()
            ->where('property_no', 'like', "%{$term}%")
            ->orderBy('property_no')
            ->limit(8)
            ->get(['id', 'property_no', 'brand', 'model']);
    }

    protected function resetAddForm(): void
    {
        $this->reset(['property_search', 'equipmentId', 'status', 'remarks', 'addError']);
    }

    protected function blankToNull(mixed $value): mixed
    {
        $value = is_string($value) ? trim($value) : $value;

        return $value === '' || $value === null ? null : $value;
    }

    protected function formData(LostStolenDamagedEquipment $record): array
    {
        return $this->formDataFromRecords(collect([$record]));
    }

    protected function formDataFromRecords($records): array
    {
        $first = $records->first();
        $equipment = $first?->equipment;
        $office = $equipment?->office;
        $employee = $equipment?->activeAssignment?->employee;

        $department = trim(implode(', ', array_filter([
            $office?->office_name,
            $office?->province?->province_name,
        ])));

        $ppeTitle = $equipment?->ppeCategory?->title
            ?? $equipment?->equipmentCategory?->title
            ?? 'Office Equipment';

        $date = $first?->date instanceof Carbon
            ? $first->date
            : ($first?->date ? Carbon::parse($first->date) : now());

        $items = $records->map(function (LostStolenDamagedEquipment $record) {
            $item = $record->equipment;
            $amount = Equipment::parseCostAmount($item?->cost);
            $description = trim((string) ($item?->description ?? ''));

            if ($description === '') {
                $description = trim(implode(' ', array_filter([
                    $item?->brand,
                    $item?->model,
                    $item?->equipmentType?->name,
                ])));
            }

            return [
                'propertyNo' => $item?->property_no ?? '',
                'description' => $description,
                'acquisitionCost' => $amount === null ? '' : number_format($amount, 2),
            ];
        })->values()->all();

        $circumstances = $records
            ->pluck('remarks')
            ->filter(fn ($remark) => filled($remark))
            ->unique()
            ->implode("\n");

        return [
            'entityName' => 'National Commission on Indigenous Peoples',
            'departmentOffice' => $department,
            'accountableOfficer' => $employee?->last_name_first ?? '',
            'designation' => $employee?->position ?? '',
            'fundCluster' => 'PPE '.$ppeTitle,
            'rlsddspNo' => $first ? sprintf('%s-%03d', $date->format('Y-m'), $first->id) : '',
            'rlsddspDate' => $date->format('m/d/Y'),
            'parIcsNo' => $equipment?->par_ics ?? '',
            'icsDate' => $equipment?->par_ics_issued_date?->format('m/d/Y') ?? '',
            'status' => $first?->status ?? $this->statusFilter,
            'items' => $items,
            'circumstances' => $circumstances,
        ];
    }
}
