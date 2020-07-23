@if ($competition->round)
    <form action="/results/{{ $competition->id }}" method="post" class="bg-info text-light p-4">
        @csrf
        <h4>{{ __('competitions/main.add_results') }}</h4>
        <table>
            <thead>
            <tr>
                <th>{{ __('competitions/main.team') }} 1</th>
                <th colspan="3" class="text-center">{{ __('competitions/main.touchdowns') }}</th>
                <th class="text-right">{{ __('competitions/main.team') }} 2</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <select name="team_1" class="custom-select">
                        @foreach ($competition->teams as $team)
                            <option value="{{ $team->id }}"
                                    @if (old('team_1') == $team->id) selected @endif
                            >{{ $team->name }} ({{ $team->race->name }})
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="touchdowns_1" class="custom-select">
                        @for ($t = 0; $t < 9; $t++)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endfor
                    </select>
                </td>
                <td style="width: 25px;"></td>
                <td>
                    <select name="touchdowns_2" class="custom-select">
                        @for ($t = 0; $t < 9; $t++)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endfor
                    </select>
                </td>
                <td>
                    <select name="team_2" class="custom-select">
                        @foreach ($competition->teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->race->name }})
                            </option>
                        @endforeach
                    </select>
                </td>
            </tr>
            </tbody>
        </table>
        <button class="btn btn-primary mt-2" type="submit">
            {{ __('general.submit') }}
        </button>
    </form>
@endif

@section('processors')
    <script type="text/javascript" src="/js/add_results.js" defer></script>
@endsection
