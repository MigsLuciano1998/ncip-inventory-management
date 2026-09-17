<?php

namespace App\Livewire;

use App\Models\Employee;
use App\Models\Office;
use Livewire\Component;
use Livewire\WithPagination;

class EmployeesPage extends Component
{
    use WithPagination;

    public $search = '';
    public $ee_sequence_number;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $position;
    public $office_id;
    public $status = true;
    public $employeeId;
    public $isEdit = false;
    public $showModal = false;
    public $offices;

    public function mount()
    {
        $this->offices = Office::where('status', 1)
            ->orderBy('office_name')
            ->get();
    }

    public function save()
    {
        $this->validate([
            'ee_sequence_number' => 'required|string|max:50|unique:employees,ee_sequence_number,' . $this->employeeId,
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'office_id' => 'nullable|exists:offices,id',
            'status' => 'boolean',
        ]);

        Employee::updateOrCreate(
            ['id' => $this->employeeId],
            [
                'ee_sequence_number' => $this->ee_sequence_number,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'position' => $this->position,
                'office_id' => $this->office_id,
                'status' => $this->status,
            ]
        );

        $this->resetForm();
        $this->showModal = false;

        session()->flash('message', 'Employee saved successfully.');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);

        $this->employeeId = $employee->id;
        $this->ee_sequence_number = $employee->ee_sequence_number;
        $this->first_name = $employee->first_name;
        $this->middle_name = $employee->middle_name;
        $this->last_name = $employee->last_name;
        $this->position = $employee->position;
        $this->office_id = $employee->office_id;
        $this->status = $employee->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function delete($id)
    {
        Employee::findOrFail($id)->delete();

        session()->flash('message', 'Employee deleted.');
    }

    public function resetForm()
    {
        $this->reset([
            'ee_sequence_number',
            'first_name',
            'middle_name',
            'last_name',
            'position',
            'office_id',
            'employeeId',
            'isEdit',
        ]);

        $this->status = true;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.employees-page', [
            'employees' => Employee::with('office')
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('ee_sequence_number', 'like', "%{$this->search}%")
                            ->orWhere('first_name', 'like', "%{$this->search}%")
                            ->orWhere('middle_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%");
                    });
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate(10),
        ]);
    }
}
