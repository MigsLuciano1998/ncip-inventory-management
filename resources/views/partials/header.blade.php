@php
    $pageTitles = [
        'dashboard' => 'Dashboard',
        'regions.index' => 'Regions',
        'offices.index' => 'Offices',
        'employees.index' => 'Employees',
        'equipment-categories.index' => 'Equipment Categories',
        'equipment-types.index' => 'Equipment Types',
        'equipment.index' => 'Equipment',
        'equipment.show' => 'Equipment Details',
        'equipment-assignments.index' => 'Equipment Assignments',
        'ppe-categories.index' => 'PPE Categories',
        'preventive-maintenances.index' => 'PM Logs',
        'reports.index' => 'Reports',
        'reports.par' => 'Property Acknowledgement Receipt',
        'reports.property-transfer' => 'Property Transfer Report',
        'reports.returned-property' => 'Receipt of Returned Property',
        'reports.ppe-in-stations' => 'PPE In Stations (Found and Missing)',
        'reports.lost-stolen-damaged' => 'Lost, Stolen, Damaged, Or Destroyed PPE Property',
        'reports.property-tagging' => 'Property Tagging',
    ];

    $currentRoute = Route::currentRouteName();
    $pageTitle = $pageTitles[$currentRoute] ?? 'PMMS';
@endphp

<header class="sticky top-0 z-10 border-b border-slate-200/80 bg-white/80 px-6 py-4 backdrop-blur-lg print:hidden lg:px-8">

    <div class="flex items-center justify-between gap-4">

        <div><!-- --></div>

        <div class="flex items-center gap-4">
            <div class="hidden items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 sm:flex">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-slate-400">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                <span>{{ now()->format('F d, Y') }}</span>
            </div>

            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                MP
            </div>
        </div>

    </div>

</header>
