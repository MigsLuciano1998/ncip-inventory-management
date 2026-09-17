<div>
    <div class="no-print print:hidden space-y-6">
        @if(session()->has('message'))
            <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('message') }}
            </div>
        @endif

        <a href="{{ route('reports.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Reports
        </a>

        <x-page-header title="Lost, Stolen, Damaged, Or Destroyed PPE Equipment">
            <x-slot:actions>
                <button type="button" wire:click="openAdd" class="pmms-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add
                </button>
            </x-slot:actions>
        </x-page-header>

        <div class="pmms-card p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search equipment..."
                    class="pmms-input max-w-xl">
                <select wire:model.live="statusFilter" class="pmms-select max-w-xs">
                    <option value="">All statuses</option>
                    @foreach(\App\Models\LostStolenDamagedEquipment::statuses() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button
                    type="button"
                    wire:click="printSelected"
                    @disabled(! $selectionEnabled)
                    class="pmms-btn-primary shrink-0">
                    Print
                </button>
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
                            <th class="w-10"></th>
                            <th>Article</th>
                            <th>Property Number</th>
                            <th>Description</th>
                            <th>Remarks</th>
                            <th class="text-right">View</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse($records as $record)
                            <tr wire:key="lsd-{{ $record->id }}" class="{{ $printRecord?->id === $record->id ? 'bg-indigo-50/60' : '' }}">
                                <td>
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedIds"
                                        value="{{ $record->id }}"
                                        @disabled(! $selectionEnabled)
                                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 disabled:cursor-not-allowed disabled:opacity-50">
                                </td>
                                <td>{{ $record->equipment?->equipmentType?->name ?? $record->equipment?->ppeCategory?->title ?? $record->equipment?->equipmentCategory?->short_name ?? '—' }}</td>
                                <td class="font-medium text-slate-900">{{ $record->equipment?->property_no ?? '—' }}</td>
                                <td class="max-w-xs truncate text-slate-600">
                                    {{ $record->equipment?->description ?: trim(($record->equipment?->brand ?? '').' '.($record->equipment?->model ?? '')) ?: '—' }}
                                </td>
                                <td class="max-w-xs truncate text-slate-500">{{ $record->remarks ?: '—' }}</td>
                                <td class="text-right">
                                    <button type="button" wire:click="viewRecord({{ $record->id }})" class="pmms-btn-primary !px-3 !py-1.5 text-xs">
                                        View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">No lost, stolen, damaged, or destroyed equipment found.</td>
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

    @if($showAddModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 print:hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeAdd"></div>
            <div class="relative w-full max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <h2 class="text-lg font-semibold text-slate-900">Add Lost, Stolen, Damaged, or Destroyed PPE</h2>
                <form wire:submit.prevent="save" class="mt-5 space-y-4">
                    <div class="relative">
                        <label class="mb-2 block text-sm font-medium text-slate-700">Property Number</label>
                        <input
                            type="text"
                            wire:model.live.debounce.200ms="property_search"
                            placeholder="Search property number..."
                            class="pmms-input">
                        @if($selectedEquipment)
                            <p class="mt-1 text-xs text-slate-500">{{ $selectedEquipment->property_no }}</p>
                        @endif
                        @if($suggestions->isNotEmpty())
                            <div class="absolute z-20 mt-1 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg">
                                @foreach($suggestions as $suggestion)
                                    <button
                                        type="button"
                                        wire:click="selectProperty('{{ $suggestion->property_no }}')"
                                        class="block w-full px-3 py-2 text-left text-sm hover:bg-indigo-50">
                                        <span class="font-medium text-slate-800">{{ $suggestion->property_no }}</span>
                                        @if($suggestion->brand || $suggestion->model)
                                            <span class="text-slate-400"> — {{ trim(($suggestion->brand ?? '').' '.($suggestion->model ?? '')) }}</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="space-y-2">
                        @foreach(\App\Models\LostStolenDamagedEquipment::statuses() as $value => $label)
                            <label class="flex items-center gap-2 text-sm text-slate-700">
                                <input type="radio" wire:model="status" value="{{ $value }}" class="border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Remarks</label>
                        <textarea wire:model="remarks" rows="4" class="pmms-input" placeholder="Circumstances or remarks..."></textarea>
                    </div>

                    @if($addError)
                        <p class="text-sm text-rose-600">{{ $addError }}</p>
                    @endif

                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" wire:click="closeAdd" class="pmms-btn-secondary">Cancel</button>
                        <button type="submit" class="pmms-btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if($printRecord && $formData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 print:hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeView"></div>
            <div class="relative max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold text-slate-900">Report of Lost, Stolen, Damaged or Destroyed PPE Property</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="window.print()" class="pmms-btn-primary">Print</button>
                        <button type="button" wire:click="closeView" class="pmms-btn-secondary">Close</button>
                    </div>
                </div>
                <div class="text-black" style="font-family: 'Times New Roman', Times, serif;">
                    <h3 class="mb-2 text-center text-sm font-bold uppercase">Report of Lost, Stolen, Damaged or Destroyed PPE Property</h3>
                    @include('partials.reports.rlsddsp-form', ['formData' => $formData])
                </div>
            </div>
        </div>
    @endif

    @if($printRecord && $formData)
        <div class="hidden print:block">
            <x-reports.document title="Report of Lost, Stolen, Damaged or Destroyed PPE Property" class="ptr-compact">
                @include('partials.reports.rlsddsp-form', ['formData' => $formData])
            </x-reports.document>
        </div>
    @endif
</div>
