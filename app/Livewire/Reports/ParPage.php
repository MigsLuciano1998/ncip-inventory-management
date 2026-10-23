<?php

namespace App\Livewire\Reports;

use App\Models\Employee;
use App\Models\Equipment;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ParPage extends Component
{
    use WithPagination;

    public $employee_search = '';

    public $employeeId;

    public array $selectedIds = [];

    public array $printIds = [];

    public $viewEquipmentId;

    public function updatingEmployeeSearch(): void
    {
        if ($this->employeeId) {
            return;
        }

        $this->resetPage();
    }

    public function selectEmployee(?int $employeeId): void
    {
        $this->employeeId = $employeeId;
        $this->selectedIds = [];
        $this->printIds = [];
        $this->viewEquipmentId = null;
        $this->resetPage();

        if ($employeeId) {
            $employee = Employee::find($employeeId);
            $this->employee_search = $employee?->last_name_first ?? '';
        } else {
            $this->employee_search = '';
        }
    }

    public function updatedEmployeeSearch($value): void
    {
        if (! $this->employeeId) {
            return;
        }

        $current = Employee::query()->whereKey($this->employeeId)->first();
        $label = $current?->last_name_first ?? '';

        if (strcasecmp(trim((string) $value), trim($label)) !== 0) {
            $this->employeeId = null;
            $this->selectedIds = [];
            $this->printIds = [];
            $this->viewEquipmentId = null;
            $this->resetPage();
        }
    }

    public function viewEquipment(int $equipmentId): void
    {
        $equipment = $this->employeeEquipmentQuery()->whereKey($equipmentId)->first();

        if (! $equipment) {
            return;
        }

        $this->viewEquipmentId = $equipment->id;
        $this->printIds = [$equipment->id];
    }

    public function closeView(): void
    {
        $this->viewEquipmentId = null;
        $this->printIds = [];
    }

    public function printSelected(): void
    {
        $ids = $this->normalizedSelectedIds();

        if ($ids === []) {
            return;
        }

        $this->viewEquipmentId = null;
        $this->printIds = $ids;
    }

    public function render()
    {
        $employee = $this->employeeId
            ? Employee::with('office')->find($this->employeeId)
            : null;

        $equipments = $employee
            ? $this->employeeEquipmentQuery()
                ->orderBy('property_no')
                ->paginate(10)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);

        $printEquipment = $this->printIds !== []
            ? $this->employeeEquipmentQuery()
                ->whereIn('id', $this->printIds)
                ->orderBy('property_no')
                ->get()
            : collect();

        $viewEquipment = $this->viewEquipmentId
            ? $printEquipment->firstWhere('id', $this->viewEquipmentId)
                ?? $this->employeeEquipmentQuery()->whereKey($this->viewEquipmentId)->first()
            : null;

        $formItems = $printEquipment->isNotEmpty() ? $printEquipment : collect();
        $first = $formItems->first();
        $issuedDate = $first
            ? ($first->par_ics_issued_date
                ?? $first->activeAssignment?->date_assigned
                ?? $first->date_acquired
                ?? now())
            : null;

        return view('livewire.reports.par-page', [
            'employee' => $employee,
            'employees' => $this->employeeSuggestions(),
            'equipments' => $equipments,
            'printEquipment' => $printEquipment,
            'viewEquipment' => $viewEquipment,
            'formItems' => $formItems,
            'equipment' => $first,
            'selectedIssuedBy' => $this->defaultIssuer(),
            'receivedBy' => $employee,
            'parNumber' => $first && $issuedDate ? $this->parNumber($first, $issuedDate) : null,
            'fundCluster' => $first ? $this->fundCluster($first) : null,
            'issuedDate' => $issuedDate,
            'canPrint' => $this->normalizedSelectedIds() !== [],
        ]);
    }

    protected function employeeEquipmentQuery()
    {
        return Equipment::query()
            ->with([
                'equipmentCategory',
                'equipmentType',
                'ppeCategory',
                'office',
                'activeAssignment.employee.office',
            ])
            ->where('classification', Equipment::CLASSIFICATION_PPE)
            ->when($this->employeeId, function ($q) {
                $q->whereHas('activeAssignment', function ($assignment) {
                    $assignment->where('employee_id', $this->employeeId);
                });
            }, function ($q) {
                $q->whereRaw('1 = 0');
            });
    }

    protected function employeeSuggestions()
    {
        $term = trim($this->employee_search);

        if ($this->employeeId || strlen($term) < 1) {
            return collect();
        }

        return Employee::query()
            ->where('status', 1)
            ->where(function ($query) use ($term) {
                $query->where('last_name', 'like', "%{$term}%")
                    ->orWhere('first_name', 'like', "%{$term}%")
                    ->orWhere('middle_name', 'like', "%{$term}%")
                    ->orWhere('ee_sequence_number', 'like', "%{$term}%");
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(10)
            ->get();
    }

    protected function normalizedSelectedIds(): array
    {
        return collect($this->selectedIds)
            ->map(fn ($id) => is_numeric($id) ? (int) $id : null)
            ->filter(fn ($id) => $id !== null)
            ->unique()
            ->values()
            ->all();
    }

    protected function defaultIssuer(): ?Employee
    {
        return Employee::query()
            ->where('last_name', 'La Madrid')
            ->where('first_name', 'Lloyd Neil')
            ->first();
    }

    protected function parNumber(Equipment $equipment, mixed $issuedDate): string
    {
        if (filled($equipment->par_ics) && str_starts_with(strtoupper(trim($equipment->par_ics)), 'PAR')) {
            return $equipment->par_ics;
        }

        $short = strtoupper($equipment->tag ?: $equipment->equipmentCategory?->short_name ?: 'ICT');
        $date = $issuedDate instanceof Carbon ? $issuedDate : Carbon::parse($issuedDate);
        $series = str_pad((string) ($equipment->series_number ?? 0), 4, '0', STR_PAD_LEFT);

        return sprintf('PAR-%s-%s-%s', $short, $date->format('Y-m'), $series);
    }

    protected function fundCluster(Equipment $equipment): string
    {
        $title = $equipment->equipmentCategory?->title
            ?? $equipment->ppeCategory?->title
            ?? 'Information and Communications Technology Equipment';

        return 'PPE '.$title;
    }
}
