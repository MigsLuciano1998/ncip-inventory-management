@props(['title' => null])

{{-- Letterhead document. thead/tfoot stay visible on screen and repeat on each printed page. --}}
<table {{ $attributes->class('report-document par-document report-print-table w-full border-collapse bg-white text-black print:max-w-none') }} style="font-family: 'Times New Roman', Times, serif;">
    <thead>
        <tr>
            <td class="report-header-cell p-0 align-bottom">
                @include('partials.reports.header', ['title' => $title])
            </td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="report-body par-body p-0 align-top">
                {{ $slot }}
            </td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td class="report-footer-cell p-0 align-top">
                @include('partials.reports.footer')
            </td>
        </tr>
    </tfoot>
</table>
