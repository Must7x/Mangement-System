@extends('layouts.app')

@section('title', 'تعديل موظف')

@section('content')
    @include('employees._form', [
        'title' => 'تعديل موظف',
        'action' => route('employees.update', $employee),
        'method' => 'PUT',
        'employee' => $employee,
    ])
@endsection
