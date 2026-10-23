<div>
    @if($printLog && $equipment)
        <x-reports.view-page>
            <x-reports.document title="Property Transfer Report" class="ptr-compact">
                @include('partials.reports.ptr-form')
            </x-reports.document>
        </x-reports.view-page>
    @else
    <div class="space-y-6">
        <a href="{{ route('reports.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Reports
        </a>

        <x-page-header
            title="Property Transfer Report"
            description="View the latest transfers and print a Property Transfer Report."
        />

        <div class="pmms-card p-5">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search by property number or employee..."
                class="pmms-input max-w-xl">
        </div>

        <div class="pmms-card overflow-hidden">
            <div class="pmms-table-wrap rounded-none border-0">
                <table class="pmms-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Property Number</th>
                            <th>From → To</th>
                            <th>Office</th>
                            <th>Reason</th>
                            <th class="text-right">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($transfers as $log)
                            <tr wire:key="ptr-{{ $log->id }}" class="{{ $printLog?->id === $log->id ? 'bg-indigo-50/60' : '' }}">
                                <td class="whitespace-nowrap text-slate-500">{{ $log->created_at->format('M d, Y') }}</td>
                                <td class="font-medium text-slate-900">{{ $log->equipment?->property_no ?? '—' }}</td>
                                <td>{{ $log->employeeDisplayName() }}</td>
                                <td class="text-slate-500">
                                    @if($log->previousOffice)
                                        {{ $log->previousOffice->office_name }} → {{ $log->office?->office_name ?? '—' }}
                                    @else
                                        {{ $log->office?->office_name ?? '—' }}
                                    @endif
                                </td>
                                <td class="max-w-xs truncate text-slate-500" title="{{ $log->remarks }}">{{ $log->remarks ?: '—' }}</td>
                                <td class="text-right">
                                    <button type="button" wire:click="viewLog({{ $log->id }})" class="pmms-btn-primary !px-3 !py-1.5 text-xs">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No transfers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transfers->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $transfers->links() }}
                </div>
            @endif
        </div>
    </div>

    @endif
</div>
