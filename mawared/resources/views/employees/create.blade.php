@extends('layouts.app')

@section('title', 'موظف جديد')

@section('content')
    @include('employees._form', [
        'title' => 'موظف جديد',
        'action' => route('employees.store'),
        'method' => 'POST',
        'employee' => null,
    ])
@endsection
