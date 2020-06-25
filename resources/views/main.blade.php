@extends('layouts.app')

@section('content')
    <h1 class="mb-3">Blood Bowl Tournament System (BBTS)</h1>
    <div class="row">
        <div class="col-lg-6 col-md-12 shadow">
            <h3 class="text-info">{{ __('main.registration_open') }}</h3>
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
        </div>
        <div class="col-lg-6 col-md-12">
            Дорогие друзья<br><br>
            Данный сайт еще находится в разрботке. По окончании разработки планируется запустить систему ведения
            турниров в нашей любимой игре Blood Bowl в ее настольной и компьютерной интерпретации.
            Система будет поддерживать общение игроков, объявления о турнирах, возможность гибко настраивать турниры и вносить
            результаты, будет вестись статистика и история, а также зал славы.
            <br><br>
            В перспективе также планируется внедрить создание ростеров онлайн и возможны другие нововвдеения.
        </div>
    </div>
@endsection
