@extends('layouts.app')

@section('content')
    <h1 class="mb-4">{{ __('competitions/management.title') }}
        <span class="text-primary">{{ $competition->name }}</span>
    </h1>
    @if ($competition->finished)
        <h3 class="text-primary mb-3">{{ __('competitions/show_public.finished') }} {{ $competition->finished }}</h3>
    @else
        <div class="mb-3">
            <form action="/competitions/{{ $competition->id }}/next_round" method="post">
                @csrf
                <button class="btn btn-danger mb-3" type="submit">
                    @if (!$competition->round)
                        {{ __('competitions/management.start_button') }}
                    @elseif ($competition->round >= $competition->getMaxRound())
                        {{ __('competitions/management.finish_button') }}
                    @else
                        {{ __('competitions/management.next_round_button') }}
                    @endif
                </button>
            </form>
            @if ($competition->round)
                <form action="/results/{{ $competition->id }}" method="post" class="bg-info text-light p-4">
                    @csrf
                    <h4>{{ __('competitions/main.add_results') }}</h4>
                    <table>
                        <thead>
                        <tr>
                            <th>{{ __('competitions/main.team') }} 1</th>
                            <th colspan="3" class="text-center">{{ __('competitions/main.touchdowns') }}</th>
                            <th class="text-right">{{ __('competitions/main.team') }} 2</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <select name="team_1" class="custom-select">
                                    @foreach ($competition->teams as $team)
                                        <option value="{{ $team->id }}"
                                                @if (old('team_1') == $team->id) selected @endif
                                        >{{ $team->name }} ({{ $team->race->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="touchdowns_1" class="custom-select">
                                    @for ($t = 0; $t < 9; $t++)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endfor
                                </select>
                            </td>
                            <td style="width: 25px;"></td>
                            <td>
                                <select name="touchdowns_2" class="custom-select">
                                    @for ($t = 0; $t < 9; $t++)
                                        <option value="{{ $t }}">{{ $t }}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <select name="team_2" class="custom-select">
                                    @foreach ($competition->teams as $team)
                                        <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->race->name }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <button class="btn btn-primary mt-2" type="submit">
                        {{ __('general.submit') }}
                    </button>
                </form>
            @endif
        </div>
    @endif
    <div class="row">
        <div class="col-lg-6 col-md-12">
            @if (!$competition->round)
                <h3 class="my-3">{{ __('competitions/management.applications') }}</h3>
                <table class="table table-borderless table-primary table-sm table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('competitions/management.name') }}</th>
                        <th>{{ __('competitions/management.race') }}</th>
                        <th>{{ __('competitions/management.coach') }}</th>
                        <th>{{ __('competitions/management.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($competition->registeredTeams as $team)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><a href="/teams/{{ $team->id }}">{{ $team->name }}</a></td>
                            <td>{{ $team->race->name }}</td>
                            <td>
                                <a href="/user/{{ $team->user->id }}">{{ $team->user->username ?: __('auth.nameless_user') }}</a>
                            </td>
                            <td>
                                <form action="/register/{{ $competition->id }}/{{$team->id}}" method="post">
                                    @csrf
                                    <button class="btn btn-sm btn-success" type="submit" name="action" value="approve">
                                        Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger" type="submit" name="action" value="reject">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <hr>
                <h3 class="my-3">{{ __('competitions/management.registered_teams') }}</h3>
                <table class="table table-borderless table-info table-sm table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>{{ __('competitions/management.name') }}</th>
                        <th>{{ __('competitions/management.race') }}</th>
                        <th>{{ __('competitions/management.coach') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($competition->teams as $team)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><a href="/teams/{{ $team->id }}">{{ $team->name }}</a></td>
                            <td>{{ $team->race->name }}</td>
                            <td>
                                <a href="/user/{{ $team->user->id }}">{{ $team->user->username ?: __('auth.nameless_user') }}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    @if ($competition->round)
        @include('competitions.types.' . $competition->type . '_data')
    @endif

    <h3 class="mt-4">{{ __('competitions/main.history') }}</h3>
    <table class="table mx-auto table-sm table-striped" id="history">
        <thead>
        <tr>
            <th scope="col">{{ __('competitions/main.coach') }} 1</th>
            <th scope="col">{{ __('competitions/main.team') }} 1</th>
            <th scope="col">{{ __('competitions/main.score') }}</th>
            <th scope="col">{{ __('competitions/main.team') }} 2</th>
            <th scope="col">{{ __('competitions/main.coach') }} 2</th>
            <th scope="col">{{ __('competitions/main.date') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($histories as $history)
            <tr>
                <td><a href="/user/{{ $history->team_1->user->id }}">{{ $history->team_1->user->name ?? __('auth.nameless_user') }}</a></td>
                <td><a href="/teams/{{ $history->team_1->id }}">{{ $history->team_1->name }}</a> ({{ $history->race_1->name() }})</td>
                <td>{{ $history->score_1 }} : {{ $history->score_2 }}</td>
                <td><a href="/teams/{{ $history->team_1->id }}">{{ $history->team_2->name }}</a> ({{ $history->race_2->name() }})</td>
                <td><a href="/user/{{ $history->team_2->user->id }}">{{ $history->team_2->user->name ?? __('auth.nameless_user') }}</a></td>
                <td>{{ $history->date }}</td>
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
