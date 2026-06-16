@extends('layouts.app')

@section('title', 'إضافة عتاد')

@section('content')
    @include('assets._form', [
        'action' => route('assets.store'),
        'method' => 'POST',
        'title' => 'تسجيل عتاد جديد',
        'asset' => null,
    ])
@endsection
