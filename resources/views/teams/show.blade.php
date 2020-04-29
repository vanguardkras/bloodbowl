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
                <strong>Количество игр:</strong> 3
            </div>
            <div>
                <strong>Процент побед:</strong> 33%
            </div>
            <div>
                <strong>Количество тачдаунов:</strong> 4
            </div>
            <div>
                <strong>Принято участий в соревнованиях:</strong> 1
            </div>
            <div>
                <strong>Количество трофеев:</strong> 1
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
        <tr class="table-danger">
            <th scope="row">1</th>
            <td>Разрушители (Орки)</td>
            <td>0 - 6</td>
            <td>Кубок чемпионов</td>
            <td class="d-none d-md-table-cell">21.10.2020</td>
        </tr>
        <tr class="table-success">
            <th scope="row">2</th>
            <td>Продуватели (Гоблины)</td>
            <td>3 - 1</td>
            <td>Кубок чемпионов</td>
            <td class="d-md-none d-none d-md-table-cell">22.10.2020</td>
        </tr>
        <tr class="table-secondary">
            <th scope="row">3</th>
            <td>Стоятели (Нурглиты)</td>
            <td>1 - 1</td>
            <td>Кубок чемпионов</td>
            <td class="d-md-none d-none d-md-table-cell">23.10.2020</td>
        </tr>
        </tbody>
    </table>
@endsection

@section('includes')
    <link rel="stylesheet" type="text/css" href="/css/datatables.min.css"/>
    <script type="text/javascript" src="/js/datatables.min.js" defer></script>
    <script type="text/javascript" src="/js/datatable.js" defer></script>
@endsection
