@auth
    @if ($unconfirmed->isNotEmpty())
        <table class="table table-striped bg-light table-sm">
            <thead class="bg-info text-light">
            <tr>
                <th class="text-center" colspan="7">
                    Неподтвержденные результаты
                </th>
            </tr>
            <tr>
                <th class="text-center">{{ __('general.team') }} 1</th>
                <th class="text-center" colspan="3">{{ __('general.score') }}</th>
                <th class="text-center">{{ __('general.team') }} 2</th>
                <th class="text-center" colspan="2">{{ __('general.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($unconfirmed as $res)
                <tr>
                    <td class="text-center">{{ $res->teamLeft->name }} ({{ $res->teamLeft->race->name }})</td>
                    <td class="text-center">{{ $res->score_1 }}</td>
                    <td class="text-center"> :</td>
                    <td class="text-center">{{ $res->score_2 }}</td>
                    <td class="text-center">{{ $res->teamRight->name }} ({{ $res->teamRight->race->name }})</td>
                    <td class="text-center">
                        <form action="/results/{{ $res->id }}/confirm" method="post" style="display: inline-block">
                            @csrf
                            <button class="btn btn-sm btn-success" type="submit">{{ __('general.confirm') }}</button>
                        </form>
                        <form action="/results/{{ $res->id }}/reject" method="post" style="display: inline-block">
                            @csrf
                            <button class="btn btn-sm btn-info">{{ __('general.reject') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
@endauth
