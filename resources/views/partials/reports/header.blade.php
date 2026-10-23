{{--
    Official NCIP letterhead for printable reports.

    @include('partials.reports.header', ['title' => 'Property Acknowledgment Receipt'])
--}}
@php
    $logoPath = public_path('images/logo.png');
    $letterheadLogo = asset('images/logo.png');
    if (is_file($logoPath)) {
        $letterheadLogo .= '?v='.filemtime($logoPath);
    }
@endphp
<div class="report-header text-center" style="font-family: 'Times New Roman', Times, serif;">
    <div class="mx-auto mb-2 flex h-20 w-20 items-center justify-center">
        <img src="{{ $letterheadLogo }}" alt="National Commission on Indigenous Peoples" class="h-20 w-20 object-contain">
    </div>
    <p class="text-[11px] font-semibold uppercase leading-tight tracking-wide">Republic of the Philippines</p>
    <p class="text-[11px] font-semibold uppercase leading-tight tracking-wide">Office of the President</p>
    <p class="text-[12px] font-bold uppercase leading-tight tracking-wide">National Commission on Indigenous Peoples</p>
    <div class="mx-auto mt-2 mb-4 w-72 border-t border-black"></div>
    @if(! empty($title))
        <h1 class="mt-3 mb-3 text-[15px] font-bold uppercase tracking-wide">{{ $title }}</h1>
    @endif
</div>
