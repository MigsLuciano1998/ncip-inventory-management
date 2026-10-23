<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PMMS') — Preventive Maintenance Management System</title>
    <link rel="icon" href="{{ asset('images/logo.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('images/logo.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        [x-cloak]{display:none!important}
        @media print {
            html, body { height: auto !important; overflow: visible !important; background: #fff !important; }
            .no-print, .print\:hidden { display: none !important; }
            .print-report, .print\:block { display: block !important; }
            .report-print-table { display: table !important; width: 100% !important; border-collapse: collapse !important; }
            .report-print-table thead { display: table-header-group !important; }
            .report-print-table tfoot { display: table-footer-group !important; }
            .report-print-table tbody { display: table-row-group !important; }
            .report-header img, .report-footer img { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>

<body class="bg-slate-50">

<div class="flex min-h-screen print:block print:h-auto print:min-h-0 print:overflow-visible">

    @include('partials.sidebar')

    <div class="flex min-w-0 flex-1 flex-col print:block print:h-auto print:min-h-0 print:overflow-visible">

        @include('partials.header')

        <main class="flex-1 p-6 lg:p-8 print:p-0 print:overflow-visible">
            @yield('content')
        </main>

    </div>

</div>

@livewireScripts
</body>
</html>
