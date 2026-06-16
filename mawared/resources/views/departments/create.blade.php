@extends('layouts.app')

@section('title', 'إضافة قسم جديد')

@section('content')
    @include('departments._form', [
        'action' => route('departments.store'),
        'method' => 'POST',
        'title' => 'إضافة قسم جديد',
        'department' => null,
    ])
@endsection
