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

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('equipment.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Equipment
            </a>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $equipment->property_no }}</h1>
                @if($equipment->status)
                    <span class="pmms-badge-success">Serviceable</span>
                @else
                    <span class="pmms-badge-danger">Unserviceable</span>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-500">
                {{ $equipment->equipmentType?->name ?? 'Unknown type' }}
                @if($equipment->brand || $equipment->model)
                    · {{ trim(($equipment->brand ?? '') . ' ' . ($equipment->model ?? '')) }}
                @endif
            </p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('preventive-maintenances.create', ['equipment_id' => $equipment->id]) }}" class="pmms-btn-secondary">
                Log PM
            </a>
            @if($equipment->activeAssignment)
                <button type="button" wire:click="openReturn" class="pmms-btn-warning">
                    Return
                </button>
            @endif
            <button type="button" wire:click="openTransfer" class="pmms-btn-secondary">
                Transfer
            </button>
            <button type="button" wire:click="openEdit" class="pmms-btn-warning">
                Edit Equipment
            </button>
        </div>
    </div>

    {{-- Equipment Details --}}
    <section class="pmms-card p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">Equipment Information</h2>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 sm:col-span-2 lg:col-span-3">
                <dt class="text-xs font-medium text-slate-400">Description</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $equipment->description ?: '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Equipment Type</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->equipmentType?->name ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Brand / Model</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">
                    {{ $equipment->brand ?? '—' }}
                    @if($equipment->model)
                        <span class="text-slate-400">/ {{ $equipment->model }}</span>
                    @endif
                </dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Serial No.</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->serial_no ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Equipment Category</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">
                    {{ $equipment->equipmentCategory?->title ?? '—' }}
                    @if($equipment->equipmentCategory?->short_name)
                        <span class="text-slate-400">({{ $equipment->equipmentCategory->short_name }})</span>
                    @endif
                </dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Tag</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->tag ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Item ID</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->item_id ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Region</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->office?->province?->region?->region_short_name ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Province</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->office?->province?->province_name ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Office</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->office?->office_name ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 sm:col-span-2 lg:col-span-3">
                <dt class="text-xs font-medium text-slate-400">Remarks</dt>
                <dd class="mt-1 text-sm text-slate-700">{{ $equipment->remarks ?: '—' }}</dd>
            </div>
        </dl>
    </section>

    <section class="pmms-card p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">Accounting</h2>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 sm:col-span-2 lg:col-span-3">
                <dt class="text-xs font-medium text-slate-400">PPE Category</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">
                    @if($equipment->ppeCategory)
                        {{ $equipment->ppeCategory->number }} — {{ $equipment->ppeCategory->title }}
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">UACS Object Code</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->uacs_object_code ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">PPE Major Group</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->ppe_major_account_group ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">General Ledger</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->general_ledger ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Series Number</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->series_number ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Location Number</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->location_number ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">EE Seq</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->ee_seq ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Form Series Number</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->form_series_number ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Form Field</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->form_field ?? '—' }}</dd>
            </div>
        </dl>
    </section>

    <section class="pmms-card p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">Acquisition</h2>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Date Purchased</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->date_purchased?->format('M d, Y') ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Date Acquired</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->date_acquired?->format('M d, Y') ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Cost</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->cost ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Classification</dt>
                <dd class="mt-1">
                    @if($equipment->classification)
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold {{ $equipment->classificationBadgeClasses() }}">
                            {{ $equipment->classificationLabel() }}
                        </span>
                    @else
                        <span class="text-sm font-medium text-slate-400">—</span>
                    @endif
                </dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Fund Source</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->fund_source ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Estimated Useful Life</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->estimated_useful_life !== null ? $equipment->estimated_useful_life . ' year(s)' : '—' }}</dd>
            </div>
        </dl>
    </section>

    <section class="pmms-card p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">PAR / ICS & inventory</h2>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">PAR / ICS</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->par_ics ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">PAR / ICS Issued Date</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->par_ics_issued_date?->format('M d, Y') ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Date Last Inventory</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->date_last_inventory?->format('M d, Y') ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Status Last Inventory</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">
                    @if($equipment->status_last_inventory === null)
                        —
                    @elseif($equipment->status_last_inventory)
                        <span class="pmms-badge-success">Serviceable</span>
                    @else
                        <span class="pmms-badge-danger">Unserviceable</span>
                    @endif
                </dd>
            </div>
        </dl>
    </section>

    {{-- Current Assignment --}}
    <section class="pmms-card p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">Current Assignment</h2>
        @if($equipment->activeAssignment)
            <div class="rounded-xl border border-indigo-100 bg-indigo-50/50 p-4">
                <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm">
                    <div>
                        <span class="text-slate-500">Assignee:</span>
                        <span class="ml-1 font-semibold text-slate-900">{{ $equipment->activeAssignment->employee?->full_name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Since:</span>
                        <span class="ml-1 font-medium text-slate-900">{{ $equipment->activeAssignment->date_assigned->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
        @else
            <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-500">This equipment is not currently assigned to anyone.</p>
        @endif
    </section>

    {{-- Assignment History --}}
    @if($assignments->total() > 0)
        <section class="pmms-card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Assignment History</h2>
            </div>
            <div class="pmms-table-wrap rounded-none border-0">
                <table class="pmms-table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Office</th>
                            <th>Date Assigned</th>
                            <th>Date Returned</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach($assignments as $assignment)
                            <tr wire:key="assignment-{{ $assignment->id }}">
                                <td class="font-medium text-slate-900">{{ $assignment->employee?->full_name ?? '—' }}</td>
                                <td class="text-slate-500">{{ $assignment->office?->office_name ?? '—' }}</td>
                                <td class="text-slate-500">{{ $assignment->date_assigned->format('M d, Y') }}</td>
                                <td class="text-slate-500">
                                    @if($assignment->date_returned)
                                        {{ $assignment->date_returned->format('M d, Y') }}
                                    @else
                                        <span class="pmms-badge bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/10">Active</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($assignments->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $assignments->links() }}
                </div>
            @endif
        </section>
    @endif

    {{-- PM Logs --}}
    <section class="pmms-card p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Preventive Maintenance Logs</h2>
            <span class="text-xs text-slate-400">{{ $equipment->preventiveMaintenances->count() }} record(s)</span>
        </div>

        @if($equipment->preventiveMaintenances->isNotEmpty())
            <div class="space-y-3">
                @foreach($equipment->preventiveMaintenances as $pm)
                    <div wire:key="pm-{{ $pm->id }}" class="overflow-hidden rounded-xl border border-slate-200">
                        <button
                            type="button"
                            wire:click="togglePmDetails({{ $pm->id }})"
                            class="flex w-full items-center justify-between gap-4 bg-white px-4 py-3 text-left transition hover:bg-slate-50">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="text-sm font-semibold text-slate-900">{{ $pm->date_inspected->format('M d, Y') }}</span>
                                @if($pm->isServiceable())
                                    <span class="pmms-badge-success">Serviceable</span>
                                @else
                                    <span class="pmms-badge-danger">Unserviceable</span>
                                @endif
                                <span class="pmms-badge bg-slate-50 text-slate-700 ring-1 ring-slate-600/10">
                                    Checklist {{ $pm->checklistCompletedCount() }}/5
                                </span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 shrink-0 text-slate-400 transition {{ $expandedPmId === $pm->id ? 'rotate-180' : '' }}">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        @if($expandedPmId === $pm->id)
                            <div class="border-t border-slate-200 bg-slate-50/50 px-4 py-4">
                                @if($pm->unserviceable_reason)
                                    <div class="mb-4 rounded-lg border border-rose-100 bg-rose-50 p-3">
                                        <p class="text-xs font-medium uppercase tracking-wide text-rose-500">Unserviceable Reason</p>
                                        <p class="mt-1 text-sm text-rose-800">{{ $pm->unserviceable_reason }}</p>
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    @foreach([
                                        ['done' => $pm->windows_update, 'label' => 'Windows Update', 'remarks' => $pm->windows_update_remarks],
                                        ['done' => $pm->remove_unnecessary_apps, 'label' => 'Remove Unnecessary Apps', 'remarks' => $pm->remove_unnecessary_apps_remarks],
                                        ['done' => $pm->virus_scan, 'label' => 'Virus Scan', 'remarks' => $pm->virus_scan_remarks],
                                        ['done' => $pm->physical_inspection, 'label' => 'Physical Inspection', 'remarks' => $pm->physical_inspection_remarks],
                                        ['done' => $pm->cdp, 'label' => 'CDP', 'remarks' => $pm->cdp_remarks],
                                    ] as $item)
                                        <div class="flex items-start gap-3 rounded-lg border border-slate-100 bg-white px-3 py-2">
                                            @if($item['done'])
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mt-0.5 h-4 w-4 shrink-0 text-slate-300">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                                </svg>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium {{ $item['done'] ? 'text-slate-900' : 'text-slate-400' }}">{{ $item['label'] }}</p>
                                                @if($item['remarks'])
                                                    <p class="mt-0.5 text-xs text-slate-500">{{ $item['remarks'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($pm->overall_remarks)
                                    <div class="mt-4 rounded-lg border border-slate-100 bg-white p-3">
                                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Overall Remarks</p>
                                        <p class="mt-1 text-sm text-slate-700">{{ $pm->overall_remarks }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                No preventive maintenance logs yet.
                <a href="{{ route('preventive-maintenances.create', ['equipment_id' => $equipment->id]) }}" class="ml-1 font-medium text-indigo-600 hover:text-indigo-700">Log the first PM</a>
            </p>
        @endif
    </section>

    {{-- Movement Log --}}
    @if($movementLogs->total() > 0)
        <section class="pmms-card overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Movement Log</h2>
            </div>
            <div class="pmms-table-wrap rounded-none border-0">
                <table class="pmms-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Employee</th>
                            <th>Office</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @foreach($movementLogs as $log)
                            <tr wire:key="log-{{ $log->id }}">
                                <td class="whitespace-nowrap text-slate-500">{{ $log->created_at->format('M d, Y h:i A') }}</td>
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
                                    {{ $log->employeeDisplayName() }}
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
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($movementLogs->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $movementLogs->links() }}
                </div>
            @endif
        </section>
    @endif

    <livewire:equipment.equipment-modal />

    @if($showTransferModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeTransferModal"></div>

            <div class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="mb-6 flex items-start justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Transfer Equipment</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Transfer ownership of {{ $equipment->property_no }} to another employee. This will be recorded in the movement log.
                        </p>
                    </div>
                    <button type="button" wire:click="closeTransferModal" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="transfer" class="space-y-4">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3 text-sm">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Current owner</p>
                        <p class="mt-1 font-medium text-slate-900">
                            {{ $equipment->activeAssignment?->employee?->full_name ?? 'Unassigned' }}
                        </p>
                    </div>

                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Transfer to <span class="text-red-500">*</span></label>
                        <button
                            type="button"
                            @click="open = !open; if (open) $nextTick(() => $refs.transferEmployeeSearch?.focus())"
                            class="pmms-select flex items-center justify-between text-left">
                            <span class="{{ $selectedTransferEmployee ? 'text-slate-800' : 'text-slate-400' }} truncate">
                                @if($selectedTransferEmployee)
                                    {{ $selectedTransferEmployee->last_name_first }}
                                    @if($selectedTransferEmployee->ee_sequence_number)
                                        <span class="text-slate-400">({{ $selectedTransferEmployee->ee_sequence_number }})</span>
                                    @endif
                                @else
                                    Select employee
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
                                    x-ref="transferEmployeeSearch"
                                    type="text"
                                    wire:model.live.debounce.200ms="transfer_employee_search"
                                    placeholder="Search by last name, first name, or EE sequence no."
                                    class="pmms-input !py-2">
                            </div>
                            <div class="max-h-56 overflow-y-auto py-1">
                                @forelse($employees as $employee)
                                    <button
                                        type="button"
                                        wire:key="transfer-employee-{{ $employee->id }}"
                                        wire:click="selectTransferEmployee({{ $employee->id }})"
                                        @click="open = false"
                                        class="block w-full px-3 py-2 text-left text-sm hover:bg-indigo-50 {{ (string) $transfer_employee_id === (string) $employee->id ? 'bg-indigo-50 text-indigo-700' : 'text-slate-800' }}">
                                        <span class="font-medium">{{ $employee->last_name_first }}</span>
                                        <span class="text-slate-400"> — {{ $employee->ee_sequence_number }}</span>
                                    </button>
                                @empty
                                    <p class="px-3 py-2 text-sm text-slate-400">No employees found.</p>
                                @endforelse
                            </div>
                        </div>
                        @error('transfer_employee_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium text-slate-700">Transfer Type <span class="text-red-500">*</span></p>
                        <div class="space-y-2 rounded-xl border border-slate-200 px-4 py-3">
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-800">
                                <input type="radio" wire:model.live="transfer_type" value="donation" class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Donation
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-800">
                                <input type="radio" wire:model.live="transfer_type" value="relocate" class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Relocate
                            </label>
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-slate-800">
                                <input type="radio" wire:model.live="transfer_type" value="reassignment" class="h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                Reassignment
                            </label>
                            <label class="flex cursor-pointer items-start gap-2 text-sm text-slate-800">
                                <input type="radio" wire:model.live="transfer_type" value="others" class="mt-2 h-4 w-4 border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="flex min-w-0 flex-1 flex-col gap-1.5">
                                    <span>Others: Specify</span>
                                    @if($transfer_type === 'others')
                                        <input
                                            type="text"
                                            wire:model="transfer_type_other"
                                            class="pmms-input !py-1.5"
                                            placeholder="Specify transfer type">
                                    @endif
                                </span>
                            </label>
                        </div>
                        @error('transfer_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        @error('transfer_type_other') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Reason for Transfer</label>
                        <textarea wire:model="transfer_reason" rows="3" class="pmms-input" placeholder="e.g. Change of accountable officer"></textarea>
                        @error('transfer_reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" wire:click="closeTransferModal" class="pmms-btn-secondary">Cancel</button>
                        <button type="submit" class="pmms-btn-primary">Transfer Ownership</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($showReturnModal && $equipment->activeAssignment)
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
                            Return {{ $equipment->property_no }} to the office.
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-slate-700">Assigned Employee</p>
                        <p class="mt-1 rounded-xl border border-slate-100 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-900">
                            {{ $equipment->activeAssignment->employee?->last_name_first ?? $equipment->activeAssignment->employee?->full_name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Reason for Return <span class="text-red-500">*</span></label>
                        <textarea wire:model="return_reason" rows="3" class="pmms-input" placeholder="Enter the reason for returning this equipment"></textarea>
                        @error('return_reason') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <p class="font-medium">This return is permanent.</p>
                        <p class="mt-1 text-amber-700">The assignment will be closed and recorded in the movement log. This cannot be undone from this screen.</p>
                    </div>
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
