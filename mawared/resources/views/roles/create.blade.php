@extends('layouts.app')

@section('title', __('pages.roles_create'))

@section('content')
    @include('roles._form', [
        'title' => __('pages.roles_create'),
        'action' => route('roles.store'),
        'method' => 'POST',
        'role' => $role,
        'permissionGroups' => $permissionGroups,
        'selectedPermissions' => old('permissions', []),
    ])
@endsection
