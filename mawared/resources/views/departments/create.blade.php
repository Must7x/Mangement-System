@extends('layouts.app')

@section('title', __('pages.departments_create'))

@section('content')
    @include('departments._form', [
        'action' => route('departments.store'),
        'method' => 'POST',
        'title' => __('pages.departments_create'),
        'department' => null,
    ])
@endsection
