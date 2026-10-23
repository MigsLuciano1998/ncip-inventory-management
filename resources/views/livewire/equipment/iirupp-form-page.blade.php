<div class="space-y-6">
    <div>
        <a href="{{ route('equipment.show', $equipment) }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to Equipment
        </a>
        <x-page-header
            title="Log IIRUPP"
            description="Inventory and Inspection Report of Unserviceable PPE Property for {{ $equipment->property_no }}."
        />
    </div>

    <section class="pmms-card p-6">
        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-slate-400">Equipment</h2>
        <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Property No.</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->property_no }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Type</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->equipmentType?->name ?? '—' }}</dd>
            </div>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4">
                <dt class="text-xs font-medium text-slate-400">Office</dt>
                <dd class="mt-1 text-sm font-medium text-slate-900">{{ $equipment->office?->office_name ?? '—' }}</dd>
            </div>
        </dl>
    </section>

    <form wire:submit="save" class="pmms-card space-y-6 p-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Depreciation details</h2>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Accumulated Depreciation (Beginning)</label>
                <input type="number" step="0.01" min="0" wire:model="accumulated_depreciation_beginning" class="pmms-input" placeholder="0.00">
                @error('accumulated_depreciation_beginning') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Accumulated Depreciation (End)</label>
                <input type="number" step="0.01" min="0" wire:model="accumulated_depreciation_end" class="pmms-input" placeholder="0.00">
                @error('accumulated_depreciation_end') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Book Value</label>
                <input type="number" step="0.01" min="0" wire:model="book_value" class="pmms-input" placeholder="0.00">
                @error('book_value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Repair Cost</label>
                <input type="number" step="0.01" min="0" wire:model="repair_cost" class="pmms-input" placeholder="0.00">
                @error('repair_cost') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Nature of Repair</label>
                <textarea wire:model="nature_of_repair" rows="3" class="pmms-input" placeholder="Describe the nature of repair"></textarea>
                @error('nature_of_repair') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Authority</label>
                <input type="text" wire:model="authority" class="pmms-input" placeholder="Authority">
                @error('authority') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Use of Depreciation Date</label>
                <input type="date" wire:model="use_of_depreciation_date" class="pmms-input">
                @error('use_of_depreciation_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Salvage Value</label>
                <input type="number" step="0.01" min="0" wire:model="salvage_value" class="pmms-input" placeholder="0.00">
                @error('salvage_value') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700">Start Date of Depreciation</label>
                <input type="date" wire:model="start_date_of_depreciation" class="pmms-input">
                @error('start_date_of_depreciation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="mb-2 block text-sm font-medium text-slate-700">Status of Depreciation</label>
                <input type="text" wire:model="status_of_depreciation" class="pmms-input" placeholder="e.g. Fully depreciated">
                @error('status_of_depreciation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('equipment.show', $equipment) }}" class="pmms-btn-secondary">Cancel</a>
            <button type="submit" class="pmms-btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Save IIRUPP</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
