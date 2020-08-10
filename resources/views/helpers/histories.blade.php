{{-- History section --}}
<h3 class="mt-4">{{ __('competitions/main.history') }}</h3>
<table class="table mx-auto table-sm table-striped" id="history">
    <thead>
    <tr>
        <th class="d-none d-md-table-cell" scope="col">{{ __('competitions/main.coach') }} 1</th>
        <th scope="col">{{ __('competitions/main.team') }} 1</th>
        <th scope="col">{{ __('competitions/main.score') }}</th>
        <th scope="col">{{ __('competitions/main.team') }} 2</th>
        <th class="d-none d-md-table-cell" scope="col">{{ __('competitions/main.coach') }} 2</th>
        <th class="d-none d-md-table-cell" scope="col">{{ __('competitions/main.date') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($histories as $history)
        <tr>
            <td class="d-none d-md-table-cell">
                <a href="/user/{{ $history->team_1->user->id }}">{{ $history->team_1->user->name ?? __('auth.nameless_user') }}</a>
            </td>
            <td><a href="/teams/{{ $history->team_1->id }}">{{ $history->team_1->name }}</a>
                <span class="d-none d-md-inline">({{ $history->race_1->name() }})</span>
            </td>
            <td>{{ $history->score_1 }} : {{ $history->score_2 }}</td>
            <td><a href="/teams/{{ $history->team_1->id }}">{{ $history->team_2->name }}</a>
                <span class="d-none d-md-inline">({{ $history->race_2->name() }})</span>
            </td>
            <td class="d-none d-md-table-cell">
                <a href="/user/{{ $history->team_2->user->id }}">{{ $history->team_2->user->name ?? __('auth.nameless_user') }}</a>
            </td>
            <td class="d-none d-md-table-cell">{{ $history->date }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
