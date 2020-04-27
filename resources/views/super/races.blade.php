@extends('layouts.app')

@section('content')
    <h1>Races list</h1>
    @include('helpers.message', ['name' => 'message', 'importance' => 'success'])
    <table class="table table-sm table-hover table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">English</th>
            <th scope="col">Russian</th>
            <th scope="col">Default</th>
            <th scope="col" colspan="2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($races as $race)
            <tr>
                <form action="/races/{{ $race->id }}" method="post">
                    @csrf
                    @method('PATCH')
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>
                        <input class="form-control form-control-sm" type="text" name="name_en" value="{{ $race->name_en }}">
                    </td>
                    <td>
                        <input class="form-control form-control-sm" type="text" name="name_ru" value="{{ $race->name_ru }}">
                    </td>
                    <td style="text-align: center">
                        <input class="form-check-input" type="checkbox"
                               name="is_default" {{ $race->is_default ? 'checked' : '' }}>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary">Edit</button>
                    </td>
                </form>
                <td>
                    <form action="/races/{{ $race->id }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                        onclick="return confirm('Are you 100% sure?');">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="5">Create</th>
        </tr>
        <tr>
            <form action="/races" method="post">
                @csrf
                <th scope="row"></th>
                <td>
                    <input class="form-control form-control-sm" type="text" name="name_en">
                </td>
                <td>
                    <input class="form-control form-control-sm" type="text" name="name_ru">
                </td>
                <td style="text-align: center">
                    <input class="form-check-input" type="checkbox" name="is_default">
                </td>
                <td>
                    <button type="submit" class="btn btn-primary">Add</button>
                </td>
            </form>
        </tr>
        </tbody>
    </table>
@endsection
