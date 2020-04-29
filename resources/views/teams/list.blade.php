@extends('layouts.app')

@section('content')
    <h1 class="mb-3">Your teams</h1>
    <a class="mb-3 btn btn-primary btn-lg" style="width: 200px" href="/teams/create" role="button">
        {{ __('teams.header') }}
    </a>
    <div class="row">
        @foreach ($teams as $team)
            <div class="align-content-center col-xl-3 col-lg-4 col-md-6 col-sm-12 pb-4">
                <div class="card team_card">
                    <label class="action_image_container team_logo_upload">
                        <input type="hidden" name="team_id" value="{{ $team->id }}">
                        <input type="file" name="team_logo" class="logo_upload" accept=".jpg, .jpeg, .png, .bmp, .jpe">
                        <img class="action_image card-img-top"
                             src="{{ $team->logo ? '/storage/'.$team->logo : '/img/defaults/team.jpg'}}"
                             alt="Logo">
                        <img class="hover_action" src="/img/icons/upload.png" alt="upload">
                        <div class="hover_action_text">{{ __('teams.upload_new_logo') }}</div>
                    </label>
                    <div class="card-header">
                        <h5><a href="/teams/{{ $team->id }}">{{ $team->name }}</a></h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <small>{{ __('teams.race_attribute') }}: <strong>{{ $team->race->name }}</strong></small>
                        </li>
                        <li class="list-group-item">
                            <form action="/teams/{{ $team->id }}" method="post">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    {{ __('teams.delete') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('includes')
    <script src="{{ asset('js/teams_list.js') }}" defer></script>
@endsection
