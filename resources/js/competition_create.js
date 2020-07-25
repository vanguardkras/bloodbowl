showHideCheckbox('#any_max_teams', '#max_teams');
showHideCheckbox('#any_max_games', '#max_games');
showHideCheckbox('#any_max_one_team_games', '#max_one_team_games');
showHideCheckbox('#open_league_wo_po', '#open_league_play_off');
showHideCheckbox('#group_rounds_wo_po', '#group_rounds_play_off');
showHideCheckbox('#open_registration', '#registration_end');

showCompetitionTypeSettings();
$('#type').change(function () {
    showCompetitionTypeSettings();
});

function showHideCheckbox(checkboxId, elementId) {
    $(checkboxId).change(function () {
        let element = $(elementId);
        if ($(this)[0].checked) {
            element.addClass('d-none');
        } else {
            element.removeClass('d-none');
        }
    });
}

function showCompetitionTypeSettings() {
    let type = $('#type').val();
    if (type) {
        $('.competition_type_settings').addClass('d-none');
        $('#' + type).removeClass('d-none');
    }

    let registration_form = $('#open_registration_form');

    if (!registration_form.hasClass('edit-page')) {
        registration_form.addClass('d-none');
        $('#registration_end').removeClass('d-none');
        $('#open_registration').prop('checked', false);
    }

    if (type === 'open_league') {
        registration_form.removeClass('d-none');
    }
}
