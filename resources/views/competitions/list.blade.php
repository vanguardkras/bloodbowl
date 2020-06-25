@extends('layouts.app')

@section('content')
    <h1 class="mb-3">{{ __('competitions/list.header') }}</h1>
    <a class="mb-3 btn btn-primary btn-lg" style="width: 200px" href="/competitions/create" role="button">
        {{ __('competitions/list.create_button') }}
    </a>
    <div class="row">
        @foreach ($competitions as $competition)
            @if (!$competition->finished)
                <div class="align-content-center col-xl-3 col-lg-4 col-md-6 col-sm-12 pb-4">
                    <div class="card team_card">
                        <a href="/competitions/{{ $competition->id }}">
                            <img class="action_image card-img-top"
                                 src="{{ $competition->logo() }}"
                                 alt="Logo">
                        </a>
                        <div class="card-header">
                            <h5><a href="/competitions/{{ $competition->id }}">{{ $competition->name }}</a></h5>
                        </div>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <small>{{ __('competitions/list.teams_number') }}:
                                    <strong>{{ $competition->teams_count }}</strong>
                                </small>
                            </li>
                            <li class="list-group-item">
                                <small>{{ __('competitions/list.type') }}:
                                    <strong>{{ __('competitions/main.' . $competition->type) }}</strong>
                                </small>
                            </li>
                            <li class="list-group-item">
                                @if (!$competition->round)
                                    <a class="btn btn-info w-100 btn-sm mb-1"
                                       href="/competitions/{{ $competition->id }}/edit">
                                        {{ __('general.edit') }}
                                    </a>
                                @endif
                                <form action="/competitions/{{ $competition->id }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100 btn-sm"
                                            onclick="return confirm('{{ __('competitions/list.are_you_sure') }}')">
                                        {{ __('general.delete') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <hr>
    <h2 class="my-3">{{ __('competitions/list.archive') }}</h2>
    <table class="table mx-auto table-sm" id="archive">
        <thead>
        <tr>
            <th scope="col">{{ __('competitions/list.name') }}</th>
            <th scope="col">{{ __('competitions/list.type') }}</th>
            <th scope="col">{{ __('competitions/list.finished') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($competitions as $competition)
            @if($competition->finished)
                <tr>
                    <td>
                        <a href="/competitions/{{ $competition->id }}">
                            {{ $competition->name }}
                        </a>
                    </td>
                    <td>{{ __('competitions/main.' . $competition->type) }}</td>
                    <td>{{ $competition->finished }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endsection

@section('includes')
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
    <script type="text/javascript" src="/js/datatables.min.js" defer></script>
    <script type="text/javascript" src="/js/datatable.js" defer></script>
@endsection
