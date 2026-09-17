@props(['label', 'value', 'color' => 'indigo'])

@php
    $colors = [
        'indigo' => 'bg-indigo-50 text-indigo-600 ring-indigo-600/10',
        'emerald' => 'bg-emerald-50 text-emerald-600 ring-emerald-600/10',
        'amber' => 'bg-amber-50 text-amber-600 ring-amber-600/10',
        'rose' => 'bg-rose-50 text-rose-600 ring-rose-600/10',
    ];
    $iconClass = $colors[$color] ?? $colors['indigo'];
@endphp

<div class="pmms-card p-6">
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900">{{ $value }}</p>
        </div>

        @if(isset($icon))
            <div class="flex h-11 w-11 items-center justify-center rounded-xl ring-1 {{ $iconClass }}">
                {{ $icon }}
            </div>
        @endif
    </div>
</div>
