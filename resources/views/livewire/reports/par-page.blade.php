<div>
    @if($formItems->isNotEmpty())
        <x-reports.view-page>
            <x-reports.document title="Property Acknowledgment Receipt">
                @include('partials.reports.par-form')
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
            title="Property Acknowledgement Receipt (PAR)"
            description="Select an employee and print a Property Acknowledgement Receipt for PPE equipment."
        />

        <div class="pmms-card p-5">
            <div class="flex justify-between flex-col gap-3 sm:flex-row sm:items-end">
                <div class="relative min-w-0 flex-1 max-w-xl">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Search Employee</label>
                    <input
                        type="text"
                        wire:model.live.debounce.200ms="employee_search"
                        placeholder="Search by name or EE sequence number..."
                        class="pmms-input">
                    @if($employees->isNotEmpty() && ! $employee)
                        <div class="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                            @foreach($employees as $suggestion)
                                <button
                                    type="button"
                                    wire:click="selectEmployee({{ $suggestion->id }})"
                                    class="block w-full px-3 py-2 text-left text-sm hover:bg-indigo-50">
                                    <span class="font-medium text-slate-800">{{ $suggestion->last_name_first }}</span>
                                    @if($suggestion->ee_sequence_number)
                                        <span class="text-slate-400"> — {{ $suggestion->ee_sequence_number }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <button
                    type="button"
                    wire:click="printSelected"
                    @disabled(! $canPrint)
                    class="pmms-btn-primary shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0H6.34m0-8.571h11.318M6.34 9.429V6.75A2.25 2.25 0 0 1 8.59 4.5h6.82A2.25 2.25 0 0 1 17.66 6.75v2.679" />
                    </svg>
                    Print
                </button>
            </div>
            @if($employee)
                <p class="mt-3 text-sm text-slate-500">
                    Showing PPE assigned to
                    <span class="font-medium text-slate-700">{{ $employee->last_name_first }}</span>
                    <button type="button" wire:click="selectEmployee(null)" class="ml-2 text-indigo-600 hover:underline">Clear</button>
                </p>
            @endif
        </div>

        <div class="pmms-card overflow-hidden">
            <div class="pmms-table-wrap rounded-none border-0">
                <table class="pmms-table">
                    <thead>
                        <tr>
                            <th class="w-10"></th>
                            <th>Property No</th>
                            <th>Description</th>
                            <th>Serial Number</th>
                            <th>Cost</th>
                            <th class="text-right">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($equipments as $equipment)
                            <tr wire:key="par-equipment-{{ $equipment->id }}" class="{{ $viewEquipment?->id === $equipment->id ? 'bg-indigo-50/60' : '' }}">
                                <td>
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedIds"
                                        value="{{ $equipment->id }}"
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                <td class="font-medium text-slate-900">{{ $equipment->property_no }}</td>
                                <td class="max-w-xs truncate text-slate-600" title="{{ $equipment->description }}">
                                    {{ $equipment->description ?: trim(($equipment->brand ?? '').' '.($equipment->model ?? '')) ?: '—' }}
                                </td>
                                <td class="text-slate-500">{{ $equipment->serial_no ?: '—' }}</td>
                                <td class="text-slate-700">
                                    @php $cost = \App\Models\Equipment::parseCostAmount($equipment->cost); @endphp
                                    {{ $cost === null ? '—' : number_format($cost, 2) }}
                                </td>
                                <td class="text-right">
                                    <button type="button" wire:click="viewEquipment({{ $equipment->id }})" class="pmms-btn-primary !px-3 !py-1.5 text-xs">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    {{ $employee ? 'No PPE assigned to this employee.' : 'Search and select an employee to view assigned PPE.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employee && $equipments->hasPages())
                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $equipments->links() }}
                </div>
            @endif
        </div>
    </div>

    @endif
</div>
