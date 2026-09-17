<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        return view('employees.index', [
            'employees' => Employee::with('office')
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('ee_sequence_number', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
                })
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->paginate(10)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('employees.create', [
            'offices' => Office::where('status', 1)
                ->orderBy('office_name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ee_sequence_number' => 'required|string|max:50|unique:employees,ee_sequence_number',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'office_id' => 'nullable|exists:offices,id',
            'status' => 'required|boolean',
        ]);

        Employee::create($request->only([
            'ee_sequence_number',
            'first_name',
            'middle_name',
            'last_name',
            'position',
            'office_id',
            'status',
        ]));

        return redirect()->route('employees.index')->with('message', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', [
            'employee' => $employee,
            'offices' => Office::where('status', 1)
                ->orderBy('office_name')
                ->get(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'ee_sequence_number' => 'required|string|max:50|unique:employees,ee_sequence_number,' . $employee->id,
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'office_id' => 'nullable|exists:offices,id',
            'status' => 'required|boolean',
        ]);

        $employee->update($request->only([
            'ee_sequence_number',
            'first_name',
            'middle_name',
            'last_name',
            'position',
            'office_id',
            'status',
        ]));

        return redirect()->route('employees.index')->with('message', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('message', 'Employee deleted.');
    }
}
