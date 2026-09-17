<div>
    <div class="no-print print:hidden space-y-6">
        <a href="{{ route('reports.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Reports
        </a>

        <x-page-header
            title="Property Acknowledgement Receipt (PAR)"
            description="View the latest acknowledgements and print a Property Acknowledgement Receipt."
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
                            <th>Description</th>
                            <th>Received by</th>
                            <th>Office</th>
                            <th class="text-right">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($acknowledgements as $log)
                            <tr wire:key="par-{{ $log->id }}" class="{{ $printLog?->id === $log->id ? 'bg-indigo-50/60' : '' }}">
                                <td class="whitespace-nowrap text-slate-500">{{ $log->created_at->format('M d, Y') }}</td>
                                <td class="font-medium text-slate-900">{{ $log->equipment?->property_no ?? '—' }}</td>
                                <td class="max-w-xs truncate text-slate-600" title="{{ $log->equipment?->description }}">
                                    {{ $log->equipment?->description ?: trim(($log->equipment?->brand ?? '').' '.($log->equipment?->model ?? '')) ?: '—' }}
                                </td>
                                <td>{{ $log->assignment?->employee?->last_name_first ?? $log->owner_name ?? '—' }}</td>
                                <td class="text-slate-500">{{ $log->office?->office_name ?? $log->assignment?->office?->office_name ?? '—' }}</td>
                                <td class="text-right">
                                    <button type="button" wire:click="viewLog({{ $log->id }})" class="pmms-btn-primary !px-3 !py-1.5 text-xs">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No acknowledgements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($acknowledgements->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $acknowledgements->links() }}
                </div>
            @endif
        </div>
    </div>

    @if($printLog && $equipment)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 print:hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeView"></div>
            <div class="relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-slate-900">Property Acknowledgement Receipt</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="pmms-btn-primary">Print</button>
                        <button type="button" wire:click="closeView" class="pmms-btn-secondary">Close</button>
                    </div>
                </div>
                <div class="text-black" style="font-family: 'Times New Roman', Times, serif;">
                    <h3 class="mb-2 text-center text-sm font-bold uppercase">Property Acknowledgment Receipt</h3>
                    @include('partials.reports.par-form')
                </div>
            </div>
        </div>

        <div class="hidden print:block">
            <x-reports.document title="Property Acknowledgment Receipt">
                @include('partials.reports.par-form')
            </x-reports.document>
        </div>
    @endif
</div>
