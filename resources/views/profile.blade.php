@extends('layouts.app')

@section('content')
    <div class="col-lg-4 col-md-6 col-sm-8">
        <h2 class="mb-4">{{ __('profile.header') }}</h2>
        <hr>
        <form action="/profile_data_update" method="post">
            @csrf
            @method('PATCH')
            <div class="form-group pl-0">
                <label for="name">{{ __('profile.coach_name') }}</label>
                <input type="text"
                       class="@error('name') is-invalid @enderror form-control-lg form-control font-weight-bold"
                       id="name" name="name"
                       placeholder="{{ __('profile.coach_name_placeholder') }}"
                       value="{{ $user->name }}" required>
                @include('helpers.input_error', ['field_name' => 'name'])
                @include('helpers.input_error', ['field_name' => 'name'])<small class="form-text text-muted pb-2">
                    {{ __('profile.coach_name_desc') }}
                    @if(!$user->name)
                        {{ __('profile.coach_name_add_desc') }}
                    @endif
                </small>
                <button type="submit" class="btn btn-primary">{{ __('profile.save') }}</button>
            </div>
        </form>
        <hr>
        @if (!$user->name)
            <div class="alert alert-primary" role="alert">
                {{ __('profile.no_name_id') }}:<br>
                <b>ID: {{ $user->id }}</b>
            </div>
        @else
            <h6>ID: {{ $user->id }}</h6>
        @endif
        <hr>
        @if($user->email)
            <h5>{{ __('profile.change_password') }}</h5>
            <form action="/change_password" method="post">
                @csrf
                @method('PATCH')
                <div class="form-group pl-0">
                    <label for="old_password">{{ __('profile.old_password') }}</label>
                    <input type="password" class="@error('old_password') is-invalid @enderror form-control"
                           id="old_password" name="old_password" required>
                    @include('helpers.input_error', ['field_name' => 'old_password'])
                    <label for="new_password">{{ __('profile.new_password') }}</label>
                    <input type="password" class="@error('new_password') is-invalid @enderror form-control mb-3"
                           id="new_password" name="new_password" required>
                    @include('helpers.input_error', ['field_name' => 'new_password'])
                    <button type="submit" class="btn btn-primary">{{ __('profile.change') }}</button>
                </div>
            </form>
        @elseif($user->vk_id)
            <h5 class="mb-4">{{ __('profile.vk_link') }}: <a href="https://vk.com/id{{ $user->vk_id }}"
                                                             target="_blank">id{{ $user->vk_id }}</a></h5>
        @endif
        @if(!auth()->user()->commissioner)
            <hr>
            <p class="small">{{ __('profile.commissioner_text') }}</p>
            <form action="become_commissioner" method="post">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn btn-primary btn-sm">
                    {{ __('profile.commissioner_button') }}
                </button>
            </form>
        @endif
    </div>
@endsection
