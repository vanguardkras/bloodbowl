@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <h1 class="text-primary">{{ $team->name }}</h1>
            <img class="img-thumbnail my-3"
                 src="{{ $team->logo ? '/storage/' . $team->logo : '/img/defaults/team.jpg' }}"
                 alt="logo">
            <h3>
                {{ __('teams.race_attribute') }}:
                <span class="text-primary">{{ $team->race->name }}</span>
            </h3>
            <hr>
        </div>
        <div class="col-md-6">
            <h2>{{ __('teams.statistics') }}</h2>
            <div>
                <strong>Количество игр:</strong> {{ $team->played }}
            </div>
            <div>
                <strong>Успешных игр:</strong> {{ $team->successful_games_percentage }}%
            </div>
            <div>
                <strong>Количество ничьих:</strong> {{ $team->draws }}
            </div>
            <div>
                <strong>Количество тачдаунов:</strong> {{ $team->touchdowns }}
            </div>
            <div>
                <strong>Количество трофеев:</strong> {{ $team->trophies()->count() }}
            </div>
            <hr>
        </div>
    </div>
    <h3>История игр</h3>
    <table class="table mx-auto table-sm" id="statistics">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Противник</th>
            <th scope="col">Счет</th>
            <th scope="col">Соревнование</th>
            <th scope="col" class="d-none d-md-table-cell">Дата</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($history as $record)
            @php
                $success = $record->getSuccess($team->id);
            @endphp
            <tr class="table-{{ $success === 'win' ? 'success' : ($success === 'loss' ? 'danger' : 'secondary') }}">
                <td>{{ $loop->iteration }}</td>
                <td>
                    @if ($record->team)
                        <a href="/teams/{{ $record->team->id }}">
                            {{ $record->team->name }}
                        </a>
                    @else
                        {{ $record->team_name }}
                    @endif
                    ({{ $record->race_name }})
                </td>
                <td>
                    {{ $team->id == $record->team_id_1 ? $record->score_1 : $record->score_2 }}
                    :
                    {{ $team->id == $record->team_id_2 ? $record->score_1 : $record->score_2 }}
                </td>
                <td><a href="/competitions/{{ $record->competition->id }}/show">{{ $record->competition->name }}</a>
                </td>
                <td class="d-none d-md-table-cell">{{ $record->date }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('includes')
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
    <script type="text/javascript" src="/js/datatables.min.js" defer></script>
    <script type="text/javascript" src="/js/datatable.js" defer></script>
@endsection
