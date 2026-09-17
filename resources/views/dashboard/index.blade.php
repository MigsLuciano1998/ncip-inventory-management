@extends('layouts.app')

@section('content')

<x-page-header
    title="Dashboard"
    description="Overview of equipment and preventive maintenance activity."
/>

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">

    <x-stat-card
        label="Total Equipment"
        :value="number_format($stats['totalEquipment'])"
        color="indigo"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25" />
            </svg>
        </x-slot:icon>
    </x-stat-card>

    <x-stat-card
        label="PM This Month"
        :value="number_format($stats['pmThisMonth'])"
        color="emerald"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />
            </svg>
        </x-slot:icon>
    </x-stat-card>

    <x-stat-card
        label="Unserviceable"
        :value="number_format($stats['unserviceable'])"
        color="rose"
    >
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </x-slot:icon>
    </x-stat-card>

</div>

<div class="mt-6 pmms-card p-6">
    <h3 class="text-base font-semibold text-slate-900">Quick Actions</h3>
    <p class="mt-1 text-sm text-slate-500">Jump to frequently used sections.</p>

    <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('equipment.index') }}" class="group rounded-xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/50">
            <p class="font-medium text-slate-900 group-hover:text-indigo-700">Manage Equipment</p>
            <p class="mt-1 text-xs text-slate-500">View and register ICT equipment</p>
        </a>
        <a href="{{ route('preventive-maintenances.index') }}" class="group rounded-xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/50">
            <p class="font-medium text-slate-900 group-hover:text-indigo-700">PM Logs</p>
            <p class="mt-1 text-xs text-slate-500">Track maintenance records</p>
        </a>
        <a href="{{ route('offices.index') }}" class="group rounded-xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/50">
            <p class="font-medium text-slate-900 group-hover:text-indigo-700">Offices</p>
            <p class="mt-1 text-xs text-slate-500">Browse office locations</p>
        </a>
        <a href="{{ route('reports.index') }}" class="group rounded-xl border border-slate-200 p-4 transition hover:border-indigo-200 hover:bg-indigo-50/50">
            <p class="font-medium text-slate-900 group-hover:text-indigo-700">Reports</p>
            <p class="mt-1 text-xs text-slate-500">Generate maintenance reports</p>
        </a>
    </div>
</div>

@endsection
