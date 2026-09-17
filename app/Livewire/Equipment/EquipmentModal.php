<?php

namespace App\Livewire\Equipment;

use App\Models\Equipment;
use App\Models\EquipmentAssignment;
use App\Models\EquipmentMovementLog;
use App\Models\EquipmentCategory;
use App\Models\EquipmentType;
use App\Models\Employee;
use App\Models\Office;
use App\Models\PpeCategory;
use App\Models\Province;
use App\Models\Region;
use Livewire\Attributes\On;
use Livewire\Component;

class EquipmentModal extends Component
{
    public $show = false;

    public $isEdit = false;

    public $equipmentId;

    public $equipment_category_id;

    public $equipment_type_id;

    public $ppe_category_id;

    public $region_id;

    public $province_id;

    public $office_id;

    public $employee_id;

    public $employee_search = '';

    public $property_no;

    public $uacs_object_code;

    public $tag;

    public $item_id;

    public $ppe_major_account_group;

    public $general_ledger;

    public $series_number;

    public $location_number;

    public $ee_seq;

    public $form_series_number;

    public $form_field;

    public $date_purchased;

    public $date_acquired;

    public $cost;

    public $classification;

    public $fund_source;

    public $estimated_useful_life;

    public $par_ics;

    public $par_ics_issued_date;

    public $date_last_inventory;

    public $status_last_inventory;

    public $serial_no;

    public $brand;

    public $model;

    public $description;

    public $remarks;

    public $status = true;

    protected function rules()
    {
        return [
            'equipment_category_id' => 'required|exists:equipment_categories,id',
            'equipment_type_id' => 'required|exists:equipment_types,id,equipment_category_id,' . $this->equipment_category_id,
            'ppe_category_id' => 'required|exists:ppe_categories,id',
            'region_id' => 'required|exists:regions,id',
            'province_id' => 'required|exists:provinces,id',
            'office_id' => 'required|exists:offices,id',
            'employee_id' => 'nullable|exists:employees,id',
            'property_no' => 'required|string|max:255|unique:equipment,property_no,' . $this->equipmentId,
            'uacs_object_code' => 'nullable|string|max:255',
            'tag' => 'required|string|max:255',
            'item_id' => 'nullable|string|max:255',
            'ppe_major_account_group' => 'required|integer|min:0',
            'general_ledger' => 'required|integer|min:0',
            'series_number' => 'required|integer|min:0',
            'location_number' => 'required|integer|min:0',
            'ee_seq' => 'nullable|string|max:50',
            'form_series_number' => 'nullable|string|max:255',
            'form_field' => 'nullable|string|max:255',
            'date_purchased' => 'required|date',
            'date_acquired' => 'nullable|date',
            'cost' => 'nullable|string|max:255',
            'classification' => 'required|in:ppe,semi_hv,lv',
            'fund_source' => 'nullable|string|max:255',
            'estimated_useful_life' => 'nullable|integer|min:0|max:99',
            'par_ics' => 'nullable|string|max:255',
            'par_ics_issued_date' => 'nullable|date',
            'date_last_inventory' => 'nullable|date',
            'status_last_inventory' => 'nullable|boolean',
            'serial_no' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'status' => 'required|boolean',
        ];
    }

    #[On('openAddEquipment')]
    public function create()
    {
        $this->resetForm();
        $this->status = true;
        $this->region_id = Region::defaultRegionId();
        $this->isEdit = false;
        $this->show = true;
    }

    #[On('openEditEquipment')]
    public function edit($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);

        $this->equipmentId = $equipment->id;
        $this->equipment_category_id = $equipment->equipment_category_id;
        $this->equipment_type_id = $equipment->equipment_type_id;
        $this->ppe_category_id = $equipment->ppe_category_id;
        $this->office_id = $equipment->office_id;
        $this->employee_id = $equipment->activeAssignment?->employee_id;
        $this->property_no = $equipment->property_no;
        $this->uacs_object_code = $equipment->uacs_object_code;
        $this->tag = $equipment->tag;
        $this->item_id = $equipment->item_id;
        $this->ppe_major_account_group = $equipment->ppe_major_account_group;
        $this->general_ledger = $equipment->general_ledger;
        $this->series_number = $equipment->series_number;
        $this->location_number = $equipment->location_number;
        $this->ee_seq = $equipment->ee_seq;
        $this->form_series_number = $equipment->form_series_number;
        $this->form_field = $equipment->form_field;
        $this->date_purchased = $equipment->date_purchased?->format('Y-m-d');
        $this->date_acquired = $equipment->date_acquired?->format('Y-m-d');
        $this->cost = $equipment->cost;
        $this->classification = $equipment->classification ?: Equipment::classifyFromCost($equipment->cost);
        $this->fund_source = $equipment->fund_source;
        $this->estimated_useful_life = $equipment->estimated_useful_life;
        $this->par_ics = $equipment->par_ics;
        $this->par_ics_issued_date = $equipment->par_ics_issued_date?->format('Y-m-d');
        $this->date_last_inventory = $equipment->date_last_inventory?->format('Y-m-d');
        $this->status_last_inventory = $equipment->status_last_inventory === null ? '' : (int) $equipment->status_last_inventory;
        $this->serial_no = $equipment->serial_no;
        $this->brand = $equipment->brand;
        $this->model = $equipment->model;
        $this->description = $equipment->description;
        $this->remarks = $equipment->remarks;
        $this->status = $equipment->status;

        $office = Office::with('province')->find($equipment->office_id);
        $this->province_id = $office?->province_id;
        $this->region_id = $office?->province?->region_id;
        $this->syncLocationNumberFromProvince();
        $this->syncEeSeqFromEmployee();

        $this->isEdit = true;
        $this->show = true;
    }

    public function updatedEquipmentCategoryId()
    {
        $this->equipment_type_id = '';
    }

    public function updatedPpeCategoryId(): void
    {
        $this->fillAccountingFromPpeCategory();
    }

    public function updatedCost(): void
    {
        $classification = Equipment::classifyFromCost($this->cost);

        if ($classification) {
            $this->classification = $classification;
        }
    }

    public function updatedRegionId()
    {
        $this->province_id = '';
        $this->office_id = '';
        $this->location_number = null;
    }

    public function updatedProvinceId()
    {
        $this->office_id = '';
        $this->syncLocationNumberFromProvince();
    }

    public function selectEmployee($employeeId = null): void
    {
        $this->employee_id = $employeeId ?: null;
        $this->employee_search = '';
        $this->syncEeSeqFromEmployee();
    }

    public function save()
    {
        foreach ([
            'uacs_object_code',
            'tag',
            'item_id',
            'ppe_major_account_group',
            'general_ledger',
            'series_number',
            'location_number',
            'ee_seq',
            'form_series_number',
            'form_field',
            'date_purchased',
            'date_acquired',
            'cost',
            'classification',
            'fund_source',
            'estimated_useful_life',
            'par_ics',
            'par_ics_issued_date',
            'date_last_inventory',
            'status_last_inventory',
            'serial_no',
            'brand',
            'model',
            'description',
            'remarks',
        ] as $field) {
            if ($this->{$field} === '') {
                $this->{$field} = null;
            }
        }

        $this->property_no = Equipment::buildPropertyNumber(
            $this->tag,
            $this->date_purchased,
            $this->ppe_major_account_group,
            $this->general_ledger,
            $this->series_number,
            $this->location_number,
        );

        $this->validate();

        $this->classification = Equipment::classifyFromCost($this->cost) ?: $this->classification;

        $attributes = [
            'equipment_category_id' => $this->equipment_category_id,
            'equipment_type_id' => $this->equipment_type_id,
            'ppe_category_id' => $this->ppe_category_id,
            'office_id' => $this->office_id,
            'property_no' => $this->property_no,
            'uacs_object_code' => $this->blankToNull($this->uacs_object_code),
            'tag' => $this->blankToNull($this->tag),
            'item_id' => $this->blankToNull($this->item_id),
            'ppe_major_account_group' => $this->blankToNull($this->ppe_major_account_group),
            'general_ledger' => $this->blankToNull($this->general_ledger),
            'series_number' => $this->blankToNull($this->series_number),
            'location_number' => $this->blankToNull($this->location_number),
            'ee_seq' => $this->blankToNull($this->ee_seq),
            'form_series_number' => $this->blankToNull($this->form_series_number),
            'form_field' => $this->blankToNull($this->form_field),
            'date_purchased' => $this->blankToNull($this->date_purchased),
            'date_acquired' => $this->blankToNull($this->date_acquired),
            'cost' => $this->blankToNull($this->cost),
            'classification' => $this->classification,
            'fund_source' => $this->blankToNull($this->fund_source),
            'estimated_useful_life' => $this->blankToNull($this->estimated_useful_life),
            'par_ics' => $this->blankToNull($this->par_ics),
            'par_ics_issued_date' => $this->blankToNull($this->par_ics_issued_date),
            'date_last_inventory' => $this->blankToNull($this->date_last_inventory),
            'status_last_inventory' => $this->blankToNull($this->status_last_inventory),
            'serial_no' => $this->blankToNull($this->serial_no),
            'brand' => $this->blankToNull($this->brand),
            'model' => $this->blankToNull($this->model),
            'description' => $this->blankToNull($this->description),
            'remarks' => $this->blankToNull($this->remarks),
            'status' => (bool) $this->status,
        ];

        if ($this->isEdit) {
            $equipment = Equipment::findOrFail($this->equipmentId);
            $previousOfficeId = $equipment->office_id;
            $equipment->update($attributes);
        } else {
            $equipment = Equipment::create($attributes);
            $previousOfficeId = null;
        }

        if (! $this->isEdit) {
            EquipmentMovementLog::record([
                'equipment_id' => $equipment->id,
                'action' => EquipmentMovementLog::ACTION_CREATED,
                'owner_name' => $this->assignedEmployeeName(),
                'office_id' => $equipment->office_id,
                'remarks' => 'Equipment registered in inventory.',
            ]);
        } elseif ($previousOfficeId && (int) $previousOfficeId !== (int) $equipment->office_id) {
            EquipmentMovementLog::record([
                'equipment_id' => $equipment->id,
                'action' => EquipmentMovementLog::ACTION_TRANSFERRED,
                'office_id' => $equipment->office_id,
                'previous_office_id' => $previousOfficeId,
                'remarks' => 'Equipment transferred to a different office.',
            ]);
        } else {
            EquipmentMovementLog::record([
                'equipment_id' => $equipment->id,
                'action' => EquipmentMovementLog::ACTION_UPDATED,
                'owner_name' => $this->assignedEmployeeName(),
                'office_id' => $equipment->office_id,
                'remarks' => 'Equipment details updated.',
            ]);
        }

        $this->handleEmployeeAssignment($equipment);

        $message = $this->isEdit ? 'Equipment updated successfully.' : 'Equipment added successfully.';

        $this->dispatch('equipmentSaved', message: $message);
        $this->closeModal();
    }

    protected function blankToNull(mixed $value): mixed
    {
        return $value === '' || $value === null ? null : $value;
    }

    protected function handleEmployeeAssignment(Equipment $equipment): void
    {
        $activeAssignment = $equipment->activeAssignment;

        if ($activeAssignment && (int) $activeAssignment->office_id !== (int) $equipment->office_id) {
            $activeAssignment->update(['office_id' => $equipment->office_id]);
        }

        $activeAssignment?->loadMissing('employee');

        if (! $this->employee_id) {
            return;
        }

        if ($activeAssignment && (int) $activeAssignment->employee_id === (int) $this->employee_id) {
            return;
        }

        if ($activeAssignment) {
            $activeAssignment->update(['date_returned' => now()->toDateString()]);
            $previousOwnerName = $activeAssignment->employee?->full_name;

            EquipmentMovementLog::record([
                'equipment_id' => $equipment->id,
                'equipment_assignment_id' => $activeAssignment->id,
                'action' => EquipmentMovementLog::ACTION_RETURNED,
                'owner_name' => $previousOwnerName,
                'office_id' => $equipment->office_id,
                'remarks' => 'Equipment returned before reassignment.',
            ]);
        }

        $assignment = EquipmentAssignment::create([
            'equipment_id' => $equipment->id,
            'office_id' => $equipment->office_id,
            'employee_id' => $this->employee_id,
            'date_assigned' => now()->toDateString(),
        ]);

        $employeeName = $this->assignedEmployeeName();

        EquipmentMovementLog::record([
            'equipment_id' => $equipment->id,
            'equipment_assignment_id' => $assignment->id,
            'action' => EquipmentMovementLog::ACTION_ASSIGNED,
            'owner_name' => $employeeName,
            'office_id' => $equipment->office_id,
            'remarks' => $employeeName
                ? 'Equipment assigned to '.$employeeName.'.'
                : 'Equipment assigned to employee.',
        ]);
    }

    protected function assignedEmployeeName(): ?string
    {
        if (! $this->employee_id) {
            return null;
        }

        return Employee::query()
            ->whereKey($this->employee_id)
            ->first()
            ?->full_name;
    }

    public function closeModal()
    {
        $this->show = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'equipmentId',
            'equipment_category_id',
            'equipment_type_id',
            'ppe_category_id',
            'region_id',
            'province_id',
            'office_id',
            'employee_id',
            'employee_search',
            'property_no',
            'uacs_object_code',
            'tag',
            'item_id',
            'ppe_major_account_group',
            'general_ledger',
            'series_number',
            'location_number',
            'ee_seq',
            'form_series_number',
            'form_field',
            'date_purchased',
            'date_acquired',
            'cost',
            'classification',
            'fund_source',
            'estimated_useful_life',
            'par_ics',
            'par_ics_issued_date',
            'date_last_inventory',
            'status_last_inventory',
            'serial_no',
            'brand',
            'model',
            'description',
            'remarks',
            'isEdit',
        ]);

        $this->status = true;
        $this->resetValidation();
    }

    protected function fillAccountingFromPpeCategory(): void
    {
        $category = $this->ppe_category_id
            ? PpeCategory::find($this->ppe_category_id)
            : null;

        if (! $category) {
            $this->uacs_object_code = null;
            $this->ppe_major_account_group = null;
            $this->general_ledger = null;

            return;
        }

        $this->uacs_object_code = $category->uacs_object_code;
        $this->ppe_major_account_group = (int) $category->ppe_sub_major_account_group;
        $this->general_ledger = (int) $category->general_ledger_account;
    }

    protected function syncLocationNumberFromProvince(): void
    {
        $this->location_number = $this->province_id ?: null;
    }

    protected function syncEeSeqFromEmployee(): void
    {
        if (! $this->employee_id) {
            $this->ee_seq = null;

            return;
        }

        $this->ee_seq = Employee::query()
            ->whereKey($this->employee_id)
            ->value('ee_sequence_number');
    }

    public function render()
    {
        return view('livewire.equipment.equipment-modal', [
            'equipmentCategories' => EquipmentCategory::where('status', 1)->orderBy('title')->get(),
            'equipmentTypes' => $this->equipment_category_id
                ? EquipmentType::where('status', 1)
                    ->where('equipment_category_id', $this->equipment_category_id)
                    ->orderBy('name')
                    ->get()
                : collect(),
            'ppeCategories' => PpeCategory::where('status', 1)
                ->orderByRaw('CAST(number AS INTEGER)')
                ->orderBy('number')
                ->get(),
            'regions' => Region::orderBy('region_short_name')->get(),
            'provinces' => $this->region_id
                ? Province::where('region_id', $this->region_id)->orderBy('province_name')->get()
                : collect(),
            'offices' => $this->province_id
                ? Office::where('province_id', $this->province_id)->orderBy('office_name')->get()
                : collect(),
            'employees' => Employee::query()
                ->where('status', 1)
                ->when($this->employee_search, function ($q) {
                    $search = trim($this->employee_search);
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
            'selectedEmployee' => $this->employee_id
                ? Employee::find($this->employee_id)
                : null,
            'generatedPropertyNo' => Equipment::buildPropertyNumber(
                $this->tag,
                $this->date_purchased,
                $this->ppe_major_account_group,
                $this->general_ledger,
                $this->series_number,
                $this->location_number,
            ),
        ]);
    }
}
