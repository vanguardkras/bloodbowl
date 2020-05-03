@extends('layouts.app')

@section('content')
    <h1 class="mb-3">{{ __('teams.header') }}</h1>
    <form action="/teams" method="post" enctype="multipart/form-data">
        @csrf
        <div class="col-lg-4 col-md-6 col-sm-8">
            <div class="form-group pl-0">
                <label for="name">{{ __('teams.name') }}</label>
                <input type="text"
                       class="@error('name') is-invalid @enderror form-control"
                       id="name" name="name"
                       placeholder="{{ __('teams.name_placeholder') }}"
                       required>
                @include('helpers.input_error', ['field_name' => 'name'])
            </div>
            <label for="race_id">{{ __('teams.race') }}</label>
            <select class="custom-select mb-3" id="race_id" name="race_id" required>
                @foreach (races() as $race)
                    <option value="{{ $race->id }}">{{ $race->name }}</option>
                @endforeach
            </select>
            <label for="logo">{{ __('teams.logo') }} ({{ __('teams.optional') }})</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="logo" name="logo"
                       accept=".jpg, .jpeg, .png, .bmp, .jpe">
                <label class="custom-file-label" for="customFile">{{ __('teams.select_logo') }}</label>
            </div>
            <button type="submit" class="btn btn-primary btn-lg mt-3">{{ __('teams.create') }}</button>
        </div>
    </form>
@endsection
