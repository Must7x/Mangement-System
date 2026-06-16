@extends('layouts.app')

@section('title', __('pages.users_create'))

@section('content')
    @include('users._form', [
        'action' => route('users.store'),
        'method' => 'POST',
        'title' => __('pages.users_create_form'),
        'user' => null,
    ])
@endsection
