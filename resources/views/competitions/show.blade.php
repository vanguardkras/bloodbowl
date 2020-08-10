@extends('layouts.app')

@section('content')
    <h1 class="mb-4">{{ __('competitions/management.title') }}
        <span class="text-primary">{{ $competition->name }}</span>
    </h1>
    @if ($competition->finished)
        <h3 class="text-primary mb-3">{{ __('competitions/show_public.finished') }} {{ $competition->finished }}</h3>
        <h5>{{ __('competitions/show_public.winners') }}</h5>
        @foreach ($competition->trophies as $trophy)
            <p class="mb-1">
                {{ $trophy->position }} {{ __('competitions/show_public.place') }}:
                <a href="/teams/{{ $trophy->team->id }}">{{ $trophy->team->name }}</a>
                ({{ $trophy->team->race->name() }})
            </p>
        @endforeach
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
            @include('helpers.confirm_results')
            @include('helpers.add_results')
        </div>
    @endif
    <div class="row">
        <div class="col-lg-6 col-md-12">
            @if (!$competition->round || ($competition->type === 'open_league' && !$competition->registration_end && $competition->round === 1))
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
                                        {{ __('competitions/management.approve') }}
                                    </button>
                                    <button class="btn btn-sm btn-danger" type="submit" name="action" value="reject">
                                        {{ __('competitions/management.reject') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if (!($competition->round && $competition->type === 'open_league' && !$competition->registration_end && $competition->round === 1))
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
            @endif
        </div>
    </div>
    @if ($competition->round)
        @include('competitions.types.' . $competition->type . '_data')
    @endif

    {{-- History section --}}
    @if ($competition->round)
        @include('helpers.histories')
    @endif

@endsection

@section('includes')
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
    <script type="text/javascript" src="/js/datatables.min.js" defer></script>
    <script type="text/javascript" src="/js/datatable.js" defer></script>
@endsection
