<div>
@if($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

        <div class="relative max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ $isEdit ? 'Edit PM Log' : 'Log Preventive Maintenance' }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $isEdit ? 'Update maintenance inspection details.' : 'Record a preventive maintenance inspection for equipment.' }}
                    </p>
                </div>
                <button type="button" wire:click="closeModal" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-6">
                {{-- Equipment Selection --}}
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Equipment Information</h3>

                    @if($equipmentLocked && $selectedEquipment)
                        <div class="rounded-lg border border-indigo-100 bg-white p-4">
                            <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Property No.</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-slate-900">{{ $selectedEquipment->property_no }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Equipment Type</dt>
                                    <dd class="mt-0.5 text-sm text-slate-900">{{ $selectedEquipment->equipmentType?->name ?? '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Brand / Model</dt>
                                    <dd class="mt-0.5 text-sm text-slate-900">
                                        {{ $selectedEquipment->brand ?? '—' }}
                                        @if($selectedEquipment->model)
                                            <span class="text-slate-400"> / {{ $selectedEquipment->model }}</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-slate-400">Office</dt>
                                    <dd class="mt-0.5 text-sm text-slate-900">{{ $selectedEquipment->office?->office_name ?? '—' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Region <span class="text-red-500">*</span></label>
                            <select wire:model.live="region_id" class="pmms-select">
                                <option value="">Select region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->region_short_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Province <span class="text-red-500">*</span></label>
                            <select wire:model.live="province_id" class="pmms-select" @disabled(! $region_id)>
                                <option value="">Select province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Office <span class="text-red-500">*</span></label>
                            <select wire:model.live="office_id" class="pmms-select" @disabled(! $province_id)>
                                <option value="">Select office</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Equipment <span class="text-red-500">*</span></label>
                            <select wire:model="equipment_id" class="pmms-select" @disabled(! $office_id)>
                                <option value="">Select equipment</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">
                                        {{ $equipment->property_no }} — {{ $equipment->equipmentType?->name }} ({{ $equipment->brand ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('equipment_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            @if($office_id && $equipments->isEmpty())
                                <p class="mt-1 text-xs text-amber-600">No equipment found in this office.</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Date Inspected <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="date_inspected" class="pmms-input">
                            @error('date_inspected') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Overall Status <span class="text-red-500">*</span></label>
                            <select wire:model.live="status" class="pmms-select">
                                <option value="Serviceable">Serviceable</option>
                                <option value="Unserviceable">Unserviceable</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        @if($status === 'Unserviceable')
                            <div class="sm:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-slate-700">Unserviceable Reason <span class="text-red-500">*</span></label>
                                <textarea wire:model="unserviceable_reason" rows="2" class="pmms-input" placeholder="Describe why the equipment is unserviceable"></textarea>
                                @error('unserviceable_reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Checklist --}}
                <div class="rounded-xl border border-slate-200 p-4">
                    <h3 class="mb-4 text-sm font-semibold text-slate-900">Maintenance Checklist</h3>
                    <div class="space-y-4">
                        @foreach([
                            ['field' => 'windows_update', 'remarks' => 'windows_update_remarks', 'label' => 'Windows Update'],
                            ['field' => 'remove_unnecessary_apps', 'remarks' => 'remove_unnecessary_apps_remarks', 'label' => 'Remove Unnecessary Apps'],
                            ['field' => 'virus_scan', 'remarks' => 'virus_scan_remarks', 'label' => 'Virus Scan'],
                            ['field' => 'physical_inspection', 'remarks' => 'physical_inspection_remarks', 'label' => 'Physical Inspection'],
                            ['field' => 'cdp', 'remarks' => 'cdp_remarks', 'label' => 'CDP (Continuous Data Protection)'],
                        ] as $item)
                            <div class="rounded-lg border border-slate-100 bg-slate-50/50 p-3">
                                <label class="flex cursor-pointer items-center gap-3">
                                    <input
                                        type="checkbox"
                                        wire:model="{{ $item['field'] }}"
                                        class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-slate-800">{{ $item['label'] }}</span>
                                </label>
                                <div class="mt-2 pl-7">
                                    <input
                                        type="text"
                                        wire:model="{{ $item['remarks'] }}"
                                        class="pmms-input !py-2 text-sm"
                                        placeholder="Optional remarks">
                                    @error($item['remarks']) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Overall Remarks --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-700">Overall Remarks</label>
                    <textarea wire:model="overall_remarks" rows="3" class="pmms-input" placeholder="Additional notes about this maintenance session"></textarea>
                    @error('overall_remarks') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">
                    <button type="button" wire:click="closeModal" class="pmms-btn-secondary">Cancel</button>
                    <button type="submit" class="pmms-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Update PM Log' : 'Save PM Log' }}</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
</div>
