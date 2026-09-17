<?php

namespace App\Livewire\Reports;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentMovementLog;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ReturnedPropertyPage extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedLogId;

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->selectedLogId = null;
    }

    public function viewReturn(int $logId): void
    {
        $log = EquipmentMovementLog::query()
            ->where('action', EquipmentMovementLog::ACTION_RETURNED)
            ->whereKey($logId)
            ->first();

        if (! $log || ! $this->isLatestMovement($log)) {
            return;
        }

        $this->selectedLogId = $log->id;
    }

    public function closeView(): void
    {
        $this->selectedLogId = null;
    }

    public function render()
    {
        $returns = EquipmentMovementLog::query()
            ->with(['equipment.equipmentType', 'office', 'assignment.employee', 'assignment.office'])
            ->where('action', EquipmentMovementLog::ACTION_RETURNED)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('equipment_movement_logs as latest_movement_logs')
                    ->groupBy('equipment_id');
            })
            ->when(trim($this->search) !== '', function ($q) {
                $term = '%'.trim($this->search).'%';
                $q->where(function ($query) use ($term) {
                    $query->where('owner_name', 'like', $term)
                        ->orWhere('remarks', 'like', $term)
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

        $printLog = $this->selectedLogId
            ? EquipmentMovementLog::with(['equipment.equipmentType', 'office', 'assignment.employee', 'assignment.office'])
                ->where('action', EquipmentMovementLog::ACTION_RETURNED)
                ->find($this->selectedLogId)
            : null;

        if ($printLog && ! $this->isLatestMovement($printLog)) {
            $this->selectedLogId = null;
            $printLog = null;
        }

        $equipment = $printLog?->equipment;
        $endUser = $printLog ? $this->endUser($printLog) : null;
        $receivedBy = $this->defaultReceiver();

        return view('livewire.reports.returned-property-page', [
            'returns' => $returns,
            'printLog' => $printLog,
            'equipment' => $equipment,
            'endUser' => $endUser,
            'endUserName' => $endUser?->last_name_first ?: ($printLog?->owner_name ?: ''),
            'receivedBy' => $receivedBy,
            'itemDescription' => $equipment ? $this->itemDescription($equipment) : null,
            'rrspNumber' => $printLog ? $this->rrspNumber($printLog) : null,
            'returnDate' => $printLog?->created_at,
        ]);
    }

    protected function endUser(EquipmentMovementLog $log): ?Employee
    {
        if ($log->assignment?->employee) {
            return $log->assignment->employee;
        }

        return $this->findEmployeeByName($log->owner_name);
    }

    protected function findEmployeeByName(?string $name): ?Employee
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        return Employee::query()
            ->get()
            ->first(function (Employee $employee) use ($name) {
                return strcasecmp($employee->full_name, $name) === 0
                    || strcasecmp($employee->last_name_first, $name) === 0;
            });
    }

    protected function defaultReceiver(): ?Employee
    {
        return Employee::query()
            ->where('last_name', 'La Madrid')
            ->where('first_name', 'Lloyd Neil')
            ->first();
    }

    protected function rrspNumber(EquipmentMovementLog $log): string
    {
        $date = $log->created_at instanceof Carbon ? $log->created_at : Carbon::parse($log->created_at);

        return sprintf('%s-%03d', $date->format('Y-m'), $log->id);
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

    protected function isLatestMovement(EquipmentMovementLog $log): bool
    {
        $latestId = EquipmentMovementLog::query()
            ->where('equipment_id', $log->equipment_id)
            ->max('id');

        return (int) $latestId === (int) $log->id;
    }
}
