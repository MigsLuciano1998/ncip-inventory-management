@extends('layouts.app')

@section('content')

<x-page-header title="Edit Employee" description="Update employee details."></x-page-header>

<form action="{{ route('employees.update', $employee) }}" method="POST" class="mt-6">
    @csrf
    @method('PUT')
    @include('employees._form', ['buttonText' => 'Update Employee'])
</form>

@endsection
