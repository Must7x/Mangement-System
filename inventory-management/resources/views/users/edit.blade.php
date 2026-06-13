@extends('layouts.app')

@section('title', 'تعديل مستخدم')

@section('content')
    @include('users._form', [
        'action' => route('users.update', $user),
        'method' => 'PUT',
        'title' => 'تعديل المستخدم',
        'user' => $user,
    ])
@endsection
