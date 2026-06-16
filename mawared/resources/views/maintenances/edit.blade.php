@extends('layouts.app')

@section('title', __('pages.maintenances_edit'))

@section('content')
    @include('maintenances._form', [
        'title' => __('pages.maintenances_edit'),
        'action' => route('maintenances.update', $maintenance),
        'method' => 'PUT',
        'maintenance' => $maintenance,
        'assets' => collect(),
        'priorities' => $priorities,
        'statuses' => $statuses,
    ])
@endsection
