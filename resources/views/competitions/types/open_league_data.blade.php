<div class="my-3">
    <table class="table table-sm" id="open_league">
        <thead class="thead-light">
        <tr>
            <th>{{ __('competitions/types/group_rounds.team') }}</th>
            <th class="d-sm-table-cell d-none">{{ __('competitions/types/group_rounds.race') }}</th>
            <th class="d-sm-table-cell d-none">{{ __('competitions/types/group_rounds.coach') }}</th>
            <th>{{ __('competitions/types/group_rounds.matches_played') }}</th>
            <th class="d-sm-table-cell d-none">{{ __('competitions/types/group_rounds.touchdowns') }}</th>
            <th class="d-sm-table-cell d-none">TD diff</th>
            <th>{{ __('competitions/types/group_rounds.points') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($scores = $competition->strategy->getScores() as $score)
            <tr>
                <td>
                    <img class="d-none d-md-inline" style="width: 25px;" src="{{ $score->team->logo() }}">
                    <a href="/teams/{{ $score->team->id }}">{{ $score->team->name }}</a>
                </td>
                <td class="d-sm-table-cell d-none">{{ $score->team->race->name }}</td>
                <td class="d-sm-table-cell d-none">
                    <a href="/user/{{ $score->team->user->id }}">
                        {{ $score->team->user->name ?: __('auth.nameless_user') }}
                    </a>
                </td>
                <td>{{ $score->team->countHistoriesOpenLeague($competition->id) }}</td>
                <td class="d-sm-table-cell d-none">{{ $score->touchdowns }}</td>
                <td class="d-sm-table-cell d-none">{{ $score->touchdowns_diff }}</td>
                <td class="font-weight-bold text-success">{{ $score->score }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@if ($competition->parameters->open_league_play_off && $competition->round > 1)
    {{ $competition->strategy->playOffView() }}
@endif
