@props(['backMethod' => 'closeView'])

<div>
    <div class="no-print print:hidden mb-4 flex flex-wrap items-center justify-between gap-3">
        <button type="button" wire:click="{{ $backMethod }}" class="pmms-btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back
        </button>
        <button type="button" onclick="window.print()" class="pmms-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0H6.34m0-8.571h11.318M6.34 9.429V6.75A2.25 2.25 0 0 1 8.59 4.5h6.82A2.25 2.25 0 0 1 17.66 6.75v2.679" />
            </svg>
            Print
        </button>
    </div>

    <div class="print-report overflow-x-auto rounded-2xl border border-slate-200 bg-white p-6 shadow-sm print:overflow-visible print:rounded-none print:border-0 print:bg-white print:p-0 print:shadow-none">
        {{ $slot }}
    </div>
</div>
