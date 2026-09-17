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

    <x-page-header title="PPE Categories" description="Manage PPE major account groups, UACS object codes, and general ledger accounts.">
        <x-slot:actions>
            <button wire:click="openAdd" class="pmms-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add PPE Category
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="pmms-card overflow-hidden">
        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>Number</th>
                        <th>Title</th>
                        <th>UACS Object Code</th>
                        <th>PPE Sub-Major</th>
                        <th>General Ledger</th>
                        <th>Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($ppeCategories as $category)
                        <tr wire:key="ppe-category-{{ $category->id }}">
                            <td class="font-semibold text-slate-900">{{ $category->number }}</td>
                            <td class="font-medium text-slate-900">{{ $category->title }}</td>
                            <td class="font-mono text-xs text-slate-600">{{ $category->uacs_object_code }}</td>
                            <td class="text-slate-700">{{ $category->ppe_sub_major_account_group }}</td>
                            <td class="text-slate-700">{{ $category->general_ledger_account }}</td>
                            <td class="text-slate-500">{{ $category->date?->format('M d, Y') ?? '—' }}</td>
                            <td class="text-center">
                                @if($category->status)
                                    <span class="pmms-badge-success">Active</span>
                                @else
                                    <span class="pmms-badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="edit({{ $category->id }})" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                        Edit
                                    </button>
                                    <button
                                        wire:click="delete({{ $category->id }})"
                                        wire:confirm="Are you sure you want to delete this PPE category?"
                                        class="pmms-btn-danger !px-3 !py-1.5 !text-xs">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">No PPE categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $isEdit ? 'Edit PPE Category' : 'Add PPE Category' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Fill in the PPE category details below.</p>

                <form wire:submit="save" class="mt-6 space-y-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Number <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="number" class="pmms-input" placeholder="e.g. 4 or 4A">
                            @error('number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Date <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="date" class="pmms-input">
                            @error('date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Title <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="title" class="pmms-input" placeholder="e.g. Office Equipment">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">UACS Object Code <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="uacs_object_code" class="pmms-input" placeholder="e.g. 1-06-05-02-000">
                        @error('uacs_object_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">PPE Sub-Major Account Group <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="ppe_sub_major_account_group" class="pmms-input" placeholder="e.g. 05">
                            @error('ppe_sub_major_account_group') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">General Ledger Account <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="general_ledger_account" class="pmms-input" placeholder="e.g. 02">
                            @error('general_ledger_account') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
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
