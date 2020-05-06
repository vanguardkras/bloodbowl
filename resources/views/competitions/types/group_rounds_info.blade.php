<li class="list-group-item py-0">
    <b>{{ __('competitions/create.group_rounds.size') }}:</b> {{ $competition->parameters->groups_size }}
</li>
<li class="list-group-item py-0">
    <b>{{ __('competitions/create.group_rounds.play_off_num') }}:</b>
    {{ $competition->parameters->group_rounds_play_off ?: __('competitions/create.group_rounds.wo_po') }}
</li>
