@extends('layouts.app')

@section('title', __('pages.departments_edit'))

@section('content')
    @include('departments._form', [
        'action' => route('departments.update', $department),
        'method' => 'PUT',
        'title' => __('pages.departments_edit'),
        'department' => $department,
    ])
@endsection
