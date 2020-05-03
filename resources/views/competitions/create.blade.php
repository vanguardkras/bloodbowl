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
                    <input type="date"
                           class="@error('registration_end') is-invalid @enderror form-control"
                           id="registration_end" name="registration_end"
                           value="{{ old('registration_end', today()->addDay()->toDateString()) }}"
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
                    <option value="open_league" {{ old('type') === 'open_league' ? 'selected' : '' }}>
                        {{ __('competitions/create.types.open_league') }}
                    </option>
                    <option value="play_off" {{ old('type') === 'play_off' ? 'selected' : '' }}>
                        {{ __('competitions/create.types.play_off') }}
                    </option>
                    <option value="group_rounds" {{ old('type') === 'group_rounds' ? 'selected' : '' }}>
                        {{ __('competitions/create.types.group_rounds') }}
                    </option>
                </select>
                <hr>
                {{-- Competition type END --}}

                {{-- Open league settings --}}
                <div id="open_league" class="competition_type_settings">
                    <label for="max_games">{{ __('competitions/create.open_league.max_games') }}</label>
                    <div class="form-check col-6">
                        <input type="checkbox" name="any_max_games"
                               class="form-check-input" id="any_max_games" value="1"
                            {{ old('any_max_games') ? 'checked' : '' }}>
                        <label class="form-check-label" for="any_max_games">
                            {{ __('competitions/create.open_league.no_limit') }}</label>
                    </div>
                    <input type="number" class="form-control col-6 {{ old('any_max_games') ? 'd-none' : '' }}"
                           name="max_games" id="max_games"
                           min="1" max="1024"
                           value="{{ old('max_games', '8') }}">
                    <hr>

                    <label for="max_one_team_games">
                        {{ __('competitions/create.open_league.max_games_one_team') }}
                    </label>
                    <div class="form-check col-6">
                        <input type="checkbox" name="any_max_one_team_games"
                               class="form-check-input" id="any_max_one_team_games" value="1"
                            {{ old('any_max_one_team_games') ? 'checked' : '' }}>
                        <label class="form-check-label" for="any_max_one_team_games">
                            {{ __('competitions/create.open_league.no_limit') }}
                        </label>
                    </div>
                    <input type="number" class="form-control col-6 {{ old('any_max_one_team_games') ? 'd-none' : '' }}"
                           name="max_one_team_games" id="max_one_team_games"
                           min="1" max="1024"
                           value="{{ old('max_one_team_games', '2') }}">
                    <hr>

                    <div class="form-check">
                        <input type="checkbox" name="one_team_row"
                               class="form-check-input" id="one_team_row" value="1"
                            {{ old('one_team_row') ? 'checked' : '' }}>
                        <label class="form-check-label" for="one_team_row">
                            {{ __('competitions/create.open_league.one_team_row') }}
                        </label>
                    </div>
                    <hr>

                    <label for="open_league_play_off">
                        {{ __('competitions/create.open_league.play_off_num') }}
                    </label>
                    <div class="form-check col-6">
                        <input type="checkbox" name="open_league_wo_po"
                               class="form-check-input" id="open_league_wo_po" value="1"
                            {{ old('open_league_wo_po') ? 'checked' : '' }}>
                        <label class="form-check-label" for="open_league_wo_po">
                            {{ __('competitions/create.open_league.wo_po') }}
                        </label>
                    </div>
                    <select class="custom-select {{ old('open_league_wo_po') ? 'd-none' : '' }}"
                            id="open_league_play_off" name="open_league_play_off">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ pow(2, $i) }}"
                                {{ old('open_league_play_off') == pow(2, $i) ? 'selected' : '' }}>
                                {{ pow(2, $i) }}
                            </option>
                        @endfor
                    </select>
                    <hr>
                </div>
                {{-- Open league settings END--}}

                {{-- Group Rounds --}}
                <div id="group_rounds" class="competition_type_settings">
                    <label for="groups_size">{{ __('competitions/create.group_rounds.size') }}</label>
                    <input type="number" class="form-control col-6"
                           name="groups_size" id="groups_size"
                           min="2" max="512"
                           value="{{ old('group_rounds', '4') }}">
                    <hr>

                    <label for="group_rounds_play_off">
                        {{ __('competitions/create.group_rounds.play_off_num') }}
                    </label>
                    <div class="form-check col-6">
                        <input type="checkbox" name="group_rounds_wo_po"
                               class="form-check-input" id="group_rounds_wo_po" value="1"
                            {{ old('group_rounds_wo_po') ? 'checked' : '' }}>
                        <label class="form-check-label" for="group_rounds_wo_po">
                            {{ __('competitions/create.group_rounds.wo_po') }}
                        </label>
                    </div>
                    <select class="custom-select {{ old('group_rounds_wo_po') ? 'd-none' : '' }}"
                            id="group_rounds_play_off" name="group_rounds_play_off">
                        @for ($i = 1; $i <= 6; $i++)
                            <option value="{{ pow(2, $i) }}"
                                {{ old('group_rounds_play_off') == pow(2, $i) ? 'selected' : '' }}>
                                {{ pow(2, $i) }}
                            </option>
                        @endfor
                    </select>
                    <hr>
                </div>
                {{-- Group Rounds END --}}

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
