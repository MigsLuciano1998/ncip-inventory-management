<div>
    @if(session()->has('message'))
        <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="mb-4 flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <x-page-header title="Offices" description="Manage offices by region and province.">
        <x-slot:actions>
            <button wire:click="openAdd" class="pmms-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Office
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 pmms-card p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="relative xl:col-span-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search office name or location..."
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
                @foreach($filterProvinces as $province)
                    <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="pmms-card overflow-hidden">
        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>Office Name</th>
                        <th>Region</th>
                        <th>Province</th>
                        <th>Location</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($offices as $office)
                        <tr wire:key="office-{{ $office->id }}">
                            <td class="font-medium text-slate-900">{{ $office->office_name }}</td>
                            <td class="text-slate-500">{{ $office->province?->region?->region_short_name ?? '—' }}</td>
                            <td class="text-slate-500">{{ $office->province?->province_name ?? '—' }}</td>
                            <td class="text-slate-500">{{ $office->office_location ?? '—' }}</td>
                            <td class="text-center">
                                @if($office->status)
                                    <span class="pmms-badge-success">Active</span>
                                @else
                                    <span class="pmms-badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="edit({{ $office->id }})" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                        Edit
                                    </button>
                                    <button
                                        wire:click="delete({{ $office->id }})"
                                        wire:confirm="Are you sure you want to delete this office?"
                                        class="pmms-btn-danger !px-3 !py-1.5 !text-xs">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                    </svg>
                                    <span>No offices found for the selected filters.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($offices->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $offices->links() }}
            </div>
        @endif
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $isEdit ? 'Edit Office' : 'Add Office' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Fill in the office details below.</p>

                <form wire:submit="save" class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Region <span class="text-red-500">*</span></label>
                        <select wire:model.live="form_region_id" class="pmms-select">
                            <option value="">Select region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->region_short_name }}</option>
                            @endforeach
                        </select>
                        @error('form_region_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Province <span class="text-red-500">*</span></label>
                        <select wire:model="form_province_id" class="pmms-select" @disabled(! $form_region_id)>
                            <option value="">Select province</option>
                            @foreach($formProvinces as $province)
                                <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                            @endforeach
                        </select>
                        @error('form_province_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Office Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="office_name" class="pmms-input" placeholder="e.g. Benguet Provincial Office">
                        @error('office_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Location</label>
                        <input type="text" wire:model="office_location" class="pmms-input" placeholder="Office address">
                        @error('office_location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Status</label>
                        <select wire:model="status" class="pmms-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal" class="pmms-btn-secondary">Cancel</button>
                        <button type="submit" class="pmms-btn-primary">
                            {{ $isEdit ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
