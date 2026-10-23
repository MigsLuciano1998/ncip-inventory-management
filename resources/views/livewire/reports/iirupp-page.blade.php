<div>
    <style>
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }
        }
    </style>

    @if($viewRecord)
        <x-reports.view-page>
            <x-reports.document title="Inventory and Inspection Report of Unserviceable PPE Property" class="ptr-compact">
                @include('partials.reports.iirupp-form', ['showTitle' => false])
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
            title="Inventory and Inspection Report of Unserviceable PPE Property (IIRUPP)"
            description="Review IIRUPP records logged for equipment."
        />

        <div class="pmms-card p-5">
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search property number, description, serial, brand..."
                class="pmms-input w-full sm:max-w-md">
        </div>

        <div class="pmms-card overflow-hidden">
            <div class="pmms-table-wrap rounded-none border-0">
                <table class="pmms-table">
                    <thead>
                        <tr>
                            <th>Property No.</th>
                            <th>Description</th>
                            <th>Book Value</th>
                            <th>Status of Depreciation</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($records as $record)
                            <tr wire:key="iirupp-{{ $record->id }}">
                                <td class="font-medium text-slate-900">{{ $record->equipment?->property_no ?? '—' }}</td>
                                <td class="max-w-xs truncate text-slate-600">{{ $record->equipment?->description ?: '—' }}</td>
                                <td>{{ $record->formattedAmount('book_value') }}</td>
                                <td>{{ $record->status_of_depreciation ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <button type="button" wire:click="viewRecord({{ $record->id }})" class="pmms-btn-secondary !px-3 !py-1.5 !text-xs">
                                            View
                                        </button>
                                        @if($record->equipment)
                                            <a href="{{ route('equipment.show', $record->equipment) }}" class="pmms-btn-warning !px-3 !py-1.5 !text-xs">
                                                Equipment
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-slate-500">No IIRUPP records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($records->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>

    @endif
</div>
