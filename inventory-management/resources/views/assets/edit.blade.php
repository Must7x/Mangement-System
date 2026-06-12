@extends('layouts.app')

@section('title', 'تعديل عتاد')

@section('content')
    @include('assets._form', [
        'action' => route('assets.update', $asset),
        'method' => 'PUT',
        'title' => 'تعديل بيانات العتاد',
        'asset' => $asset,
    ])
@endsection
