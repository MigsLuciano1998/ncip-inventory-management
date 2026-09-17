<div>
    <div class="no-print print:hidden space-y-6">
        <a href="{{ route('reports.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Reports
        </a>

        <x-page-header
            title="PPE In Stations (Found and Missing)"
            description="Track PPE (value above ₱49,999) found or missing at the station."
        />

        <div class="pmms-card p-5 space-y-4">
            <div class="flex flex-wrap items-center gap-4">
                <p class="text-sm font-medium text-slate-700">View:</p>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" wire:model.live="showFound" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Found at station
                </label>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" wire:model.live="showMissing" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    Missing at station
                </label>
            </div>

            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search equipment..."
                    class="pmms-input w-full sm:max-w-md">
                <button type="button" onclick="window.print()" class="pmms-btn-primary shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0H6.34m0-8.571h11.318M6.34 9.429V6.75A2.25 2.25 0 0 1 8.59 4.5h6.82A2.25 2.25 0 0 1 17.66 6.75v2.679" />
                    </svg>
                    Print
                </button>
            </div>
        </div>

        <div class="pmms-card overflow-hidden">
            <div class="pmms-table-wrap rounded-none border-0">
                <table class="pmms-table">
                    <thead>
                        <tr>
                            <th>Article / Item</th>
                            <th>Description</th>
                            <th>New Property No.</th>
                            <th>Person Accountable</th>
                            <th class="text-right">Unit Cost/Value</th>
                            <th class="text-right">Total Cost/Value</th>
                            <th>Remarks</th>
                            <th class="text-center">Station</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($equipments as $equipment)
                            @php
                                $amount = \App\Models\Equipment::parseCostAmount($equipment->cost);
                                $costDisplay = $amount === null ? '—' : number_format($amount, 2);
                                $found = $equipment->isFoundAtStation();
                            @endphp
                            <tr wire:key="ppe-station-{{ $equipment->id }}">
                                <td class="font-medium text-slate-900">
                                    {{ $equipment->equipmentType?->name ?? $equipment->ppeCategory?->title ?? $equipment->equipmentCategory?->short_name ?? '—' }}
                                </td>
                                <td class="max-w-xs text-slate-600">
                                    {{ $equipment->description ?: trim(($equipment->brand ?? '').' '.($equipment->model ?? '')) ?: '—' }}
                                </td>
                                <td class="font-medium text-slate-900">{{ $equipment->property_no }}</td>
                                <td class="text-slate-600">
                                    {{ $equipment->activeAssignment?->employee?->last_name_first ?? '—' }}
                                </td>
                                <td class="text-right tabular-nums">{{ $costDisplay }}</td>
                                <td class="text-right tabular-nums">{{ $costDisplay }}</td>
                                <td class="max-w-xs truncate text-slate-500" title="{{ $equipment->remarks }}">{{ $equipment->remarks ?: '—' }}</td>
                                <td class="text-center">
                                    <div class="inline-flex items-center gap-1">
                                        <button
                                            type="button"
                                            wire:click="markStation({{ $equipment->id }}, 'found')"
                                            class="rounded-md px-2 py-1 text-xs font-semibold {{ $found ? 'bg-emerald-100 text-emerald-800 ring-1 ring-emerald-600/20' : 'bg-slate-50 text-slate-500 ring-1 ring-slate-200 hover:bg-emerald-50' }}">
                                            Found
                                        </button>
                                        <button
                                            type="button"
                                            wire:click="markStation({{ $equipment->id }}, 'missing')"
                                            class="rounded-md px-2 py-1 text-xs font-semibold {{ ! $found ? 'bg-rose-100 text-rose-800 ring-1 ring-rose-600/20' : 'bg-slate-50 text-slate-500 ring-1 ring-slate-200 hover:bg-rose-50' }}">
                                            Missing
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">No PPE equipment found for the selected view.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($equipments->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $equipments->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="hidden print:block">
        <x-reports.document title="PPE Equipment in Station">
            <p class="mb-3 text-xs">
                View:
                @if($showFound && $showMissing)
                    Found at station and Missing at station
                @elseif($showFound)
                    Found at station
                @elseif($showMissing)
                    Missing at station
                @else
                    None
                @endif
                @if(trim($search) !== '')
                    · Search: {{ $search }}
                @endif
            </p>

            <table class="w-full border-collapse text-[10px] leading-tight">
                <thead>
                    <tr>
                        <th class="border border-black px-1 py-1 text-left">Article / Item</th>
                        <th class="border border-black px-1 py-1 text-left">Description</th>
                        <th class="border border-black px-1 py-1 text-left">New Property No.</th>
                        <th class="border border-black px-1 py-1 text-left">Person Accountable</th>
                        <th class="border border-black px-1 py-1 text-right">Unit Cost/Value</th>
                        <th class="border border-black px-1 py-1 text-right">Total Cost/Value</th>
                        <th class="border border-black px-1 py-1 text-left">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($printItems as $equipment)
                        @php
                            $amount = \App\Models\Equipment::parseCostAmount($equipment->cost);
                            $costDisplay = $amount === null ? '—' : number_format($amount, 2);
                            $stationRemark = $equipment->isFoundAtStation() ? 'Found at station' : 'Missing at station';
                            $remarks = trim(implode(' · ', array_filter([
                                $equipment->remarks,
                                $stationRemark,
                                $equipment->stationRecord?->dates?->format('M d, Y'),
                            ])));
                        @endphp
                        <tr>
                            <td class="border border-black px-1 py-1">{{ $equipment->equipmentType?->name ?? $equipment->ppeCategory?->title ?? $equipment->equipmentCategory?->short_name ?? '—' }}</td>
                            <td class="border border-black px-1 py-1">{{ $equipment->description ?: trim(($equipment->brand ?? '').' '.($equipment->model ?? '')) ?: '—' }}</td>
                            <td class="border border-black px-1 py-1">{{ $equipment->property_no }}</td>
                            <td class="border border-black px-1 py-1">{{ $equipment->activeAssignment?->employee?->last_name_first ?? '—' }}</td>
                            <td class="border border-black px-1 py-1 text-right">{{ $costDisplay }}</td>
                            <td class="border border-black px-1 py-1 text-right">{{ $costDisplay }}</td>
                            <td class="border border-black px-1 py-1">{{ $remarks ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-black px-1 py-2 text-center">No PPE equipment found for the selected view.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-reports.document>
    </div>
</div>
