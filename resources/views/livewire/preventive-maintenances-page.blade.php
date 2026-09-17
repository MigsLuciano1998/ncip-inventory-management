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

    <x-page-header title="Preventive Maintenances" description="Log and review preventive maintenance activities.">
        <x-slot:actions>
            <a href="{{ route('preventive-maintenances.create') }}" class="pmms-btn-primary inline-flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Log PM
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="pmms-card p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search by property no., serial, brand..."
                    class="pmms-input !pl-10">
            </div>

            <select wire:model.live="statusFilter" class="pmms-select">
                <option value="">All Statuses</option>
                <option value="Serviceable">Serviceable</option>
                <option value="Unserviceable">Unserviceable</option>
            </select>
        </div>
    </div>

    <div class="pmms-card overflow-hidden">
        <div class="pmms-table-wrap rounded-none border-0">
            <table class="pmms-table">
                <thead>
                    <tr>
                        <th>Date Inspected</th>
                        <th>Property No.</th>
                        <th>Equipment Type</th>
                        <th>Office</th>
                        <th class="text-center">Checklist</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($pmLogs as $pm)
                        <tr
                            wire:key="pm-{{ $pm->id }}"
                            wire:click="viewPm({{ $pm->id }})"
                            class="cursor-pointer {{ $selectedPmId === $pm->id ? 'bg-indigo-50/60' : 'hover:bg-slate-50' }}">
                            <td class="whitespace-nowrap text-slate-500">{{ $pm->date_inspected->format('M d, Y') }}</td>
                            <td class="font-medium text-slate-900">{{ $pm->equipment?->property_no ?? '—' }}</td>
                            <td>{{ $pm->equipment?->equipmentType?->name ?? '—' }}</td>
                            <td class="text-slate-500">{{ $pm->equipment?->office?->office_name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="pmms-badge bg-slate-50 text-slate-700 ring-1 ring-slate-600/10">
                                    {{ $pm->checklistCompletedCount() }}/6
                                </span>
                            </td>
                            <td class="text-center">
                                @if($pm->isServiceable())
                                    <span class="pmms-badge-success">Serviceable</span>
                                @else
                                    <span class="pmms-badge-danger">Unserviceable</span>
                                @endif
                            </td>
                            <td class="text-center" wire:click.stop>
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('preventive-maintenances.edit', $pm) }}" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="delete({{ $pm->id }})"
                                        wire:confirm="Are you sure you want to delete this PM log?"
                                        class="pmms-btn-danger !px-3 !py-1.5 !text-xs">
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-slate-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
                                    </svg>
                                    <span>No PM logs found. Click "Log PM" to record maintenance.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pmLogs->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $pmLogs->links() }}
            </div>
        @endif
    </div>

    @if($selectedPm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closePmDetail"></div>

            <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">Preventive Maintenance Information</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ $selectedPm->equipment?->property_no ?? 'Equipment' }}
                            · {{ $selectedPm->date_inspected->format('F d, Y') }}
                        </p>
                    </div>
                    <button type="button" wire:click="closePmDetail" class="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Property No.</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedPm->equipment?->property_no ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Equipment Type</p>
                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $selectedPm->equipment?->equipmentType?->name ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Office</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $selectedPm->equipment?->office?->office_name ?? '—' }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Date Inspected</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $selectedPm->date_inspected->format('F d, Y') }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Overall Status</p>
                        <div class="mt-1">
                            @if($selectedPm->isServiceable())
                                <span class="pmms-badge-success">Serviceable</span>
                            @else
                                <span class="pmms-badge-danger">Unserviceable</span>
                            @endif
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Checklist</p>
                        <p class="mt-1 text-sm font-medium text-slate-900">{{ $selectedPm->checklistCompletedCount() }}/6 completed</p>
                    </div>
                </div>

                @if($selectedPm->unserviceable_reason)
                    <div class="mt-4 rounded-xl border border-rose-100 bg-rose-50 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-rose-500">Unserviceable Reason</p>
                        <p class="mt-1 text-sm text-rose-800">{{ $selectedPm->unserviceable_reason }}</p>
                    </div>
                @endif

                <div class="mt-5">
                    <p class="mb-2 text-sm font-semibold text-slate-800">Checklist</p>
                    <div class="space-y-2">
                        @foreach([
                            ['done' => $selectedPm->windows_update, 'label' => 'Windows Update', 'remarks' => $selectedPm->windows_update_remarks],
                            ['done' => $selectedPm->remove_unnecessary_apps, 'label' => 'Remove Unnecessary Apps', 'remarks' => $selectedPm->remove_unnecessary_apps_remarks],
                            ['done' => $selectedPm->health_check_diagnosis, 'label' => 'Health Check Diagnosis', 'remarks' => $selectedPm->health_check_diagnosis_remarks],
                            ['done' => $selectedPm->virus_scan, 'label' => 'Virus Scan', 'remarks' => $selectedPm->virus_scan_remarks],
                            ['done' => $selectedPm->physical_inspection, 'label' => 'Physical Inspection', 'remarks' => $selectedPm->physical_inspection_remarks],
                            ['done' => $selectedPm->cdp, 'label' => 'CDP (Cleaning, dusting, and polishing)', 'remarks' => $selectedPm->cdp_remarks],
                        ] as $item)
                            <div class="flex items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-3 py-2.5">
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
                </div>

                @if($selectedPm->overall_remarks)
                    <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50/80 p-4">
                        <p class="text-xs font-medium uppercase tracking-wide text-slate-400">Overall Remarks</p>
                        <p class="mt-1 text-sm text-slate-700">{{ $selectedPm->overall_remarks }}</p>
                    </div>
                @endif

                <div class="mt-6 flex justify-end gap-2">
                    <a href="{{ route('preventive-maintenances.edit', $selectedPm) }}" class="pmms-btn-warning">Edit</a>
                    <button type="button" wire:click="closePmDetail" class="pmms-btn-secondary">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
