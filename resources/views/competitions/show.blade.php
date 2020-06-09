@extends('layouts.app')

@section('content')
    <h1 class="mb-4">Competition <span class="text-primary">{{ $competition->name }}</span> management</h1>
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
                <h4>Внести результаты матча</h4>
                <table>
                    <thead>
                    <tr>
                        <th>Команда 1</th>
                        <th colspan="3" class="text-center">Тачдауны</th>
                        <th class="text-right">Команда 2</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <select name="team_1" class="custom-select">
                                @foreach ($competition->teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->race->name }})</option>
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
                                    <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->race->name }})</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button class="btn btn-primary mt-2" type="submit">
                    Подтвердить
                </button>
            </form>
        @endif
    </div>
    <div class="row">
        <div class="col-lg-6 col-md-12">
            @if (!$competition->round)
                <h3 class="my-3">Applications</h3>
                <table class="table table-borderless table-primary table-sm table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Race</th>
                        <th>Coach</th>
                        <th>Actions</th>
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
                <h3 class="my-3">Registered Teams</h3>
                <table class="table table-borderless table-info table-sm table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Race</th>
                        <th>Coach</th>
                        @if ($competition->round)
                            <th>Total Points</th>
                        @endif
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
                            @if ($competition->round)
                                <th>CALCULATE THEM</th>
                            @endif
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
@endsection

@section('includes')

@endsection
