@extends('layouts.app')

@section('title', __('pages.users_edit'))

@section('content')
    @include('users._form', [
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'title' => __('pages.users_edit'),
        'user' => $user,
    ])
@endsection
