<?php

namespace App\Livewire;

use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\EquipmentMovementLog;
use App\Models\Employee;
use App\Models\Office;
use App\Models\Province;
use App\Models\Region;
use Livewire\Component;
use Livewire\WithPagination;

class EquipmentAssignmentsPage extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;

    public $showReturnModal = false;

    public $returningAssignmentId;

    public $equipment_id;

    public $region_id;

    public $province_id;

    public $office_id;

    public $employee_id;

    public $date_assigned;

    public $remarks;

    public $employees;

    public function mount()
    {
        $this->date_assigned = now()->format('Y-m-d');
        $this->employees = Employee::where('status', 1)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
    }

    public function updatedRegionId()
    {
        $this->province_id = '';
        $this->office_id = '';
        $this->equipment_id = '';
    }

    public function updatedProvinceId()
    {
        $this->office_id = '';
        $this->equipment_id = '';
    }

    public function updatedOfficeId()
    {
        $this->equipment_id = '';
    }

    public function openAssignModal()
    {
        $this->resetForm();
        $this->date_assigned = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openReturnModal($assignmentId): void
    {
        $assignment = EquipmentAssignment::with(['equipment.equipmentType', 'employee', 'office'])
            ->findOrFail($assignmentId);

        if (! $assignment->isActive()) {
            session()->flash('error', 'This assignment has already been returned.');

            return;
        }

        $this->returningAssignmentId = $assignment->id;
        $this->showReturnModal = true;
    }

    public function closeReturnModal(): void
    {
        $this->showReturnModal = false;
        $this->returningAssignmentId = null;
    }

    public function assign()
    {
        $this->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'office_id' => 'required|exists:offices,id',
            'employee_id' => 'required|exists:employees,id',
            'date_assigned' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        $equipment = Equipment::with('activeAssignment')->findOrFail($this->equipment_id);

        if ($equipment->activeAssignment) {
            $this->addError('equipment_id', 'This equipment is already assigned. Return it before assigning to someone else.');

            return;
        }

        $assignment = EquipmentAssignment::create([
            'equipment_id' => $equipment->id,
            'office_id' => $this->office_id,
            'employee_id' => $this->employee_id,
            'date_assigned' => $this->date_assigned,
        ]);

        $employee = Employee::findOrFail($this->employee_id);

        EquipmentMovementLog::record([
            'equipment_id' => $equipment->id,
            'equipment_assignment_id' => $assignment->id,
            'action' => EquipmentMovementLog::ACTION_ASSIGNED,
            'owner_name' => $employee->full_name,
            'office_id' => $this->office_id,
            'remarks' => $this->remarks ?: 'Equipment assigned to employee.',
        ]);

        $employeeName = $employee->full_name;
        $this->closeModal();
        session()->flash('message', 'Equipment assigned to ' . $employeeName . ' successfully.');
    }

    public function returnEquipment()
    {
        if (! $this->returningAssignmentId) {
            return;
        }

        $assignment = EquipmentAssignment::with(['equipment', 'employee'])->findOrFail($this->returningAssignmentId);

        if (! $assignment->isActive()) {
            $this->closeReturnModal();
            session()->flash('error', 'This assignment has already been returned.');

            return;
        }

        $assignment->update([
            'date_returned' => now()->toDateString(),
        ]);

        $employeeName = $assignment->employee?->full_name ?? 'employee';

        EquipmentMovementLog::record([
            'equipment_id' => $assignment->equipment_id,
            'equipment_assignment_id' => $assignment->id,
            'action' => EquipmentMovementLog::ACTION_RETURNED,
            'owner_name' => $employeeName,
            'office_id' => $assignment->office_id,
            'remarks' => 'Equipment returned by ' . $employeeName . '.',
        ]);

        $this->closeReturnModal();
        session()->flash('message', 'Equipment returned from ' . $employeeName . '.');
    }

    public function resetForm()
    {
        $this->reset([
            'equipment_id',
            'region_id',
            'province_id',
            'office_id',
            'employee_id',
            'remarks',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        $regions = Region::orderBy('region_short_name')->get();

        $provinces = Province::when($this->region_id, fn ($q) => $q->where('region_id', $this->region_id))
            ->orderBy('province_name')
            ->get();

        $offices = Office::when($this->province_id, fn ($q) => $q->where('province_id', $this->province_id))
            ->orderBy('office_name')
            ->get();

        $availableEquipment = Equipment::with(['equipmentType', 'office'])
            ->whereDoesntHave('assignments', fn ($q) => $q->whereNull('date_returned'))
            ->when($this->office_id, fn ($q) => $q->where('office_id', $this->office_id))
            ->where('status', true)
            ->orderBy('property_no')
            ->get();

        $activeAssignments = EquipmentAssignment::with(['equipment.equipmentType', 'office', 'employee'])
            ->active()
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->whereHas('employee', function ($q) {
                        $q->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('middle_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    })
                    ->orWhereHas('equipment', fn ($d) => $d->where('property_no', 'like', "%{$this->search}%"));
                });
            })
            ->latest('date_assigned')
            ->paginate(10, pageName: 'assignmentsPage');

        $movementLogs = EquipmentMovementLog::with(['equipment', 'office', 'previousOffice'])
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('owner_name', 'like', "%{$this->search}%")
                        ->orWhere('remarks', 'like', "%{$this->search}%")
                        ->orWhereHas('equipment', fn ($d) => $d->where('property_no', 'like', "%{$this->search}%"));
                });
            })
            ->latest()
            ->paginate(10, pageName: 'logsPage');

        return view('livewire.equipment-assignments-page', [
            'regions' => $regions,
            'provinces' => $provinces,
            'offices' => $offices,
            'availableEquipment' => $availableEquipment,
            'activeAssignments' => $activeAssignments,
            'movementLogs' => $movementLogs,
            'returningAssignment' => $this->returningAssignmentId
                ? EquipmentAssignment::with(['equipment.equipmentType', 'employee', 'office'])->find($this->returningAssignmentId)
                : null,
        ]);
    }
}
