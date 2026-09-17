<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\PreventiveMaintenance;
use Illuminate\Http\Request;

class PreventiveMaintenanceController extends Controller
{
    public function index()
    {
        return view('preventive-maintenances.index');
    }

    public function create(Request $request)
    {
        $selectedEquipment = null;

        if ($request->filled('equipment_id')) {
            $selectedEquipment = Equipment::with('equipmentType', 'office.province.region')->find($request->equipment_id);
        }

        return view('preventive-maintenances.create', [
            'selectedEquipment' => $selectedEquipment,
            'equipments' => Equipment::with('equipmentType', 'office')
                ->orderBy('property_no')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'date_inspected' => 'required|date',
            'status' => 'required|in:Serviceable,Unserviceable',
            'unserviceable_reason' => 'required_if:status,Unserviceable|nullable|string|max:1000',
            'windows_update' => 'sometimes|boolean',
            'windows_update_remarks' => 'nullable|string|max:500',
            'remove_unnecessary_apps' => 'sometimes|boolean',
            'remove_unnecessary_apps_remarks' => 'nullable|string|max:500',
            'health_check_diagnosis' => 'sometimes|boolean',
            'health_check_diagnosis_remarks' => 'nullable|string|max:500',
            'virus_scan' => 'sometimes|boolean',
            'virus_scan_remarks' => 'nullable|string|max:500',
            'physical_inspection' => 'sometimes|boolean',
            'physical_inspection_remarks' => 'nullable|string|max:500',
            'cdp' => 'sometimes|boolean',
            'cdp_remarks' => 'nullable|string|max:500',
            'overall_remarks' => 'nullable|string|max:2000',
        ]);

        $validated = array_merge($validated, [
            'windows_update' => $request->boolean('windows_update'),
            'remove_unnecessary_apps' => $request->boolean('remove_unnecessary_apps'),
            'health_check_diagnosis' => $request->boolean('health_check_diagnosis'),
            'virus_scan' => $request->boolean('virus_scan'),
            'physical_inspection' => $request->boolean('physical_inspection'),
            'cdp' => $request->boolean('cdp'),
        ]);

        PreventiveMaintenance::create($validated);

        return redirect()->route('preventive-maintenances.index')->with('message', 'PM log recorded successfully.');
    }

    public function edit(PreventiveMaintenance $preventiveMaintenance)
    {
        return view('preventive-maintenances.edit', [
            'preventiveMaintenance' => $preventiveMaintenance->load('equipment.equipmentType', 'equipment.office'),
            'selectedEquipment' => $preventiveMaintenance->equipment,
            'equipments' => Equipment::with('equipmentType', 'office')
                ->orderBy('property_no')
                ->get(),
        ]);
    }

    public function update(Request $request, PreventiveMaintenance $preventiveMaintenance)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'date_inspected' => 'required|date',
            'status' => 'required|in:Serviceable,Unserviceable',
            'unserviceable_reason' => 'required_if:status,Unserviceable|nullable|string|max:1000',
            'windows_update' => 'sometimes|boolean',
            'windows_update_remarks' => 'nullable|string|max:500',
            'remove_unnecessary_apps' => 'sometimes|boolean',
            'remove_unnecessary_apps_remarks' => 'nullable|string|max:500',
            'health_check_diagnosis' => 'sometimes|boolean',
            'health_check_diagnosis_remarks' => 'nullable|string|max:500',
            'virus_scan' => 'sometimes|boolean',
            'virus_scan_remarks' => 'nullable|string|max:500',
            'physical_inspection' => 'sometimes|boolean',
            'physical_inspection_remarks' => 'nullable|string|max:500',
            'cdp' => 'sometimes|boolean',
            'cdp_remarks' => 'nullable|string|max:500',
            'overall_remarks' => 'nullable|string|max:2000',
        ]);

        $validated = array_merge($validated, [
            'windows_update' => $request->boolean('windows_update'),
            'remove_unnecessary_apps' => $request->boolean('remove_unnecessary_apps'),
            'health_check_diagnosis' => $request->boolean('health_check_diagnosis'),
            'virus_scan' => $request->boolean('virus_scan'),
            'physical_inspection' => $request->boolean('physical_inspection'),
            'cdp' => $request->boolean('cdp'),
        ]);

        $preventiveMaintenance->update($validated);

        return redirect()->route('preventive-maintenances.index')->with('message', 'PM log updated successfully.');
    }
}
