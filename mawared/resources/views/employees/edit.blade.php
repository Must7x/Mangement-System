@extends('layouts.app')

@section('title', __('pages.employees_edit'))

@section('content')
    @include('employees._form', [
        'title' => __('pages.employees_edit'),
        'action' => route('employees.update', $employee),
        'method' => 'PUT',
        'employee' => $employee,
    ])
@endsection
