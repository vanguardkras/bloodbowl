<div class="shadow-lg p-3">
    <h1>{{ __('competitions/main.play_off_header') }}</h1>
    <table class="table table-sm">
        @for ($row = 0; $row < $playOffRounds; $row++)
            <tr>
                @for ($col = 0; $col <= $lastRound; $col++)
                    @if ($row % pow(2, $col) === 0)
                        <td style="vertical-align: middle; text-align: center; border: none"
                            rowspan="{{ pow(2, $col) }}"
                            class="px-0 position-relative">
                            <div class="d-flex align-items-center">
                                @if ($col)
                                    <div class="d-flex flex-grow-1 bg-dark" style="height: 1px;"></div>
                                @endif
                                <div
                                    class="p-2 text-light bg-dark @if ($col == $lastRound) border border-success @endif">
                                    @if ($score = $scores->where('round', $col + $startRound)->firstWhere('order', $row / pow(2, $col)))
                                        @if($col != $lastRound)
                                        <span class="text-success">{{ $score->touchdowns }}</span>
                                        @endif
                                        <span><a href="/teams/{{ $score->team->id }}">{{ $score->team->name }}</a><br>
                                            ({{ $score->team->race->name() }})</span><br>
                                        <span class="small"><span class="text-info">{{ __('coach.coach') }}:</span> <a
                                                href="/user/{{ $score->team->user->id }}">{{ $score->team->user->name ?: __('auth.nameless_user') }}</a></span>
                                    @else
                                        {{ __('competitions/main.awaiting') }}
                                    @endif
                                </div>
                                @if ($col != $lastRound)
                                    <div class="d-flex flex-grow-1 bg-dark" style="height: 1px;"></div>
                                    <div class="position-absolute bg-dark"
                                         style="right: 0;
                                         {{ floor($row / pow(2, $col)) % 2 === 0 ? 'bottom' : 'top' }}: 0;
                                             height: 50%; width: 1px;">
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
</div>
