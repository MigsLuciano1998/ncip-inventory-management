<div>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 5mm;
            }

            .property-tag-sheet {
                display: flex;
                flex-wrap: wrap;
                align-items: flex-start;
                column-gap: 4mm;
                row-gap: 4mm;
            }

            .property-tag {
                width: 9.5cm;
                max-width: 9.5cm;
                height: auto;
                overflow: visible;
                break-inside: avoid;
                page-break-inside: avoid;
                font-family: Calibri, 'Carlito', 'Segoe UI', sans-serif;
                font-size: 11px;
            }

            .property-tag table,
            .property-tag p {
                font-family: Calibri, 'Carlito', 'Segoe UI', sans-serif;
                font-size: 11px;
                line-height: 1.2;
            }
        }
    </style>

    <div class="no-print print:hidden space-y-6">
        <a href="{{ route('reports.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Reports
        </a>

        <x-page-header
            title="Property Tagging"
            description="Filter equipment, select tags, and print stickers.">
            <x-slot:actions>
                <button type="button" wire:click="printSelected" class="pmms-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0H6.34m0-8.571h11.318M6.34 9.429V6.75A2.25 2.25 0 0 1 8.59 4.5h6.82A2.25 2.25 0 0 1 17.66 6.75v2.679" />
                    </svg>
                    Print
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="pmms-card p-5">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search property, tag, serial, brand..."
                        class="pmms-input !pl-10">
                </div>

                <select wire:model.live="selectedRegion" class="pmms-select">
                    <option value="">All Regions</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">{{ $region->region_short_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedProvince" class="pmms-select">
                    <option value="">All Provinces</option>
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}">{{ $province->province_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedOffice" class="pmms-select">
                    <option value="">All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}">{{ $office->office_name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="selectedClassification" class="pmms-select">
                    <option value="">All Classifications</option>
                    <option value="ppe">PPE</option>
                    <option value="semi_hv">Semi-HV</option>
                    <option value="lv">LV</option>
                </select>
            </div>
            @if($printError)
                <p class="mt-3 text-sm text-rose-600">{{ $printError }}</p>
            @endif
        </div>

        <div class="pmms-card overflow-hidden">
            <div class="pmms-table-wrap rounded-none border-0">
                <table class="pmms-table">
                    <thead>
                        <tr>
                            <th class="w-28">
                                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600">
                                    <input
                                        type="checkbox"
                                        wire:model.live="checkAll"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    Check all
                                </label>
                            </th>
                            <th>Property No.</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Brand / Model</th>
                            <th>Office</th>
                            <th>Assignee</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($equipments as $equipment)
                            <tr wire:key="tag-equipment-{{ $equipment->id }}">
                                <td>
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedIds"
                                        value="{{ $equipment->id }}"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="font-medium text-slate-900">{{ $equipment->property_no }}</td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5">
                                        {{ $equipment->equipmentCategory?->short_name ?? '—' }}
                                        @if($equipment->classification)
                                            <span class="inline-flex items-center rounded-md px-1.5 py-0.5 text-xs font-semibold {{ $equipment->classificationBadgeClasses() }}">
                                                {{ $equipment->classificationLabel() }}
                                            </span>
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $equipment->equipmentType?->name ?? '—' }}</td>
                                <td>
                                    <span class="text-slate-900">{{ $equipment->brand ?? '—' }}</span>
                                    @if($equipment->model)
                                        <span class="text-slate-400"> / {{ $equipment->model }}</span>
                                    @endif
                                </td>
                                <td class="text-slate-500">{{ $equipment->office?->office_name ?? '—' }}</td>
                                <td class="text-slate-500">
                                    @if($equipment->activeAssignment)
                                        <span class="font-medium text-slate-700">{{ $equipment->activeAssignment->employee?->full_name ?? '—' }}</span>
                                    @else
                                        <span class="text-slate-400">Unassigned</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($equipment->status)
                                        <span class="pmms-badge-success">Serviceable</span>
                                    @else
                                        <span class="pmms-badge-danger">Unserviceable</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">
                                    No equipment found for the selected filters.
                                </td>
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

    @if($printEquipment->isNotEmpty())
        <div class="property-tag-sheet hidden print:block text-black">
            @foreach($printEquipment as $equipment)
                @php
                    $description = trim((string) $equipment->description);
                    if ($description === '') {
                        $description = trim(implode(' ', array_filter([
                            $equipment->brand,
                            $equipment->model,
                            $equipment->equipmentType?->name,
                        ])));
                    }

                    $cost = \App\Models\Equipment::parseCostAmount($equipment->cost);
                    $officeName = $equipment->office?->office_name;
                    $provinceName = $equipment->office?->province?->province_name;
                    $officeLine = trim(implode(',', array_filter([$officeName, $provinceName])));
                @endphp
                <div class="property-tag">
                    @include('partials.reports.property-tag', [
                        'equipment' => $equipment,
                        'description' => $description !== '' ? $description : '',
                        'seriesNo' => $equipment->series_number !== null ? str_pad((string) $equipment->series_number, 4, '0', STR_PAD_LEFT) : '',
                        'accountablePerson' => $equipment->activeAssignment?->employee?->last_name_first ?? '',
                        'officeLine' => $officeLine,
                        'acquisitionCost' => $cost === null ? '' : number_format($cost, 2),
                        'acquisitionDate' => ($equipment->date_acquired ?? $equipment->date_purchased)?->format('n/j/y') ?? '',
                    ])
                </div>
            @endforeach
        </div>
    @endif
</div>
