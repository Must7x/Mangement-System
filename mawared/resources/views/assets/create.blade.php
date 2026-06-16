@extends('layouts.app')

@section('title', __('pages.assets_create'))

@section('content')
    @include('assets._form', [
        'action' => route('assets.store'),
        'method' => 'POST',
        'title' => __('pages.assets_create_form'),
        'asset' => null,
    ])
@endsection
