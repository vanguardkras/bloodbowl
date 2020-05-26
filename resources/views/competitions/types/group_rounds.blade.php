<div id="group_rounds" class="competition_type_settings">
    <label for="groups_size">{{ __('competitions/create.group_rounds.size') }}</label>

    <select class="custom-select"
            id="groups_size" name="groups_size">
        @for ($i = 2; $i <= 20; $i += 2)
            <option value="{{ $i }}"
                {{ isset($competition->parameters->groups_size) ?
                    ($competition->parameters->groups_size == $i ? 'selected' : '') :
                    (old('groups_size') == $i ? 'selected' : '') }}>
                {{ $i }}
            </option>
        @endfor
    </select>
    <hr>

    <label for="group_rounds_play_off">
        {{ __('competitions/create.group_rounds.play_off_num') }}
    </label>
    <div class="form-check col-6">
        <input type="checkbox" name="group_rounds_wo_po"
               class="form-check-input" id="group_rounds_wo_po" value="1"
            {{ isset($competition->parameters->group_rounds_play_off) ?
            ($competition->parameters->group_rounds_play_off ? '' : 'checked') :
            (old('group_rounds_wo_po') ? 'checked' : '') }}>
        <label class="form-check-label" for="group_rounds_wo_po">
            {{ __('competitions/create.group_rounds.wo_po') }}
        </label>
    </div>
    <select class="custom-select {{ isset($competition->parameters->group_rounds_play_off) ?
            ($competition->parameters->group_rounds_play_off ? '' : 'd-none') :
            (old('group_rounds_wo_po') ? 'd-none' : '') }}"
            id="group_rounds_play_off" name="group_rounds_play_off">
        @for ($i = 1; $i <= 6; $i++)
            <option value="{{ pow(2, $i) }}"
                {{ isset($competition->parameters->group_rounds_play_off) ?
                ($competition->parameters->group_rounds_play_off == pow(2, $i) ? 'selected' : '') :
                (old('group_rounds_play_off') == pow(2, $i) ? 'selected' : '') }}>
                {{ pow(2, $i) }}
            </option>
        @endfor
    </select>
    <hr>
</div>
