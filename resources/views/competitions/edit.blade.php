@extends('layouts.app')

@section('content')
    <form action="/competitions/{{ $competition->id }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <input type="hidden" name="request" value="edit">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <h1 class="mb-3 text-primary">{{ $competition->name }}</h1>
                <h5 class="text-info">
                    {{ __('competitions/list.type') }}:
                    {{ __('competitions/create.types.' . $competition->type) }}
                </h5>
                <div>
                    <img
                        src="{{ $competition->logo() }}"
                        alt="logo" style="width: 200px">
                </div>
                {{-- Competition logo --}}
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

                <div class="form-group pl-0">
                    {{-- Competition information --}}
                    <label for="info">{{ __('competitions/create.info') }}</label>
                    <textarea class="@error('info') is-invalid @enderror form-control"
                              rows="5" maxlength="1000"
                              placeholder="{{ __('competitions/create.info_placeholder') }}"
                              id="info" name="info">{{ $competition->info }}</textarea>
                    @include('helpers.input_error', ['field_name' => 'info'])
                    <hr>
                    {{-- Competition information END --}}

                    {{-- Registration end --}}
                    <label for="registration_end">{{ __('competitions/create.registration_end') }}</label>

                    @if ($competition->type === 'open_league')
                        <div class="form-check edit-page" id="open_registration_form">
                            <input type="checkbox" name="open_registration"
                                   class="form-check-input" id="open_registration" value="1"
                                {{ !$competition->registration_end ? 'checked' : '' }}>
                            <label class="form-check-label" for="open_registration">
                                {{ __('competitions/create.open_registration_date') }}
                            </label>
                        </div>
                    @endif

                    <input type="date"
                           class="@error('registration_end') is-invalid @enderror
                               form-control
                               {{ !$competition->registration_end ? 'd-none' : '' }}"
                           id="registration_end" name="registration_end"
                           value="{{ $competition->registration_end ?? today()->addDay()->toDateString() }}"
                           min="{{ today()->addDay()->toDateString() }}"
                           required>
                    <p class="small">
                        {{ __('competitions/create.registration_end_desc') }}
                    </p>
                    @include('helpers.input_error', ['field_name' => 'registration_end'])
                    <hr>
                    {{-- Registration end END --}}

                    {{-- Matches confirmation --}}
                    <label for="self_confirm">{{ __('competitions/create.confirmation.label') }}</label>
                    <select class="custom-select" id="self_confirm" name="self_confirm">
                        <option value="2"
                            {{ $competition->self_confirm == '2' ? 'selected' : '' }}>
                            {{ __('competitions/create.confirmation.any') }}
                        </option>
                        <option value="1"
                            {{ $competition->self_confirm == '1' ? 'selected' : '' }}>
                            {{ __('competitions/create.confirmation.both') }}
                        </option>
                        <option value="0"
                            {{ $competition->self_confirm == '0' ? 'selected' : '' }}>
                            {{ __('competitions/create.confirmation.commissioner') }}
                        </option>
                    </select>
                    <p class="small">
                        {{ __('competitions/create.confirmation.description') }}
                    </p>
                    <hr>
                    {{-- Matches confirmation END --}}

                    {{-- Maximum number of teams --}}
                    <label for="max_teams">{{ __('competitions/create.max_teams') }}</label>
                    <div class="form-check col-6">
                        <input type="checkbox" name="any_max_teams"
                               class="form-check-input" id="any_max_teams" value="1"
                            {{ !$competition->max_teams ? 'checked' : '' }}>
                        <label class="form-check-label" for="any_max_teams">
                            {{ __('competitions/create.no_limit') }}
                        </label>
                    </div>
                    <input type="number"
                           class="form-control mb-2 {{ !$competition->max_teams ? 'd-none' : '' }} col-6"
                           name="max_teams" id="max_teams"
                           min="2" max="1024"
                           value="{{ $competition->max_teams ?: 8 }}">
                    <hr>
                    {{-- Maximum number of teams END --}}

                </div>
            </div>
            <div class="col-md-6 col-sm-12">
                <hr>
                <div class="row">
                    <div class="col-6">
                        {{-- Winner points --}}
                        <label for="winner_points">{{ __('competitions/create.winner_points') }}</label>
                        <select class="custom-select" id="winner_points" name="winner_points">
                            <option value="2" {{ $competition->winner_points == 2 ? 'selected' : '' }}>2</option>
                            <option value="3" {{ $competition->winner_points == 3 ? 'selected' : '' }}>3</option>
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
                               value="{{ $competition->tops_number }}">
                        <p class="small">
                            {{ __('competitions/create.num_winners_desc') }}
                        </p>
                        {{-- Number of winers END --}}

                    </div>
                </div>
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
                                {{ $competitionRaces->contains('id', null, $race->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="races[{{ $race->id }}]">
                                {{ $race->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <hr>
                {{-- Allowed races END --}}

                @include('competitions.types.' . $competition->type)

            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg mt-3">
            {{ __('competitions/create.edit_competition') }}
        </button>
    </form>
@endsection

@section('includes')
    <script src="{{ asset('js/competition_create.js') }}" defer></script>
@endsection

