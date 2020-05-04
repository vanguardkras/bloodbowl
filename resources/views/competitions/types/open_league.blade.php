<div id="open_league" class="competition_type_settings">
    <label for="max_games">{{ __('competitions/create.open_league.max_games') }}</label>
    <div class="form-check col-6">
        <input type="checkbox" name="any_max_games"
               class="form-check-input" id="any_max_games" value="1"
            {{ isset($competition->parameters->max_games) ?
            ($competition->parameters->max_games ? '' : 'checked') :
            (old('any_max_games') ? 'checked' : '') }}>
        <label class="form-check-label" for="any_max_games">
            {{ __('competitions/create.open_league.no_limit') }}</label>
    </div>
    <input type="number" class="form-control col-6 {{ isset($competition->parameters->max_games) ?
            ($competition->parameters->max_games ? '' : 'd-none') :
            (old('any_max_games') ? 'd-none' : '') }}"
           name="max_games" id="max_games"
           min="1" max="1024"
           value="{{ $competition->parameters->max_games ?? old('max_games', '8') }}">
    <hr>

    <label for="max_one_team_games">
        {{ __('competitions/create.open_league.max_games_one_team') }}
    </label>
    <div class="form-check col-6">
        <input type="checkbox" name="any_max_one_team_games"
               class="form-check-input" id="any_max_one_team_games" value="1"
            {{ isset($competition->parameters->max_one_team_games) ?
            ($competition->parameters->max_one_team_games ? '' : 'checked') :
            (old('any_max_one_team_games') ? 'checked' : '') }}>
        <label class="form-check-label" for="any_max_one_team_games">
            {{ __('competitions/create.open_league.no_limit') }}
        </label>
    </div>
    <input type="number" class="form-control col-6 {{ isset($competition->parameters->max_one_team_games) ?
            ($competition->parameters->max_one_team_games ? '' : 'd-none') :
            (old('any_max_one_team_games') ? 'd-none' : '') }}"
           name="max_one_team_games" id="max_one_team_games"
           min="1" max="1024"
           value="{{ $competition->parameters->max_one_team_games ?? old('max_one_team_games', '2') }}">
    <hr>

    <div class="form-check">
        <input type="checkbox" name="one_team_row"
               class="form-check-input" id="one_team_row" value="1"
            {{ isset($competition->parameters->one_team_row) ?
            ($competition->parameters->one_team_row ? 'checked' : '') :
            (old('one_team_row') ? 'checked' : '') }}>
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
            {{ isset($competition->parameters->open_league_play_off) ?
            ($competition->parameters->open_league_play_off ? '' : 'checked') :
            (old('open_league_wo_po') ? 'checked' : '') }}>
        <label class="form-check-label" for="open_league_wo_po">
            {{ __('competitions/create.open_league.wo_po') }}
        </label>
    </div>
    <select class="custom-select {{ isset($competition->parameters->open_league_play_off) ?
            ($competition->parameters->open_league_play_off ? '' : 'd-none') :
            (old('open_league_wo_po') ? 'd-none' : '') }}"
            id="open_league_play_off" name="open_league_play_off">
        @for ($i = 1; $i <= 6; $i++)
            <option value="{{ pow(2, $i) }}"
                {{ isset($competition->parameters->open_league_play_off) ?
                ($competition->parameters->open_league_play_off == pow(2, $i) ? 'selected' : '') :
                (old('open_league_play_off') == pow(2, $i) ? 'selected' : '') }}>
                {{ pow(2, $i) }}
            </option>
        @endfor
    </select>
    <hr>
</div>
