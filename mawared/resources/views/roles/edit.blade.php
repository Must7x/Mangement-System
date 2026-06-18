@extends('layouts.app')

@section('title', __('pages.roles_edit'))

@section('content')
    @include('roles._form', [
        'title' => __('pages.roles_edit'),
        'action' => route('roles.update', $role),
        'method' => 'PUT',
        'role' => $role,
        'permissionGroups' => $permissionGroups,
        'selectedPermissions' => old('permissions', $selectedPermissions),
    ])
@endsection
