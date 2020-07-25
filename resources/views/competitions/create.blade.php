@extends('layouts.app')

@section('content')
    <h1 class="mb-3">{{ __('competitions/create.header') }}</h1>
    <form action="/competitions" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <div class="form-group pl-0">
                    <hr>
                    {{-- Competiton name --}}
                    <label for="name">{{ __('competitions/create.name') }}</label>
                    <input type="text"
                           class="@error('name') is-invalid @enderror form-control"
                           id="name" name="name"
                           placeholder="{{ __('competitions/create.name_placeholder') }}"
                           value="{{ old('name') }}"
                           required>
                    @include('helpers.input_error', ['field_name' => 'name'])
                    <hr>
                    {{-- Competiton name END --}}

                    {{-- Competition information --}}
                    <label for="info">{{ __('competitions/create.info') }}</label>
                    <textarea class="@error('info') is-invalid @enderror form-control"
                              rows="5" maxlength="1000"
                              placeholder="{{ __('competitions/create.info_placeholder') }}"
                              id="info" name="info">{{ old('info') }}</textarea>
                    @include('helpers.input_error', ['field_name' => 'info'])
                    <hr>
                    {{-- Competition information END --}}

                    {{-- Competition logo --}}
                    <label for="logo">{{ __('competitions/create.logo') }}</label>
                    <div class="custom-file @error('logo') is-invalid @enderror">
                        <input type="file" class="custom-file-input" id="logo" name="logo"
                               accept=".jpg, .jpeg, .png, .bmp, .jpe">
                        @include('helpers.input_error', ['field_name' => 'logo'])
                        <label class="custom-file-label" for="customFile">
                            {{ __('competitions/create.logo_placeholder') }}
                        </label>
                    </div>
                    <hr>
                    {{-- Competition logo END --}}

                    {{-- Registration end --}}
                    <label for="registration_end">{{ __('competitions/create.registration_end') }}</label>

                    <div class="form-check d-none" id="open_registration_form">
                        <input type="checkbox" name="open_registration"
                               class="form-check-input" id="open_registration" value="1"
                            {{ old('open_registration') ? 'checked' : '' }}>
                        <label class="form-check-label" for="open_registration">
                            {{ __('competitions/create.open_registration_date') }}
                        </label>
                    </div>

                    <input type="date"
                           class="@error('registration_end') is-invalid @enderror
                               form-control
                               {{ old('open_registration') ? 'd-none' : '' }}"
                           id="registration_end" name="registration_end"
                           value="{{ old('registration_end', today()->addDay()->toDateString()) }}"
                           min="{{ today()->addDay()->toDateString() }}"
                           required>
                    <p class="small" id="open_registration_desc">
                        {{ __('competitions/create.registration_end_desc') }}
                    </p>
                    @include('helpers.input_error', ['field_name' => 'registration_end'])
                    <hr>
                    {{-- Registration end END --}}

                    {{-- Matches confirmation --}}
                    <label for="self_confirm">{{ __('competitions/create.confirmation.label') }}</label>
                    <select class="custom-select" id="self_confirm" name="self_confirm">
                        <option value="2"
                            {{ old('self_confirm') === '2' ? 'selected' : '' }}>
                            {{ __('competitions/create.confirmation.any') }}
                        </option>
                        <option value="1"
                            {{ old('self_confirm') === '1' ? 'selected' : '' }}>
                            {{ __('competitions/create.confirmation.both') }}
                        </option>
                        <option value="0"
                            {{ old('self_confirm') === '0' ? 'selected' : '' }}>
                            {{ __('competitions/create.confirmation.commissioner') }}
                        </option>
                    </select>
                    <p class="small">
                        {{ __('competitions/create.confirmation.description') }}
                    </p>
                    <hr>
                    {{-- Matches confirmation END --}}

                </div>
                {{-- Maximum number of teams --}}
                <label for="max_teams">{{ __('competitions/create.max_teams') }}</label>
                <div class="form-check col-6">
                    <input type="checkbox" name="any_max_teams"
                           class="form-check-input" id="any_max_teams" value="1"
                        {{ old('max_teams') ? (old('any_max_teams') ? 'checked' : '') : 'checked' }}>
                    <label class="form-check-label" for="any_max_teams">
                        {{ __('competitions/create.no_limit') }}
                    </label>
                </div>
                <input type="number"
                       class="form-control mb-2 {{ old('max_teams') ? (old('any_max_teams') ? 'd-none' : '') : 'd-none' }} col-6"
                       name="max_teams" id="max_teams"
                       min="2" max="1024"
                       value="{{ old('max_teams', '8') }}">
                <hr>
                {{-- Maximum number of teams END --}}

                <div class="row">
                    <div class="col-6">

                        {{-- Winner points --}}
                        <label for="winner_points">{{ __('competitions/create.winner_points') }}</label>
                        <select class="custom-select" id="winner_points" name="winner_points">
                            <option value="2" {{ old('winner_points') === '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('winner_points') === '2' ? '' : 'selected' }}>3</option>
                        </select>
                        <p class="small">
                            {{ __('competitions/create.winner_points_desc') }}
                        </p>
                        {{-- Winner points END --}}

                    </div>
                    <div class="col-6">

                        {{-- Number of winners --}}
                        <label for="tops_number">{{ __('competitions/create.num_winners') }}</label>
                        <input type="number" class="form-control"
                               placeholder="Enter number of winners"
                               name="tops_number" id="tops_number"
                               max="10" min="1"
                               value="{{ old('tops_number', '1') }}">
                        <p class="small">
                            {{ __('competitions/create.num_winners_desc') }}
                        </p>
                        {{-- Number of winers END --}}

                    </div>
                </div>
                <hr>
            </div>
            <div class="col-md-6 col-sm-12">
                <hr>
                {{-- Allowed races --}}
                <h5>{{ __('competitions/create.races') }}</h5>
                <div class="row pl-3">
                    @foreach (races() as $race)
                        <div class="col-6 form-check">
                            <input type="checkbox"
                                   name="races[]"
                                   value="{{ $race->id }}"
                                   class="form-check-input" id="races[{{ $race->id }}]"
                                {{ is_array(old('races')) && in_array($race->id, old('races')) ? 'checked' : ($race->is_default ? 'checked' : '') }}>
                            <label class="form-check-label" for="races[{{ $race->id }}]">
                                {{ $race->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <hr>
                {{-- Allowed races END --}}

                {{-- Competition type --}}
                <label for="type">{{ __('competitions/create.types.name') }}</label>
                <select class="custom-select" id="type" name="type">
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                            {{ __('competitions/main.' . $type) }}
                        </option>
                    @endforeach
                </select>
                <hr>
                {{-- Competition type END --}}

                @foreach ($types as $type)
                    @include('competitions.types.' . $type)
                @endforeach

            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg mt-3">
            {{ __('competitions/create.button') }}
        </button>
    </form>
@endsection

@section('includes')
    <script src="{{ asset('js/competition_create.js') }}" defer></script>
@endsection
