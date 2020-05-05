@extends('layouts.app')

@section('content')
    <h1 class="mb-3">Blood Bowl в Красноярске</h1>
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <h3 class="info">Открыта регистрация на соревнования:</h3>
            <table class="table table-sm">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Тип</th>
                    <th>Регистрация до</th>
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
        <div class="col-lg-6 col-md-12"></div>
    </div>
@endsection
