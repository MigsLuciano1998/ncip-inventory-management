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
    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="bg-slate-50">

<div class="flex min-h-screen">

    @include('partials.sidebar')

    <div class="flex min-w-0 flex-1 flex-col">

        @include('partials.header')

        <main class="flex-1 p-6 lg:p-8 print:p-0">
            @yield('content')
        </main>

    </div>

</div>

@livewireScripts
</body>
</html>
