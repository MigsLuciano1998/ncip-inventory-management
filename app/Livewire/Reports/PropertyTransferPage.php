<?php

namespace App\Livewire\Reports;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentMovementLog;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyTransferPage extends Component
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
            ->where('action', EquipmentMovementLog::ACTION_TRANSFERRED)
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
        $transfers = EquipmentMovementLog::query()
            ->with([
                'equipment.equipmentType',
                'equipment.equipmentCategory',
                'office',
                'previousOffice',
                'assignment.employee',
            ])
            ->where('action', EquipmentMovementLog::ACTION_TRANSFERRED)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('equipment_movement_logs as latest_transfers')
                    ->where('action', EquipmentMovementLog::ACTION_TRANSFERRED)
                    ->groupBy('equipment_id');
            })
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($query) use ($term) {
                    $query->where('owner_name', 'like', $term)
                        ->orWhere('previous_owner_name', 'like', $term)
                        ->orWhere('remarks', 'like', $term)
                        ->orWhereHas('equipment', function ($equipment) use ($term) {
                            $equipment->where('property_no', 'like', $term)
                                ->orWhere('description', 'like', $term)
                                ->orWhere('brand', 'like', $term)
                                ->orWhere('model', 'like', $term);
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
                'previousOffice',
                'assignment.employee.office',
            ])
                ->where('action', EquipmentMovementLog::ACTION_TRANSFERRED)
                ->find($this->printLogId)
            : null;

        $equipment = $printLog?->equipment;
        $fromEmployee = $printLog ? $this->findEmployeeByName($printLog->previous_owner_name) : null;
        $toEmployee = $printLog ? $this->findEmployeeByName($printLog->owner_name) : null;

        return view('livewire.reports.property-transfer-page', [
            'transfers' => $transfers,
            'equipment' => $equipment,
            'printLog' => $printLog,
            'fromEmployee' => $fromEmployee,
            'toEmployee' => $toEmployee,
            'fromName' => $fromEmployee?->last_name_first ?: ($printLog?->previous_owner_name ?: ''),
            'toName' => $toEmployee?->last_name_first ?: ($printLog?->owner_name ?: ''),
            'approvedBy' => $this->defaultApprover(),
            'issuedBy' => $this->defaultIssuer(),
            'transferType' => $printLog ? $this->transferType($printLog) : null,
            'transferTypeOther' => $printLog?->transfer_type_other,
            'ptrNumber' => $printLog ? $this->ptrNumber($printLog) : null,
            'icsNoDate' => $equipment && $printLog ? $this->icsNoDate($equipment, $printLog) : null,
            'itemDescription' => $equipment ? $this->itemDescription($equipment) : null,
            'condition' => $equipment ? $this->inventoryCondition($equipment) : null,
            'reason' => $printLog ? $this->transferReason($printLog) : null,
        ]);
    }

    protected function defaultIssuer(): ?Employee
    {
        return Employee::query()
            ->where('last_name', 'La Madrid')
            ->where('first_name', 'Lloyd Neil')
            ->first();
    }

    protected function defaultApprover(): ?Employee
    {
        return Employee::query()
            ->where('last_name', 'Addog')
            ->where('first_name', 'Atanacio')
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

    protected function transferType(EquipmentMovementLog $log): string
    {
        if (filled($log->transfer_type)) {
            return $log->transfer_type;
        }

        $remarks = strtolower((string) $log->remarks);

        if (str_contains($remarks, 'donat')) {
            return EquipmentMovementLog::TRANSFER_DONATION;
        }

        if (str_contains($remarks, 'relocat')) {
            return EquipmentMovementLog::TRANSFER_RELOCATE;
        }

        if ($log->action === EquipmentMovementLog::ACTION_TRANSFERRED
            || $log->action === EquipmentMovementLog::ACTION_ASSIGNED) {
            return EquipmentMovementLog::TRANSFER_REASSIGNMENT;
        }

        return EquipmentMovementLog::TRANSFER_OTHERS;
    }

    protected function transferReason(EquipmentMovementLog $log): string
    {
        if (filled($log->remarks)) {
            return $log->remarks;
        }

        return 'Change of accountable officer';
    }

    protected function ptrNumber(EquipmentMovementLog $log): string
    {
        $date = $log->created_at instanceof Carbon ? $log->created_at : Carbon::parse($log->created_at);

        return sprintf('%s-%03d', $date->format('Y-m'), $log->id);
    }

    protected function icsNoDate(Equipment $equipment, EquipmentMovementLog $log): string
    {
        $ics = trim((string) $equipment->par_ics);
        $date = $equipment->par_ics_issued_date?->format('m-d-Y')
            ?? $log->created_at?->format('m-d-Y');

        if ($ics !== '' && $date) {
            return $ics.'/'.$date;
        }

        return $ics !== '' ? $ics : '—';
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

        return $description !== '' ? $description : '—';
    }

    protected function inventoryCondition(Equipment $equipment): string
    {
        if ($equipment->status_last_inventory === false) {
            return 'Unserviceable';
        }

        return 'Serviceable';
    }
}
