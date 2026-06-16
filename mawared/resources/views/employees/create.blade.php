@extends('layouts.app')

@section('title', __('pages.employees_create'))

@section('content')
    @include('employees._form', [
        'title' => __('pages.employees_create'),
        'action' => route('employees.store'),
        'method' => 'POST',
        'employee' => null,
    ])
@endsection
