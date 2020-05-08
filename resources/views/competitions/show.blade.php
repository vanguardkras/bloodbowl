@extends('layouts.app')

@section('content')
    <h1>Competition <span class="text-primary">{{ $competition->name }}</span> management</h1>
    <div class="row">
        <div class="col-lg-6 col-md-12">
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
                            <a href="/users/{{ $team->user->id }}">{{ $team->user->username ?: __('auth.nameless_user') }}</a>
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
                </tr>
                </thead>
                <tbody>
                @foreach ($competition->teams as $team)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><a href="/teams/{{ $team->id }}">{{ $team->name }}</a></td>
                        <td>{{ $team->race->name }}</td>
                        <td>
                            <a href="/users/{{ $team->user->id }}">{{ $team->user->username ?: __('auth.nameless_user') }}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-lg-6 col-md-12"></div>
    </div>
@endsection

@section('includes')

@endsection
