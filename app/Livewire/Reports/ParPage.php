<?php

namespace App\Livewire\Reports;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentMovementLog;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ParPage extends Component
{
    use WithPagination;

    public $search = '';

    public $printLogId;

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->printLogId = null;
    }

    public function viewLog(int $logId): void
    {
        $log = EquipmentMovementLog::query()
            ->where('action', EquipmentMovementLog::ACTION_ASSIGNED)
            ->whereKey($logId)
            ->first();

        if (! $log) {
            return;
        }

        $this->printLogId = $log->id;
    }

    public function closeView(): void
    {
        $this->printLogId = null;
    }

    public function render()
    {
        $acknowledgements = EquipmentMovementLog::query()
            ->with([
                'equipment.equipmentType',
                'equipment.equipmentCategory',
                'office',
                'assignment.employee',
                'assignment.office',
            ])
            ->where('action', EquipmentMovementLog::ACTION_ASSIGNED)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('equipment_movement_logs as latest_assignments')
                    ->where('action', EquipmentMovementLog::ACTION_ASSIGNED)
                    ->groupBy('equipment_id');
            })
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($query) use ($term) {
                    $query->where('owner_name', 'like', $term)
                        ->orWhereHas('equipment', function ($equipment) use ($term) {
                            $equipment->where('property_no', 'like', $term)
                                ->orWhere('description', 'like', $term)
                                ->orWhere('brand', 'like', $term)
                                ->orWhere('model', 'like', $term);
                        })
                        ->orWhereHas('assignment.employee', function ($employee) use ($term) {
                            $employee->where('first_name', 'like', $term)
                                ->orWhere('middle_name', 'like', $term)
                                ->orWhere('last_name', 'like', $term);
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $printLog = $this->printLogId
            ? EquipmentMovementLog::with([
                'equipment.equipmentCategory',
                'equipment.equipmentType',
                'equipment.ppeCategory',
                'equipment.office',
                'office',
                'assignment.employee.office',
            ])
                ->where('action', EquipmentMovementLog::ACTION_ASSIGNED)
                ->find($this->printLogId)
            : null;

        $equipment = $printLog?->equipment;
        $receivedBy = $printLog?->assignment?->employee
            ?: ($printLog ? $this->findEmployeeByName($printLog->owner_name) : null);
        $issuedBy = $this->defaultIssuer();
        $issuedDate = $printLog
            ? ($equipment?->par_ics_issued_date
                ?? $printLog->assignment?->date_assigned
                ?? $printLog->created_at)
            : null;

        return view('livewire.reports.par-page', [
            'acknowledgements' => $acknowledgements,
            'equipment' => $equipment,
            'printLog' => $printLog,
            'selectedIssuedBy' => $issuedBy,
            'receivedBy' => $receivedBy,
            'parNumber' => $equipment && $issuedDate ? $this->parNumber($equipment, $issuedDate) : null,
            'fundCluster' => $equipment ? $this->fundCluster($equipment) : null,
            'itemDescription' => $equipment ? $this->itemDescription($equipment) : null,
            'issuedDate' => $issuedDate,
        ]);
    }

    protected function defaultIssuer(): ?Employee
    {
        return Employee::query()
            ->where('last_name', 'La Madrid')
            ->where('first_name', 'Lloyd Neil')
            ->first();
    }

    protected function findEmployeeByName(?string $name): ?Employee
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        return Employee::query()
            ->with('office')
            ->get()
            ->first(function (Employee $employee) use ($name) {
                return strcasecmp($employee->full_name, $name) === 0
                    || strcasecmp($employee->last_name_first, $name) === 0;
            });
    }

    protected function parNumber(Equipment $equipment, mixed $issuedDate): string
    {
        if (filled($equipment->par_ics)) {
            return $equipment->par_ics;
        }

        $short = strtoupper($equipment->equipmentCategory?->short_name ?: 'ICT');
        $date = $issuedDate instanceof Carbon ? $issuedDate : Carbon::parse($issuedDate);
        $series = str_pad((string) ($equipment->series_number ?? 0), 4, '0', STR_PAD_LEFT);

        return sprintf('PAR-%s-%s-%s', $short, $date->format('Y-m'), $series);
    }

    protected function fundCluster(Equipment $equipment): string
    {
        $title = $equipment->ppeCategory?->title
            ?? $equipment->equipmentCategory?->title
            ?? 'Information and Communications Technology Equipment';

        return 'PPE '.$title;
    }

    protected function itemDescription(Equipment $equipment): string
    {
        $description = trim((string) $equipment->description);

        if ($description === '') {
            $description = trim(implode(' ', array_filter([
                $equipment->brand,
                $equipment->model,
                $equipment->equipmentType?->name,
            ])));
        }

        if ($equipment->serial_no && ! str_contains(strtolower($description), strtolower($equipment->serial_no))) {
            $description = trim($description.', sn - '.$equipment->serial_no, ', ');
        }

        return $description !== '' ? $description : '—';
    }
}
