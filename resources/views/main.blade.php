@extends('layouts.app')

@section('content')
    <h1 class="mb-5 mt-3">Blood Bowl Tournament System (BBTS)</h1>
    <div class="row">
        <div class="col-lg-6 col-md-12 shadow">
            <h3 class="text-info mt-3">{{ __('main.registration_open') }}</h3>
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>{{ __('main.name') }}</th>
                    <th>{{ __('main.type') }}</th>
                    <th>{{ __('main.registration_end') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($open_registrations as $open_registration)
                    <tr>
                        <td class="align-middle"><a href="/competitions/{{ $open_registration->id }}/show">
                                <img style="width: 30px;" src="{{ $open_registration->logo() }}" alt="logo">
                                {{ $open_registration->name }}
                            </a></td>
                        <td class="align-middle">{{ __('competitions/main.' . $open_registration->type) }}</td>
                        <td class="align-middle">{{ $open_registration->registration_end }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @auth
                @if ($ongoing_competitions->isNotEmpty())
                    <hr>
                    <h3 class="text-info mt-2">{{ __('main.you_take_part') }}</h3>
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>{{ __('main.name') }}</th>
                            <th>{{ __('main.type') }}</th>
                            <th>Команда</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($ongoing_competitions as $team)
                            @if ($team->competition)
                                <tr>
                                    <td class="align-middle"><a href="/competitions/{{ $team->competition->id }}/show">
                                            <img style="width: 30px;" src="{{ $team->competition->logo() }}" alt="logo">
                                            {{ $team->competition->name }}
                                        </a></td>
                                    <td class="align-middle">{{ __('competitions/main.' . $team->competition->type) }}</td>
                                    <td class="align-middle"><a href="/teams/{{ $team->id }}">{{ $team->name }}</a></td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                @endif
            @endauth
        </div>
        <div class="col-lg-6 col-md-12">
            {!! __('news.main') !!}
            <div class="mt-3">
                <h2>{{ __('general.instruction') }}</h2>
                <div class="text-center">
                    <iframe width="560" height="315"
                            src="https://www.youtube.com/embed/{{ (app()->getLocale() === 'ru') ? 'LBBJsOADWZQ' : 'AMjRACCvjV0' }} "
                            frameborder="0"
                            allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection
