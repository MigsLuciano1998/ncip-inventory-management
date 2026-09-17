<div>
    @if(session()->has('message'))
        <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            {{ session('message') }}
        </div>
    @endif

    <x-page-header title="Employees" description="Manage employee records.">
        <x-slot:actions>
            <button type="button" wire:click="$set('showModal', true)" class="pmms-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Employee
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="pmms-card p-5">
        <div class="max-w-md">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search EE sequence number or name..."
                class="pmms-input w-full"
            >
        </div>
    </div>

    <div class="pmms-card overflow-hidden">
        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>EE Sequence #</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($employees as $employee)
                        <tr>
                            <td class="font-mono text-xs text-slate-400">#{{ $employee->id }}</td>
                            <td class="font-medium text-slate-900">{{ $employee->ee_sequence_number }}</td>
                            <td>{{ $employee->first_name }}</td>
                            <td>{{ $employee->middle_name ?? '—' }}</td>
                            <td>{{ $employee->last_name }}</td>
                            <td>{{ $employee->position ?? '—' }}</td>
                            <td>{{ $employee->office?->office_name ?? '—' }}</td>
                            <td class="text-center">
                                @if($employee->status)
                                    <span class="pmms-badge-success">Active</span>
                                @else
                                    <span class="pmms-badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button wire:click="edit({{ $employee->id }})" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $employee->id }})" class="pmms-btn-danger !px-3 !py-1.5 !text-xs">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">No employees found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="px-4 py-3">
        {{ $employees->links() }}
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="relative w-full max-w-xl rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold text-slate-900">
                    {{ $isEdit ? 'Edit Employee' : 'Add Employee' }}
                </h2>
                <p class="mt-1 text-sm text-slate-500">Fill in the details below.</p>

                <form wire:submit.prevent="save" class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">EE Sequence Number</label>
                        <input type="text" wire:model="ee_sequence_number" class="pmms-input" placeholder="e.g. NCIP-001">
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">First Name</label>
                            <input type="text" wire:model="first_name" class="pmms-input" placeholder="First name">
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-slate-700">Middle Name</label>
                            <input type="text" wire:model="middle_name" class="pmms-input" placeholder="Middle name">
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Last Name</label>
                            <input type="text" wire:model="last_name" class="pmms-input" placeholder="Last name">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Position</label>
                        <input type="text" wire:model="position" class="pmms-input" placeholder="Job title or role">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Office</label>
                        <select wire:model="office_id" class="pmms-select">
                            <option value="">Select office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                            @endforeach
                        </select>
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
