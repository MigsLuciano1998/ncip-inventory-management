@php
    use App\Models\PreventiveMaintenance;

    $preventiveMaintenance = $preventiveMaintenance ?? null;
    $status = old(
        'status',
        isset($preventiveMaintenance)
            ? $preventiveMaintenance->status
            : PreventiveMaintenance::STATUS_SERVICEABLE
    );

    $equipmentId = old(
        'equipment_id',
        isset($preventiveMaintenance)
            ? $preventiveMaintenance->equipment_id
            : ($selectedEquipment->id ?? '')
    );
@endphp

<div class="space-y-6">
    @if($selectedEquipment)
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Selected Equipment</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedEquipment->property_no }} — {{ $selectedEquipment->equipmentType?->name ?? 'Unknown' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Office</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $selectedEquipment->office?->office_name ?? '—' }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="pmms-card p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Equipment <span class="text-red-500">*</span></label>
                <select name="equipment_id" class="pmms-select">
                    <option value="">Select equipment</option>
                    @foreach($equipments as $equipment)
                        <option value="{{ $equipment->id }}" {{ $equipmentId == $equipment->id ? 'selected' : '' }}>
                            {{ $equipment->property_no }} — {{ $equipment->equipmentType?->name ?? 'Equipment' }} @if($equipment->office), {{ $equipment->office->office_name }}@endif
                        </option>
                    @endforeach
                </select>
                @error('equipment_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Date Inspected <span class="text-red-500">*</span></label>
                <input type="date" name="date_inspected" value="{{ old('date_inspected', isset($preventiveMaintenance) && $preventiveMaintenance->date_inspected ? $preventiveMaintenance->date_inspected->format('Y-m-d') : now()->format('Y-m-d')) }}" class="pmms-input w-full">
                @error('date_inspected') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Overall Status <span class="text-red-500">*</span></label>
                <select name="status" class="pmms-select">
                    <option value="{{ PreventiveMaintenance::STATUS_SERVICEABLE }}" {{ $status === PreventiveMaintenance::STATUS_SERVICEABLE ? 'selected' : '' }}>Serviceable</option>
                    <option value="{{ PreventiveMaintenance::STATUS_UNSERVICEABLE }}" {{ $status === PreventiveMaintenance::STATUS_UNSERVICEABLE ? 'selected' : '' }}>Unserviceable</option>
                </select>
                @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @if($status === PreventiveMaintenance::STATUS_UNSERVICEABLE)
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Unserviceable Reason <span class="text-red-500">*</span></label>
                    <textarea name="unserviceable_reason" rows="2" class="pmms-input w-full">{{ old('unserviceable_reason', optional($preventiveMaintenance)->unserviceable_reason) }}</textarea>
                    @error('unserviceable_reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>
    </div>

    <div class="pmms-card p-6">
        <div class="grid grid-cols-1 gap-4">
            @foreach([
                ['field' => 'windows_update', 'remarks' => 'windows_update_remarks', 'label' => 'Windows Update'],
                ['field' => 'remove_unnecessary_apps', 'remarks' => 'remove_unnecessary_apps_remarks', 'label' => 'Remove Unnecessary Apps'],
                ['field' => 'health_check_diagnosis', 'remarks' => 'health_check_diagnosis_remarks', 'label' => 'Health Check Diagnosis'],
                ['field' => 'virus_scan', 'remarks' => 'virus_scan_remarks', 'label' => 'Virus Scan'],
                ['field' => 'physical_inspection', 'remarks' => 'physical_inspection_remarks', 'label' => 'Physical Inspection'],
                ['field' => 'cdp', 'remarks' => 'cdp_remarks', 'label' => 'CDP (Cleaning, dusting, and polishing)'],
            ] as $item)
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="{{ $item['field'] }}" value="1" @checked(old($item['field'], optional($preventiveMaintenance)->{$item['field']} ?? false)) class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-slate-800">{{ $item['label'] }}</span>
                    </label>
                    <div class="mt-3">
                        <input type="text" name="{{ $item['remarks'] }}" class="pmms-input w-full" placeholder="Optional remarks" value="{{ old($item['remarks'], optional($preventiveMaintenance)->{$item['remarks']}) }}">
                        @error($item['remarks']) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="pmms-card p-6">
        <label class="mb-2 block text-sm font-medium text-slate-700">Overall Remarks</label>
        <textarea name="overall_remarks" rows="4" class="pmms-input w-full" placeholder="Additional notes about this maintenance session">{{ old('overall_remarks', optional($preventiveMaintenance)->overall_remarks) }}</textarea>
        @error('overall_remarks') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('preventive-maintenances.index') }}" class="pmms-btn-secondary">Cancel</a>
        <button type="submit" class="pmms-btn-primary">{{ $buttonText }}</button>
    </div>
</div>
