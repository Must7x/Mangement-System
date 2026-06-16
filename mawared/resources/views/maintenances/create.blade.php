@extends('layouts.app')

@section('title', __('pages.maintenances_create'))

@section('content')
    @include('maintenances._form', [
        'title' => __('pages.maintenances_create'),
        'action' => route('maintenances.store'),
        'method' => 'POST',
        'maintenance' => null,
        'assets' => $assets,
        'priorities' => $priorities,
        'statuses' => $statuses,
    ])
@endsection
