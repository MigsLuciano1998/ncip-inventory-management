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

    <x-page-header title="Equipment Types" description="Manage equipment types under each category.">
        <x-slot:actions>
            <button wire:click="openAdd" class="pmms-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Equipment Type
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="pmms-card overflow-hidden">
        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Equipment Type</th>
                        <th>Category</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($equipmentTypes as $equipmentType)
                        <tr>
                            <td class="font-mono text-xs text-slate-400">#{{ $equipmentType->id }}</td>
                            <td class="font-medium text-slate-900">{{ $equipmentType->name }}</td>
                            <td class="text-slate-500">
                                {{ $equipmentType->equipmentCategory?->short_name ?? '—' }}
                                @if($equipmentType->equipmentCategory?->title)
                                    <span class="text-slate-400">— {{ $equipmentType->equipmentCategory->title }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($equipmentType->status)
                                    <span class="pmms-badge-success">Active</span>
                                @else
                                    <span class="pmms-badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="edit({{ $equipmentType->id }})" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $equipmentType->id }})" class="pmms-btn-danger !px-3 !py-1.5 !text-xs">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">No equipment types found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $isEdit ? 'Edit Equipment Type' : 'Add Equipment Type' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Fill in the details below.</p>

                <form wire:submit="save" class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Equipment Category <span class="text-red-500">*</span></label>
                        <select wire:model="equipment_category_id" class="pmms-select">
                            <option value="">Select category</option>
                            @foreach($equipmentCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->short_name }} — {{ $category->title }}</option>
                            @endforeach
                        </select>
                        @error('equipment_category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Equipment Type</label>
                        <input type="text" wire:model="name" class="pmms-input" placeholder="e.g. Desktop Computer">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
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
