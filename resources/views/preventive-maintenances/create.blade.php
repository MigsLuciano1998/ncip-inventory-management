@extends('layouts.app')

@section('content')
    <x-page-header title="Log Preventive Maintenance" description="Record a preventive maintenance inspection for equipment." />

    <form action="{{ route('preventive-maintenances.store') }}" method="POST" class="mt-6">
        @csrf
        @include('preventive-maintenances._form', [
            'buttonText' => 'Save PM Log',
            'selectedEquipment' => $selectedEquipment ?? null,
        ])
    </form>
@endsection
