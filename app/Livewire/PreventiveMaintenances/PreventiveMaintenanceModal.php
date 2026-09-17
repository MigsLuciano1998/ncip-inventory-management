<?php

namespace App\Livewire\PreventiveMaintenances;

use App\Models\Equipment;
use App\Models\Office;
use App\Models\PreventiveMaintenance;
use App\Models\Province;
use App\Models\Region;
use Livewire\Attributes\On;
use Livewire\Component;

class PreventiveMaintenanceModal extends Component
{
    public $show = false;

    public $isEdit = false;

    public $pmId;

    public $equipment_id;

    public $region_id;

    public $province_id;

    public $office_id;

    public $date_inspected;

    public $status = PreventiveMaintenance::STATUS_SERVICEABLE;

    public $unserviceable_reason;

    public $windows_update = false;

    public $windows_update_remarks;

    public $remove_unnecessary_apps = false;

    public $remove_unnecessary_apps_remarks;

    public $virus_scan = false;

    public $virus_scan_remarks;

    public $physical_inspection = false;

    public $physical_inspection_remarks;

    public $cdp = false;

    public $cdp_remarks;

    public $overall_remarks;

    public $equipmentLocked = false;

    protected function rules()
    {
        return [
            'equipment_id' => 'required|exists:equipment,id',
            'date_inspected' => 'required|date',
            'status' => 'required|in:Serviceable,Unserviceable',
            'unserviceable_reason' => 'required_if:status,Unserviceable|nullable|string|max:1000',
            'windows_update' => 'boolean',
            'windows_update_remarks' => 'nullable|string|max:500',
            'remove_unnecessary_apps' => 'boolean',
            'remove_unnecessary_apps_remarks' => 'nullable|string|max:500',
            'virus_scan' => 'boolean',
            'virus_scan_remarks' => 'nullable|string|max:500',
            'physical_inspection' => 'boolean',
            'physical_inspection_remarks' => 'nullable|string|max:500',
            'cdp' => 'boolean',
            'cdp_remarks' => 'nullable|string|max:500',
            'overall_remarks' => 'nullable|string|max:2000',
        ];
    }

    #[On('openAddPm')]
    public function create()
    {
        $this->resetForm();
        $this->date_inspected = now()->format('Y-m-d');
        $this->status = PreventiveMaintenance::STATUS_SERVICEABLE;
        $this->isEdit = false;
        $this->equipmentLocked = false;
        $this->show = true;
    }

    #[On('openPmForEquipment')]
    public function createForEquipment($equipmentId)
    {
        $equipment = Equipment::with('office.province')->findOrFail($equipmentId);

        $this->resetForm();
        $this->equipment_id = $equipment->id;
        $this->office_id = $equipment->office_id;
        $this->province_id = $equipment->office?->province_id;
        $this->region_id = $equipment->office?->province?->region_id;
        $this->date_inspected = now()->format('Y-m-d');
        $this->status = PreventiveMaintenance::STATUS_SERVICEABLE;
        $this->isEdit = false;
        $this->equipmentLocked = true;
        $this->show = true;
    }

    #[On('openEditPm')]
    public function edit($pmId)
    {
        $pm = PreventiveMaintenance::with('equipment.office.province')->findOrFail($pmId);

        $this->pmId = $pm->id;
        $this->equipment_id = $pm->equipment_id;
        $this->date_inspected = $pm->date_inspected->format('Y-m-d');
        $this->status = $pm->status;
        $this->unserviceable_reason = $pm->unserviceable_reason;
        $this->windows_update = $pm->windows_update;
        $this->windows_update_remarks = $pm->windows_update_remarks;
        $this->remove_unnecessary_apps = $pm->remove_unnecessary_apps;
        $this->remove_unnecessary_apps_remarks = $pm->remove_unnecessary_apps_remarks;
        $this->virus_scan = $pm->virus_scan;
        $this->virus_scan_remarks = $pm->virus_scan_remarks;
        $this->physical_inspection = $pm->physical_inspection;
        $this->physical_inspection_remarks = $pm->physical_inspection_remarks;
        $this->cdp = $pm->cdp;
        $this->cdp_remarks = $pm->cdp_remarks;
        $this->overall_remarks = $pm->overall_remarks;

        $office = $pm->equipment?->office;
        $this->office_id = $office?->id;
        $this->province_id = $office?->province_id;
        $this->region_id = $office?->province?->region_id;

        $this->isEdit = true;
        $this->equipmentLocked = false;
        $this->show = true;
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

    public function updatedStatus()
    {
        if ($this->status === PreventiveMaintenance::STATUS_SERVICEABLE) {
            $this->unserviceable_reason = null;
        }
    }

    public function save()
    {
        $this->validate();

        $attributes = [
            'equipment_id' => $this->equipment_id,
            'date_inspected' => $this->date_inspected,
            'status' => $this->status,
            'unserviceable_reason' => $this->status === PreventiveMaintenance::STATUS_UNSERVICEABLE
                ? $this->unserviceable_reason
                : null,
            'windows_update' => (bool) $this->windows_update,
            'windows_update_remarks' => $this->windows_update_remarks,
            'remove_unnecessary_apps' => (bool) $this->remove_unnecessary_apps,
            'remove_unnecessary_apps_remarks' => $this->remove_unnecessary_apps_remarks,
            'virus_scan' => (bool) $this->virus_scan,
            'virus_scan_remarks' => $this->virus_scan_remarks,
            'physical_inspection' => (bool) $this->physical_inspection,
            'physical_inspection_remarks' => $this->physical_inspection_remarks,
            'cdp' => (bool) $this->cdp,
            'cdp_remarks' => $this->cdp_remarks,
            'overall_remarks' => $this->overall_remarks,
        ];

        if ($this->isEdit) {
            PreventiveMaintenance::findOrFail($this->pmId)->update($attributes);
            $message = 'PM log updated successfully.';
        } else {
            PreventiveMaintenance::create($attributes);
            $message = 'PM log recorded successfully.';
        }

        $this->dispatch('pmSaved', message: $message);
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->show = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'pmId',
            'equipment_id',
            'region_id',
            'province_id',
            'office_id',
            'date_inspected',
            'unserviceable_reason',
            'windows_update_remarks',
            'remove_unnecessary_apps_remarks',
            'virus_scan_remarks',
            'physical_inspection_remarks',
            'cdp_remarks',
            'overall_remarks',
            'isEdit',
            'equipmentLocked',
        ]);

        $this->status = PreventiveMaintenance::STATUS_SERVICEABLE;
        $this->equipmentLocked = false;
        $this->windows_update = false;
        $this->remove_unnecessary_apps = false;
        $this->virus_scan = false;
        $this->physical_inspection = false;
        $this->cdp = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.preventive-maintenances.preventive-maintenance-modal', [
            'regions' => Region::orderBy('region_short_name')->get(),
            'provinces' => $this->region_id
                ? Province::where('region_id', $this->region_id)->orderBy('province_name')->get()
                : collect(),
            'offices' => $this->province_id
                ? Office::where('province_id', $this->province_id)->orderBy('office_name')->get()
                : collect(),
            'equipments' => $this->office_id
                ? Equipment::with('equipmentType')
                    ->where('office_id', $this->office_id)
                    ->orderBy('property_no')
                    ->get()
                : collect(),
            'selectedEquipment' => $this->equipment_id
                ? Equipment::with(['equipmentType', 'office.province.region'])->find($this->equipment_id)
                : null,
        ]);
    }
}
