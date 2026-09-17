<div class="space-y-6">

    @if(session()->has('message'))
        <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <x-page-header title="Equipment" description="Manage all ICT equipment.">
        <x-slot:actions>
            <button wire:click="openAddEquipment" class="pmms-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Equipment
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="pmms-card p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search property, tag, serial, brand..."
                    class="pmms-input !pl-10">
            </div>

            <select wire:model.live="selectedRegion" class="pmms-select">
                <option value="">All Regions</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}">{{ $region->region_short_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="selectedProvince" class="pmms-select">
                <option value="">All Provinces</option>
                @foreach($provinces as $province)
                    <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="selectedOffice" class="pmms-select">
                <option value="">All Offices</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                @endforeach
            </select>

            <select wire:model.live="selectedClassification" class="pmms-select">
                <option value="">All Classifications</option>
                <option value="ppe">PPE</option>
                <option value="semi_hv">Semi-HV</option>
                <option value="lv">LV</option>
            </select>
        </div>
    </div>

    <div class="pmms-card overflow-hidden">
        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>Property No.</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Brand / Model</th>
                        <th>Office</th>
                        <th>Assignee</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($equipments as $equipment)
                        <tr
                            wire:key="equipment-{{ $equipment->id }}"
                            onclick="window.location='{{ route('equipment.show', $equipment) }}'"
                            class="cursor-pointer transition hover:bg-indigo-50/40">
                            <td class="font-medium text-slate-900">{{ $equipment->property_no }}</td>
                            <td>
                                <span class="inline-flex items-center gap-1.5">
                                    {{ $equipment->equipmentCategory?->short_name ?? '—' }}
                                    @if($equipment->classification)
                                        <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-semibold {{ $equipment->classificationBadgeClasses() }}">
                                            {{ $equipment->classificationLabel() }}
                                        </span>
                                    @endif
                                </span>
                            </td>
                            <td>{{ $equipment->equipmentType?->name ?? '—' }}</td>
                            <td>
                                <span class="text-slate-900">{{ $equipment->brand ?? '—' }}</span>
                                @if($equipment->model)
                                    <span class="text-slate-400"> / {{ $equipment->model }}</span>
                                @endif
                            </td>
                            <td class="text-slate-500">{{ $equipment->office?->office_name ?? '—' }}</td>
                            <td class="text-slate-500">
                                @if($equipment->activeAssignment)
                                    <span class="font-medium text-slate-700">{{ $equipment->activeAssignment->employee?->full_name ?? '—' }}</span>
                                @else
                                    <span class="text-slate-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($equipment->status)
                                    <span class="pmms-badge-success">Serviceable</span>
                                @else
                                    <span class="pmms-badge-danger">Unserviceable</span>
                                @endif
                            </td>
                            <td class="text-center" onclick="event.stopPropagation()">
                                <div class="inline-flex items-center gap-2">
                                    <a
                                        href="{{ route('preventive-maintenances.create', ['equipment_id' => $equipment->id]) }}"
                                        title="Log Preventive Maintenance"
                                        class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 p-1.5 text-indigo-600 transition hover:bg-indigo-100 focus:outline-none focus:ring-4 focus:ring-indigo-500/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                        </svg>
                                    </a>
                                    <button wire:click="openEditEquipment({{ $equipment->id }})" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                        Edit
                                    </button>
                                    <button
                                        wire:click="delete({{ $equipment->id }})"
                                        wire:confirm="Are you sure you want to delete this equipment?"
                                        class="pmms-btn-danger !px-3 !py-1.5 !text-xs">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
                                    </svg>
                                    <span>No equipment found for the selected filters.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($equipments->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $equipments->links() }}
            </div>
        @endif
    </div>

    <livewire:equipment.equipment-modal />

</div>
