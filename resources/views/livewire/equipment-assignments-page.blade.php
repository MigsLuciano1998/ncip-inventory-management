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

    <x-page-header title="Equipment Assignments" description="Assign equipment to employees and track every movement.">
        <x-slot:actions>
            <button wire:click="openAssignModal" class="pmms-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Assign Equipment
            </button>
        </x-slot:actions>
    </x-page-header>

    <div class="pmms-card p-5">
        <div class="relative max-w-md">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by employee or property no..."
                class="pmms-input !pl-10">
        </div>
    </div>

    {{-- Active Assignments --}}
    <div class="pmms-card overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Active Assignments</h2>
            <p class="mt-1 text-sm text-slate-500">Equipment currently assigned to employees.</p>
        </div>

        <div class="pmms-table-wrap rounded-none border-0 border-b">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>Property No.</th>
                        <th>Equipment Type</th>
                        <th>Employee</th>
                        <th>Office</th>
                        <th>Date Assigned</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($activeAssignments as $assignment)
                        <tr wire:key="assignment-{{ $assignment->id }}">
                            <td class="font-medium text-slate-900">{{ $assignment->equipment->property_no }}</td>
                            <td>{{ $assignment->equipment->equipmentType?->name ?? '—' }}</td>
                            <td class="font-medium text-slate-900">{{ $assignment->employee?->full_name ?? '—' }}</td>
                            <td class="text-slate-500">{{ $assignment->office->office_name }}</td>
                            <td class="text-slate-500">{{ $assignment->date_assigned->format('M d, Y') }}</td>
                            <td class="text-center">
                                <button
                                    wire:click="openReturnModal({{ $assignment->id }})"
                                    class="pmms-btn-secondary !px-3 !py-1.5 !text-xs">
                                    Return Equipment
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                No active assignments. Click "Assign Equipment" to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activeAssignments->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $activeAssignments->links() }}
            </div>
        @endif
    </div>

    {{-- Movement Log --}}
    <div class="pmms-card overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4">
            <h2 class="text-base font-semibold text-slate-900">Movement Log</h2>
            <p class="mt-1 text-sm text-slate-500">Complete audit trail of all equipment movements.</p>
        </div>

        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Property No.</th>
                        <th>Action</th>
                        <th>Employee</th>
                        <th>Office</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($movementLogs as $log)
                        <tr wire:key="log-{{ $log->id }}">
                            <td class="whitespace-nowrap text-slate-500">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                            <td class="font-medium text-slate-900">{{ $log->equipment->property_no }}</td>
                            <td>
                                @php
                                    $badgeClass = match($log->action) {
                                        'assigned' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/10',
                                        'returned' => 'bg-amber-50 text-amber-700 ring-amber-600/10',
                                        'transferred' => 'bg-violet-50 text-violet-700 ring-violet-600/10',
                                        'created' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10',
                                        'deleted' => 'bg-rose-50 text-rose-700 ring-rose-600/10',
                                        default => 'bg-slate-50 text-slate-700 ring-slate-600/10',
                                    };
                                @endphp
                                <span class="pmms-badge ring-1 {{ $badgeClass }}">{{ $log->actionLabel() }}</span>
                            </td>
                            <td>
                                @if($log->action === 'transferred' && $log->previous_owner_name)
                                    {{ $log->previous_owner_name }} → {{ $log->owner_name }}
                                @else
                                    {{ $log->owner_name ?? '—' }}
                                @endif
                            </td>
                            <td class="text-slate-500">
                                @if($log->action === 'transferred' && $log->previousOffice)
                                    {{ $log->previousOffice->office_name }} → {{ $log->office?->office_name }}
                                @else
                                    {{ $log->office?->office_name ?? '—' }}
                                @endif
                            </td>
                            <td class="max-w-xs truncate text-slate-500" title="{{ $log->remarks }}">{{ $log->remarks ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                No movement records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movementLogs->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $movementLogs->links() }}
            </div>
        @endif
    </div>

    {{-- Assign Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>

            <div class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold text-slate-900">Assign Equipment to Employee</h2>
                <p class="mt-1 text-sm text-slate-500">Select equipment and enter the employee details.</p>

                <form wire:submit="assign" class="mt-6 space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Region</label>
                        <select wire:model.live="region_id" class="pmms-select">
                            <option value="">Select region</option>
                            @foreach($regions as $region)
                                <option value="{{ $region->id }}">{{ $region->region_short_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Province</label>
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
                        @error('office_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Equipment <span class="text-red-500">*</span></label>
                        <select wire:model="equipment_id" class="pmms-select" @disabled(! $office_id)>
                            <option value="">Select available equipment</option>
                            @foreach($availableEquipment as $equipment)
                                <option value="{{ $equipment->id }}">
                                    {{ $equipment->property_no }} — {{ $equipment->equipmentType?->name }} ({{ $equipment->brand ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('equipment_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @if($office_id && $availableEquipment->isEmpty())
                            <p class="mt-1 text-xs text-amber-600">No unassigned serviceable equipment in this office.</p>
                        @endif
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Employee <span class="text-red-500">*</span></label>
                        <select wire:model="employee_id" class="pmms-select">
                            <option value="">Select employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }} ({{ $employee->ee_sequence_number }})</option>
                            @endforeach
                        </select>
                        @error('employee_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Date Assigned <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="date_assigned" class="pmms-input">
                        @error('date_assigned') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Remarks</label>
                        <textarea wire:model="remarks" rows="2" class="pmms-input" placeholder="Optional notes for the movement log"></textarea>
                        @error('remarks') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" wire:click="closeModal" class="pmms-btn-secondary">Cancel</button>
                        <button type="submit" class="pmms-btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="assign">Assign Equipment</span>
                            <span wire:loading wire:target="assign">Assigning...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Return Confirmation Modal --}}
    @if($showReturnModal && $returningAssignment)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeReturnModal"></div>

            <div class="relative w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Confirm equipment return</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Return {{ $returningAssignment->equipment->property_no }} from {{ $returningAssignment->employee?->full_name ?? 'this employee' }}.
                        </p>
                    </div>
                </div>

                <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3 text-sm">
                    <dl class="space-y-1.5">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Property No.</dt>
                            <dd class="font-medium text-slate-900">{{ $returningAssignment->equipment->property_no }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Type</dt>
                            <dd class="font-medium text-slate-900">{{ $returningAssignment->equipment->equipmentType?->name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Employee</dt>
                            <dd class="font-medium text-slate-900">{{ $returningAssignment->employee?->full_name ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500">Office</dt>
                            <dd class="font-medium text-slate-900">{{ $returningAssignment->office?->office_name ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                    <p class="font-medium">This action is permanent.</p>
                    <p class="mt-1 text-amber-700">The assignment will be closed, recorded in the movement log, and cannot be undone from this screen.</p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" wire:click="closeReturnModal" class="pmms-btn-secondary">Cancel</button>
                    <button type="button" wire:click="returnEquipment" class="pmms-btn-warning" wire:loading.attr="disabled" wire:target="returnEquipment">
                        <span wire:loading.remove wire:target="returnEquipment">Confirm Return</span>
                        <span wire:loading wire:target="returnEquipment">Returning...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
