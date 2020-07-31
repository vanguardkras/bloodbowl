@extends('layouts.app')

@section('content')
    <h1 class="text-primary">{{ $user->name ?: __('auth.nameless_user') }}</h1>
    @if ($user->vk_id)
        <h6 class="text-info">VK: <a class="text-info" target="_blank"
                                     href="https://vk.com/id{{ $user->vk_id }}">id{{ $user->vk_id }}</a></h6>
    @endif
    <h4 class="mt-3">{{ __('coach.teams') }}</h4>
    @foreach ($user->teams as $team)
        <div class="my-1">
            <img style="width: 20px" src="{{ $team->logo() }}" alt="logo">
            <a href="/teams/{{ $team->id }}">{{ $team->name }}</a> ({{ $team->race->name }})
        </div>
    @endforeach
    <h4 class="mt-3">{{ __('coach.statistics') }}</h4>
    <div><strong>{{ __('coach.played') }}:</strong> {{ $user->teams->sum('played') }}</div>
    <div><strong>{{ __('coach.touchdowns') }}:</strong> {{ $user->teams->sum('touchdowns') }}</div>
    <div>
        <strong>{{ __('coach.success_rate') }}:</strong>
        {{ $user->teams->sum('played') ? (($user->teams->sum('wins') + $user->teams->sum('draws')) * 100) / $user->teams->sum('played') . '%': 'Нет данных' }}
    </div>
@endsection
