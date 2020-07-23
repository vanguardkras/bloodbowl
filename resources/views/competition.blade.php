@extends('layouts.app')

@section('content')
    <h5 class="text-info">{{ __('competitions/show_public.header') }}</h5>
    <h1 class="text-primary">{{ $competition->name }}</h1>
    <div class="row">
        <div class="col-md-6 col-sm-12">
            <img src="{{ $competition->logo() }}" alt="logo">
            <p class="pt-2 mb-1">
                <b>{{ __('competitions/show_public.commissioner') }}:</b>
                <a href="/user/{{ $competition->user->id }}">
                    {{ $competition->user->name ?: __('auth.nameless_user') }}
                </a>
            </p>
            <p class="mb-1">
                <b>{{ __('competitions/show_public.type') }}
                    :</b> {{ __('competitions/create.types.' . $competition->type) }}
            </p>
            <p class="mb-1">
                <b>{{ __('competitions/show_public.participants') }}:</b> {{ $competition->teams()->count() }}
            </p>
            <p class="mb-1">
                <b>{{ __('competitions/show_public.reg_requests') }}:</b> {{ $competition->registeredTeams()->count() }}
            </p>
            <p>
                @if ($competition->round)
                    <b>{{ __('competitions/show_public.current_round') }}:</b> {{ $competition->round }}
                @else
                    <b>{{ __('competitions/show_public.reg_end') }}:</b> {{ $competition->registration_end }}
                @endif
            </p>
        </div>
        <div class="col-md-6 col-sm-12">
            <h5>{{ __('competitions/show_public.info') }}</h5>
            <p class="text-info">{{ $competition->info }}</p>
            <p>
                <button class="btn btn-info btn-sm" type="button" data-toggle="collapse" data-target="#settings">
                    {{ __('competitions/show_public.details') }}
                </button>
            </p>
            <div class="collapse mb-3" id="settings">
                <ul class="list-group list-group">
                    <li class="list-group-item py-0">
                        <b>{{ __('competitions/show_public.prize_places') }}:</b> {{ $competition->tops_number }}
                    </li>
                    <li class="list-group-item py-0">
                        <b>{{ __('competitions/show_public.winner_points') }}:</b> {{ $competition->winner_points }}
                    </li>
                    @include('competitions.types.' . $competition->type . '_info')
                </ul>
            </div>
            @if ($competition->finished)
                <p class="text-danger">{{ __('competitions/show_public.finished') }} {{ $competition->finished }}</p>
                <h5>{{ __('competitions/show_public.winners') }}</h5>
                @foreach ($competition->trophies as $trophy)
                    <p class="mb-0">
                        {{ $trophy->position }} {{ __('competitions/show_public.place') }}:
                        <a href="/teams/{{ $trophy->team->id }}">{{ $trophy->team->name }}</a>
                        ({{ $trophy->team->race->name() }})
                    </p>
                @endforeach
            @elseif ($competition->round)

            @elseif (today()->toDateString() > $competition->registration_end)
                <p class="text-danger">{{ __('competitions/show_public.reg_over') }}</p>
            @elseif (auth()->guest())
                <p>{{ __('competitions/show_public.guest_info') }}</p>
                <a href="/register" class="btn btn-primary">{{ __('competitions/show_public.register') }}</a>
            @elseif (auth()->user()->getAvailableTeamsByRaces($competition->races)->isEmpty())
                <p class="small mb-0">{{ __('competitions/show_public.no_teams') }}</p>
                <p><a href="/teams/create"
                      class="btn btn-primary btn-sm">{{ __('competitions/show_public.create_team') }}</a></p>
            @else
                <form action="/competitions/{{ $competition->id }}/register_team" method="post">
                    @csrf
                    <label for="team_id">
                        @if (auth()->user()->registered_team || auth()->user()->approved_team)
                            {{ __('competitions/show_public.already_applied') }}:
                            <a href="/teams/{{ auth()->user()->approved_team->id ?? auth()->user()->registered_team->id }}">
                                {{ auth()->user()->approved_team->name ?? auth()->user()->registered_team->name }}
                            </a>
                            @if (!auth()->user()->approved_team)
                                <br>
                                {{ __('competitions/show_public.can_change') }}.
                            @endif
                        @else
                            {{ __('competitions/show_public.select_team') }}
                        @endif
                    </label>
                    @if (!auth()->user()->approved_team)
                        <select name="team_id" id="team_id" class="custom-select col-9">
                            @foreach (auth()->user()->getAvailableTeamsByRaces($competition->races) as $team)
                                @if ($competition->races->contains($team->race->id))
                                    <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->race->name }})</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="mt-2">
                            <button type="submit" class="btn
                                @if (auth()->user()->registered_team) btn-secondary @else btn-primary @endif">
                                {{ __('competitions/show_public.apply') }}
                            </button>
                        </div>
                    @endif
                </form>
            @endif
        </div>
    </div>

    {{-- Section of the started competition --}}
    @include('helpers.confirm_results')
    @if ($isParticipant)
        @include('helpers.add_results')
    @endif
    {{-- Competition information section --}}
    @if ($competition->round)
        @include('competitions.types.' . $competition->type . '_data')
    @endif

    {{-- History section --}}
    @if ($competition->round)
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
                    <td>
                        <a href="/user/{{ $history->team_1->user->id }}">{{ $history->team_1->user->name ?? __('auth.nameless_user') }}</a>
                    </td>
                    <td><a href="/teams/{{ $history->team_1->id }}">{{ $history->team_1->name }}</a>
                        ({{ $history->race_1->name() }})
                    </td>
                    <td>{{ $history->score_1 }} : {{ $history->score_2 }}</td>
                    <td><a href="/teams/{{ $history->team_1->id }}">{{ $history->team_2->name }}</a>
                        ({{ $history->race_2->name() }})
                    </td>
                    <td>
                        <a href="/user/{{ $history->team_2->user->id }}">{{ $history->team_2->user->name ?? __('auth.nameless_user') }}</a>
                    </td>
                    <td>{{ $history->date }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endsection

@section('includes')
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
    <script type="text/javascript" src="/js/datatables.min.js" defer></script>
    <script type="text/javascript" src="/js/datatable.js" defer></script>
@endsection
