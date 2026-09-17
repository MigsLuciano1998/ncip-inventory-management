<div>
@if($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

        <div class="relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
            <div class="mb-6 flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">
                        {{ $isEdit ? 'Edit Equipment' : 'Add Equipment' }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        {{ $isEdit ? 'Update equipment information.' : 'Register a new ICT equipment item.' }}
                    </p>
                </div>
                <button type="button" wire:click="closeModal" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form wire:submit="save" class="space-y-6">
                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">Classification & location</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Equipment Category <span class="text-red-500">*</span></label>
                            <select wire:model.live="equipment_category_id" class="pmms-select">
                                <option value="">Select category</option>
                                @foreach($equipmentCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->short_name }} — {{ $category->title }}</option>
                                @endforeach
                            </select>
                            @error('equipment_category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Equipment Type <span class="text-red-500">*</span></label>
                            <select wire:model="equipment_type_id" class="pmms-select" @disabled(! $equipment_category_id)>
                                <option value="">Select equipment type</option>
                                @foreach($equipmentTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('equipment_type_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            @if($equipment_category_id && $equipmentTypes->isEmpty())
                                <p class="mt-1 text-xs text-amber-600">No equipment types in this category yet.</p>
                            @endif
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Region <span class="text-red-500">*</span></label>
                            <select wire:model.live="region_id" class="pmms-select">
                                <option value="">Select region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region->id }}">{{ $region->region_short_name }}</option>
                                @endforeach
                            </select>
                            @error('region_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Province <span class="text-red-500">*</span></label>
                            <select wire:key="province-select-{{ $region_id }}" wire:model.live="province_id" class="pmms-select" @disabled(! $region_id)>
                                <option value="">Select province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                                @endforeach
                            </select>
                            @error('province_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Office <span class="text-red-500">*</span></label>
                            <select wire:model.live="office_id" class="pmms-select" @disabled(! $province_id)>
                                <option value="">Select office</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                                @endforeach
                            </select>
                            @error('office_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="relative sm:col-span-2" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Assigned Employee</label>
                            <button
                                type="button"
                                @click="open = !open; if (open) $nextTick(() => $refs.employeeSearch?.focus())"
                                class="pmms-select flex items-center justify-between text-left">
                                <span class="{{ $selectedEmployee ? 'text-slate-800' : 'text-slate-400' }} truncate">
                                    @if($selectedEmployee)
                                        {{ $selectedEmployee->last_name_first }}
                                        @if($selectedEmployee->ee_sequence_number)
                                            <span class="text-slate-400">({{ $selectedEmployee->ee_sequence_number }})</span>
                                        @endif
                                    @else
                                        Leave unassigned
                                    @endif
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-slate-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition
                                class="absolute z-30 mt-1 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                                <div class="border-b border-slate-100 p-2">
                                    <input
                                        x-ref="employeeSearch"
                                        type="text"
                                        wire:model.live.debounce.200ms="employee_search"
                                        placeholder="Search by last name, first name, or EE sequence no."
                                        class="pmms-input !py-2">
                                </div>
                                <div class="max-h-56 overflow-y-auto py-1">
                                    <button
                                        type="button"
                                        wire:click="selectEmployee(null)"
                                        @click="open = false"
                                        class="block w-full px-3 py-2 text-left text-sm text-slate-500 hover:bg-slate-50">
                                        Leave unassigned
                                    </button>
                                    @forelse($employees as $employee)
                                        <button
                                            type="button"
                                            wire:key="employee-option-{{ $employee->id }}"
                                            wire:click="selectEmployee({{ $employee->id }})"
                                            @click="open = false"
                                            class="block w-full px-3 py-2 text-left text-sm hover:bg-indigo-50 {{ (string) $employee_id === (string) $employee->id ? 'bg-indigo-50 text-indigo-700' : 'text-slate-800' }}">
                                            <span class="font-medium">{{ $employee->last_name_first }}</span>
                                            <span class="text-slate-400"> — {{ $employee->ee_sequence_number }}</span>
                                        </button>
                                    @empty
                                        <p class="px-3 py-2 text-sm text-slate-400">No employees found.</p>
                                    @endforelse
                                </div>
                            </div>
                            @error('employee_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">Identification</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Property No. <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                value="{{ $generatedPropertyNo ?? '' }}"
                                class="pmms-input bg-slate-50 text-slate-700"
                                placeholder="Generated from Tag, Year Purchased, PPE, GL, Series, and Location"
                                readonly
                                tabindex="-1">
                            <p class="mt-1 text-xs text-slate-500">System generated. Fill Tag, Date Purchased, PPE Major Account Group, General Ledger, Series Number, and Location Number.</p>
                            @error('property_no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Tag <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.live="tag" class="pmms-input" placeholder="e.g. LAPTOP">
                            @error('tag') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Item ID</label>
                            <input type="text" wire:model="item_id" class="pmms-input">
                            @error('item_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Serial No.</label>
                            <input type="text" wire:model="serial_no" class="pmms-input" placeholder="Serial number">
                            @error('serial_no') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Brand</label>
                            <input type="text" wire:model="brand" class="pmms-input" placeholder="e.g. Dell, HP">
                            @error('brand') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Model</label>
                            <input type="text" wire:model="model" class="pmms-input" placeholder="Model name">
                            @error('model') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">Accounting</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="mb-2 block text-sm font-medium text-slate-700">PPE Category <span class="text-red-500">*</span></label>
                            <select wire:model.live="ppe_category_id" class="pmms-select">
                                <option value="">Select PPE category</option>
                                @foreach($ppeCategories as $ppeCategory)
                                    <option value="{{ $ppeCategory->id }}">{{ $ppeCategory->number }} — {{ $ppeCategory->title }}</option>
                                @endforeach
                            </select>
                            @error('ppe_category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">UACS Object Code</label>
                            <input type="text" wire:model="uacs_object_code" class="pmms-input bg-slate-50 text-slate-700" readonly tabindex="-1">
                            @error('uacs_object_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">PPE Major Group <span class="text-red-500">*</span></label>
                            <input type="number" wire:model.live="ppe_major_account_group" min="0" class="pmms-input bg-slate-50 text-slate-700" readonly tabindex="-1">
                            @error('ppe_major_account_group') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">General Ledger <span class="text-red-500">*</span></label>
                            <input type="number" wire:model.live="general_ledger" min="0" class="pmms-input bg-slate-50 text-slate-700" readonly tabindex="-1">
                            @error('general_ledger') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Series Number <span class="text-red-500">*</span></label>
                            <input type="number" wire:model.live="series_number" min="0" class="pmms-input">
                            @error('series_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Location Number <span class="text-red-500">*</span></label>
                            <input type="number" wire:model.live="location_number" min="0" class="pmms-input bg-slate-50 text-slate-700" readonly tabindex="-1">
                            @error('location_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">EE Seq</label>
                            <input type="text" wire:model="ee_seq" class="pmms-input bg-slate-50 text-slate-700" readonly tabindex="-1">
                            @error('ee_seq') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="sm:col-span-2 lg:col-span-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Form Series Number</label>
                                <input type="text" wire:model="form_series_number" class="pmms-input">
                                @error('form_series_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-slate-700">Form Field</label>
                                <input type="text" wire:model="form_field" class="pmms-input">
                                @error('form_field') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">Acquisition</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Date Purchased <span class="text-red-500">*</span></label>
                            <input type="date" wire:model.live="date_purchased" class="pmms-input">
                            @error('date_purchased') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Date Acquired</label>
                            <input type="date" wire:model="date_acquired" class="pmms-input">
                            @error('date_acquired') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Cost</label>
                            <input type="text" wire:model.live.debounce.300ms="cost" class="pmms-input" placeholder="e.g. 25000.00">
                            @error('cost') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Classification <span class="text-red-500">*</span></label>
                            <select wire:model="classification" class="pmms-select">
                                <option value="">Select classification</option>
                                <option value="ppe">PPE (Above 50,000)</option>
                                <option value="semi_hv">Semi-HV (5,001 – 49,999)</option>
                                <option value="lv">LV (1 – 4,999)</option>
                            </select>
                            @error('classification') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Fund Source</label>
                            <input type="text" wire:model="fund_source" class="pmms-input">
                            @error('fund_source') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Estimated Useful Life (years)</label>
                            <input type="number" wire:model="estimated_useful_life" min="0" max="99" class="pmms-input">
                            @error('estimated_useful_life') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">PAR / ICS & inventory</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">PAR / ICS</label>
                            <input type="text" wire:model="par_ics" class="pmms-input">
                            @error('par_ics') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">PAR / ICS Issued Date</label>
                            <input type="date" wire:model="par_ics_issued_date" class="pmms-input">
                            @error('par_ics_issued_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Date Last Inventory</label>
                            <input type="date" wire:model="date_last_inventory" class="pmms-input">
                            @error('date_last_inventory') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Status Last Inventory</label>
                            <select wire:model="status_last_inventory" class="pmms-select">
                                <option value="">Not set</option>
                                <option value="1">Serviceable</option>
                                <option value="0">Unserviceable</option>
                            </select>
                            @error('status_last_inventory') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Status <span class="text-red-500">*</span></label>
                            <select wire:model="status" class="pmms-select">
                                <option value="1">Serviceable</option>
                                <option value="0">Unserviceable</option>
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="mb-3 text-sm font-semibold text-slate-900">Notes</h3>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Description</label>
                            <textarea wire:model="description" rows="3" class="pmms-input" placeholder="Optional notes about this equipment"></textarea>
                            @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Remarks</label>
                            <textarea wire:model="remarks" rows="3" class="pmms-input"></textarea>
                            @error('remarks') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">
                    <button type="button" wire:click="closeModal" class="pmms-btn-secondary">Cancel</button>
                    <button type="submit" class="pmms-btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Update Equipment' : 'Save Equipment' }}</span>
                        <span wire:loading wire:target="save">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
</div>
