<?php

namespace App\Livewire\Equipment;

use App\Models\Employee;
use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\EquipmentMovementLog;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class EquipmentShowPage extends Component
{
    use WithPagination;

    public Equipment $equipment;

    public $expandedPmId;

    public $showTransferModal = false;

    public $showReturnModal = false;

    public $return_reason = '';

    public $transfer_employee_id;

    public $transfer_employee_search = '';

    public $transfer_reason = '';

    public $transfer_type = 'reassignment';

    public $transfer_type_other = '';

    public function mount(Equipment $equipment)
    {
        $this->equipment = $this->loadEquipmentRelations($equipment);
    }

    #[On('equipmentSaved')]
    public function refreshEquipment(?string $message = null)
    {
        $this->equipment = $this->loadEquipmentRelations($this->equipment->fresh());
        $this->expandedPmId = null;

        if ($message) {
            session()->flash('message', $message);
        }
    }

    public function openEdit()
    {
        $this->dispatch('openEditEquipment', equipmentId: $this->equipment->id)->to(EquipmentModal::class);
    }

    public function openReturn(): void
    {
        $this->equipment->refresh();
        $this->equipment->load('activeAssignment.employee');

        if (! $this->equipment->activeAssignment) {
            session()->flash('error', 'This equipment is not currently assigned.');

            return;
        }

        $this->return_reason = '';
        $this->resetValidation();
        $this->showReturnModal = true;
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->return_reason = '';
        $this->resetValidation();
    }

    public function returnEquipment(): void
    {
        $this->validate([
            'return_reason' => 'required|string|max:500',
        ], [
            'return_reason.required' => 'Enter a reason for return.',
        ]);

        $this->equipment->refresh();
        $this->equipment->load('activeAssignment.employee');

        $assignment = $this->equipment->activeAssignment;

        if (! $assignment || ! $assignment->isActive()) {
            $this->closeReturnModal();
            session()->flash('error', 'This equipment is not currently assigned.');

            return;
        }

        $employeeName = $assignment->employee?->full_name ?? 'employee';

        DB::transaction(function () use ($assignment, $employeeName) {
            $assignment->update([
                'date_returned' => now()->toDateString(),
            ]);

            EquipmentMovementLog::record([
                'equipment_id' => $this->equipment->id,
                'equipment_assignment_id' => $assignment->id,
                'action' => EquipmentMovementLog::ACTION_RETURNED,
                'owner_name' => $employeeName,
                'office_id' => $assignment->office_id,
                'remarks' => trim($this->return_reason),
            ]);
        });

        $this->closeReturnModal();
        $this->resetPage('assignmentsPage');
        $this->resetPage('logsPage');
        $this->refreshEquipment('Equipment returned from '.$employeeName.'.');
    }

    public function openTransfer(): void
    {
        $this->resetTransferForm();
        $this->showTransferModal = true;
    }

    public function closeTransferModal(): void
    {
        $this->showTransferModal = false;
        $this->resetTransferForm();
    }

    public function selectTransferEmployee($employeeId = null): void
    {
        $this->transfer_employee_id = $employeeId ?: null;
        $this->transfer_employee_search = '';
    }

    public function transfer(): void
    {
        $this->validate([
            'transfer_employee_id' => 'required|exists:employees,id',
            'transfer_type' => 'required|in:donation,relocate,reassignment,others',
            'transfer_type_other' => 'required_if:transfer_type,others|nullable|string|max:100',
            'transfer_reason' => 'nullable|string|max:500',
        ], [
            'transfer_employee_id.required' => 'Select an employee to transfer this equipment to.',
            'transfer_type.required' => 'Select a transfer type.',
            'transfer_type_other.required_if' => 'Specify the transfer type.',
        ]);

        $this->equipment->refresh();
        $this->equipment->load('activeAssignment.employee');

        $currentAssignment = $this->equipment->activeAssignment;
        $newEmployee = Employee::findOrFail($this->transfer_employee_id);

        if ($currentAssignment && (int) $currentAssignment->employee_id === (int) $newEmployee->id) {
            $this->addError('transfer_employee_id', 'This employee already has this equipment.');

            return;
        }

        $previousOwnerName = $currentAssignment?->employee?->full_name;

        DB::transaction(function () use ($currentAssignment, $newEmployee, $previousOwnerName) {
            if ($currentAssignment) {
                $currentAssignment->update(['date_returned' => now()->toDateString()]);
            }

            $assignment = EquipmentAssignment::create([
                'equipment_id' => $this->equipment->id,
                'office_id' => $this->equipment->office_id,
                'employee_id' => $newEmployee->id,
                'date_assigned' => now()->toDateString(),
            ]);

            EquipmentMovementLog::record([
                'equipment_id' => $this->equipment->id,
                'equipment_assignment_id' => $assignment->id,
                'action' => EquipmentMovementLog::ACTION_TRANSFERRED,
                'transfer_type' => $this->transfer_type,
                'transfer_type_other' => $this->transfer_type === EquipmentMovementLog::TRANSFER_OTHERS
                    ? trim($this->transfer_type_other)
                    : null,
                'owner_name' => $newEmployee->full_name,
                'previous_owner_name' => $previousOwnerName,
                'office_id' => $this->equipment->office_id,
                'remarks' => filled($this->transfer_reason) ? trim($this->transfer_reason) : null,
            ]);
        });

        $this->closeTransferModal();
        $this->resetPage('assignmentsPage');
        $this->resetPage('logsPage');
        $this->refreshEquipment('Equipment transferred to '.$newEmployee->full_name.' successfully.');
    }

    protected function resetTransferForm(): void
    {
        $this->reset([
            'transfer_employee_id',
            'transfer_employee_search',
            'transfer_reason',
            'transfer_type_other',
        ]);
        $this->transfer_type = 'reassignment';
        $this->resetValidation();
    }

    public function togglePmDetails($pmId)
    {
        $pmId = (int) $pmId;
        $this->expandedPmId = $this->expandedPmId === $pmId ? null : $pmId;
    }

    protected function loadEquipmentRelations(Equipment $equipment): Equipment
    {
        return $equipment->load([
            'equipmentCategory',
            'equipmentType',
            'ppeCategory',
            'office.province.region',
            'activeAssignment.employee',
            'preventiveMaintenances',
        ]);
    }

    public function render()
    {
        $currentEmployeeId = $this->equipment->activeAssignment?->employee_id;

        return view('livewire.equipment.equipment-show-page', [
            'assignments' => EquipmentAssignment::query()
                ->with(['office', 'employee'])
                ->where('equipment_id', $this->equipment->id)
                ->latest('date_assigned')
                ->paginate(3, pageName: 'assignmentsPage'),
            'movementLogs' => EquipmentMovementLog::query()
                ->with(['office', 'previousOffice', 'assignment.employee'])
                ->where('equipment_id', $this->equipment->id)
                ->latest()
                ->paginate(3, pageName: 'logsPage'),
            'employees' => Employee::query()
                ->where('status', 1)
                ->when($currentEmployeeId, fn ($q) => $q->where('id', '!=', $currentEmployeeId))
                ->when($this->transfer_employee_search, function ($q) {
                    $search = trim($this->transfer_employee_search);
                    $q->where(function ($query) use ($search) {
                        $query->where('last_name', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('ee_sequence_number', 'like', "%{$search}%");
                    });
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->limit(50)
                ->get(),
            'selectedTransferEmployee' => $this->transfer_employee_id
                ? Employee::find($this->transfer_employee_id)
                : null,
        ]);
    }
}
