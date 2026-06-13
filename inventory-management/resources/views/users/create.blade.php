@extends('layouts.app')

@section('title', 'إضافة مستخدم')

@section('content')
    @include('users._form', [
        'action' => route('users.store'),
        'method' => 'POST',
        'title' => 'إضافة مستخدم جديد',
        'user' => null,
    ])
@endsection
