<table class="table table-sm">
    @for ($scores = $competition->strategy->getGroupStageTeams(), $i = 0; $i < count($scores); $i += $competition->parameters->groups_size)
        <thead class="thead-dark">
        <tr>
            <th colspan="6" class="text-center">
                {{ __('competitions/types/group_rounds.group') }}
                {{ chr(65 + floor($i / $competition->parameters->groups_size)) }}
            </th>
        </tr>
        </thead>
        <thead class="thead-light">
        <tr>
            <th>{{ __('competitions/types/group_rounds.team') }}</th>
            <th class="d-sm-table-cell d-none">{{ __('competitions/types/group_rounds.race') }}</th>
            <th class="d-sm-table-cell d-none">{{ __('competitions/types/group_rounds.coach') }}</th>
            <th>{{ __('competitions/types/group_rounds.matches_played') }}</th>
            <th>{{ __('competitions/types/group_rounds.touchdowns') }}</th>
            <th>{{ __('competitions/types/group_rounds.points') }}</th>
        </tr>
        </thead>
        <tbody>
        @for ($j = $i; $j < $i + $competition->parameters->groups_size; $j++)
            <tr>
                <td><a href="/teams/{{ $scores[$j]->team->id }}">{{ $scores[$j]->team->name }}</a></td>
                <td class="d-sm-table-cell d-none">{{ $scores[$j]->team->race->name }}</td>
                <td class="d-sm-table-cell d-none">
                    <a href="/user/{{ $scores[$j]->team->user->id }}">
                        {{ $scores[$j]->team->user->name ?: __('auth.nameless_user') }}
                    </a>
                </td>
                <td>{{ $scores[$j]->round }}</td>
                <td>{{ $scores[$j]->touchdowns }}</td>
                <td class="font-weight-bold text-success">{{ $scores[$j]->score }}</td>
            </tr>
        @endfor
        </tbody>
    @endfor
</table>

@if ($competition->parameters->group_rounds_play_off && $competition->round > ($competition->parameters->groups_size - 1))
    {{ $competition->strategy->playOffView() }}
@endif
