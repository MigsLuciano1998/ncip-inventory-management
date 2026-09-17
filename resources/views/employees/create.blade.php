@extends('layouts.app')

@section('content')

<x-page-header title="Add Employee" description="Create a new employee record."></x-page-header>

<form action="{{ route('employees.store') }}" method="POST" class="mt-6">
    @csrf
    @include('employees._form', ['buttonText' => 'Create Employee'])
</form>

@endsection
