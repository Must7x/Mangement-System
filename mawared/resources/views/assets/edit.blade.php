@extends('layouts.app')

@section('title', __('pages.assets_edit'))

@section('content')
    @include('assets._form', [
        'action' => route('assets.update', $asset),
        'method' => 'PUT',
        'title' => __('pages.assets_edit_form'),
        'asset' => $asset,
    ])
@endsection
