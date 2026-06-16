@extends('layouts.app')

@section('title', 'تعديل القسم')

@section('content')
    @include('departments._form', [
        'action' => route('departments.update', $department),
        'method' => 'PUT',
        'title' => 'تعديل بيانات القسم',
        'department' => $department,
    ])
@endsection
