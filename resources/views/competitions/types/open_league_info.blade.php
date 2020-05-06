<li class="list-group-item py-0">
    <b>{{ __('competitions/create.open_league.max_games') }}</b>
    {{ $competition->parameters->max_games ?: __('competitions/create.open_league.no_limit') }}
</li>
<li class="list-group-item py-0">
    <b>{{ __('competitions/create.open_league.max_games_one_team') }}</b>
    {{ $competition->parameters->max_one_team_games ?: __('competitions/create.open_league.no_limit') }}
</li>
<li class="list-group-item py-0">
    <b>{{ __('competitions/create.open_league.one_team_row') }}</b>
    {{ $competition->parameters->one_team_row ? __('general.yes') : __('general.no') }}
</li>
<li class="list-group-item py-0">
    <b>{{ __('competitions/create.open_league.play_off_num') }}</b>
    {{ $competition->parameters->open_league_play_off ?: __('competitions/create.open_league.wo_po') }}
</li>
