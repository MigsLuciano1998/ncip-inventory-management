<?php

namespace App\Livewire\Reports;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentDepreciation;
use Livewire\Component;
use Livewire\WithPagination;

class IiruppPage extends Component
{
    use WithPagination;

    public $search = '';

    public $viewRecordId;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function viewRecord(int $id): void
    {
        $this->viewRecordId = $id;
    }

    public function closeView(): void
    {
        $this->viewRecordId = null;
    }

    public function render()
    {
        $records = $this->recordsQuery()->paginate(10);

        $viewRecord = $this->viewRecordId
            ? $this->recordsQuery()->whereKey($this->viewRecordId)->first()
            : null;

        return view('livewire.reports.iirupp-page', [
            'records' => $records,
            'viewRecord' => $viewRecord,
            'formData' => $this->formData($viewRecord),
        ]);
    }

    protected function recordsQuery()
    {
        return EquipmentDepreciation::query()
            ->with([
                'equipment.equipmentType',
                'equipment.equipmentCategory',
                'equipment.ppeCategory',
                'equipment.office.province.region',
                'equipment.activeAssignment.employee.office.province.region',
            ])
            ->when($this->search, function ($query) {
                $search = trim($this->search);
                $query->whereHas('equipment', function ($equipment) use ($search) {
                    $equipment->where('property_no', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('serial_no', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%");
                });
            })
            ->latest();
    }

    protected function formData(?EquipmentDepreciation $record): array
    {
        $equipment = $record?->equipment;
        $officer = $equipment?->activeAssignment?->employee ?? $this->namedEmployee('Lloyd Neil', 'La Madrid');
        $office = $officer?->office ?? $equipment?->office;
        $station = collect([
            $office?->office_name,
            $office?->province?->region?->region_short_name,
        ])->filter()->implode(', ');

        $cost = Equipment::parseCostAmount($equipment?->cost);
        $accumulated = $record?->accumulated_depreciation_end ?? $record?->accumulated_depreciation_beginning;
        $description = trim((string) ($equipment?->description ?? ''));

        if ($description === '') {
            $description = trim(implode(' ', array_filter([
                $equipment?->brand,
                $equipment?->model,
                $equipment?->equipmentType?->name,
            ])));
        }

        return [
            'entityName' => 'National Commission on Indigenous Peoples',
            'accountableOfficer' => $officer
                ? strtoupper($officer->full_name)
                : 'LLOYD NEIL C. LA MADRID',
            'designation' => $officer?->position ?: 'AO III',
            'station' => $station !== '' ? strtoupper($station) : '',
            'fundCluster' => $this->fundCluster($equipment),
            'asOfDate' => now()->format('F d, Y'),
            'approvedBy' => strtoupper($this->namedEmployee('Wilson', 'Kalangeg')?->full_name ?? 'ATTY. WILSON K. KALANGEG'),
            'approvedByPosition' => $this->namedEmployee('Wilson', 'Kalangeg')?->position ?: 'TMSD Chief/Overseer',
            'inspectedBy' => 'ENGR. VICTOR V. BUMATAYONG',
            'witness' => strtoupper($this->namedEmployee('Jerald', 'Camilo')?->full_name ?? 'JERALD P. CAMILO'),
            'items' => $equipment ? [[
                'date_acquired' => $equipment->date_acquired?->format('n/j/Y')
                    ?? $equipment->date_purchased?->format('n/j/Y')
                    ?? '',
                'particulars' => $description,
                'property_no' => $equipment->property_no,
                'qty' => 1,
                'unit_cost' => $cost,
                'total_cost' => $cost,
                'accumulated' => $accumulated !== null ? (float) $accumulated : null,
                'carrying' => $record?->book_value !== null ? (float) $record->book_value : null,
                'remarks' => $record?->nature_of_repair ?: $equipment->remarks,
                'sale' => null,
                'transfer' => null,
                'destruction' => '',
                'others' => '',
                'disposal_total' => null,
                'appraised' => $record?->salvage_value !== null ? (float) $record->salvage_value : null,
                'or_no' => '',
                'or_amount' => null,
            ]] : [],
        ];
    }

    protected function fundCluster(?Equipment $equipment): string
    {
        if (! $equipment) {
            return 'PPE';
        }

        $title = $equipment->equipmentCategory?->title
            ?? $equipment->ppeCategory?->title
            ?? 'Information and Communications Technology Equipment';

        return 'PPE '.$title;
    }

    protected function namedEmployee(string $firstName, string $lastName): ?Employee
    {
        return Employee::query()
            ->with('office.province.region')
            ->where('first_name', $firstName)
            ->where('last_name', $lastName)
            ->first();
    }
}
