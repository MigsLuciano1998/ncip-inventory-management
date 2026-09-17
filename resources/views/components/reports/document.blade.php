@props(['title' => null])

<div {{ $attributes->class('report-document par-document mx-auto flex min-h-[257mm] w-full max-w-[8.5in] flex-col bg-white p-6 text-black shadow-sm print:min-h-[273mm] print:p-0 print:shadow-none') }} style="font-family: 'Times New Roman', Times, serif;">
    <div class="report-body par-body flex-1">
        @include('partials.reports.header', ['title' => $title])
        {{ $slot }}
    </div>
    @include('partials.reports.footer')
</div>
