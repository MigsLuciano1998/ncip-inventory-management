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

    <x-page-header title="Regions" description="View and manage regional divisions.">
        <x-slot:actions>
            <button wire:click="openAdd" class="pmms-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Region
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 pmms-card p-5">
        <div class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search short name or complete name..."
                class="pmms-input !pl-10">
        </div>
    </div>

    <div class="pmms-card overflow-hidden">
        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>Short Name</th>
                        <th>Complete Name</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($regions as $region)
                        <tr wire:key="region-{{ $region->id }}">
                            <td class="font-medium text-slate-900">{{ $region->region_short_name }}</td>
                            <td class="text-slate-700">{{ $region->region_complete_name }}</td>
                            <td class="text-center">
                                @if($region->status)
                                    <span class="pmms-badge-success">Active</span>
                                @else
                                    <span class="pmms-badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="edit({{ $region->id }})" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                        Edit
                                    </button>
                                    <button
                                        wire:click="delete({{ $region->id }})"
                                        wire:confirm="Are you sure you want to delete this region?"
                                        class="pmms-btn-danger !px-3 !py-1.5 !text-xs">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                                    </svg>
                                    <span>No regions found.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($regions->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $regions->links() }}
            </div>
        @endif
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $isEdit ? 'Edit Region' : 'Add Region' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Fill in the region details below.</p>

                <form wire:submit="save" class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Short Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="region_short_name" class="pmms-input" placeholder="e.g. CAR">
                        @error('region_short_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Complete Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="region_complete_name" class="pmms-input" placeholder="e.g. Cordillera Administrative Region">
                        @error('region_complete_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
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
