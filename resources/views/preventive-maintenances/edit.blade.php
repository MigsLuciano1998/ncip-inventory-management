@extends('layouts.app')

@section('content')
    <x-page-header title="Edit PM Log" description="Update the preventive maintenance record." />

    <form action="{{ route('preventive-maintenances.update', $preventiveMaintenance) }}" method="POST" class="mt-6">
        @csrf
        @method('PUT')
        @include('preventive-maintenances._form', [
            'buttonText' => 'Update PM Log',
            'preventiveMaintenance' => $preventiveMaintenance,
            'selectedEquipment' => $selectedEquipment ?? null,
        ])
    </form>
@endsection
