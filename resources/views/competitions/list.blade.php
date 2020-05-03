@extends('layouts.app')

@section('content')
    <h1 class="mb-3">{{ __('competitions/list.header') }}</h1>
    <a class="mb-3 btn btn-primary btn-lg" style="width: 200px" href="/competitions/create" role="button">
        {{ __('competitions/list.create_button') }}
    </a>
@endsection
