let competition_id = window.location.pathname.split('/')[2];
let team_1_selector = $('[name="team_1"]');
$.ajax({
    url: '/is_commissioner/' + competition_id,
    success: function (message) {
        if (!message) {
            $.ajax({
                url: '/get_team/' + competition_id,
                success: function (team_id) {
                    leaveOneTeam(team_id);
                }
            });
        }
    }
});

team_1_selector.change(findPossibleOpponents);

function leaveOneTeam(team_id) {
    let other_teams = $('[name="team_1"] option:not([value="' + team_id + '"])');
    other_teams.hide();
}


function findPossibleOpponents() {
    let team_id = team_1_selector.val();

    $.ajax({
        url: '/get_possible_opponents/' + competition_id + '/' + team_id,
        success: function (teams) {
            $('[name="team_2"] option').hide();

            if (teams.length) {
                $('[name="team_2"]').prop('disabled', false);
            } else {
                $('[name="team_2"]').prop('disabled', true);
            }

            for (let i = 0; i < teams.length; i++) {
                let option = $('[name="team_2"] option[value="' + teams[i] + '"]');
                option.show();
                if (i === 0) {
                    option.prop('selected', true);
                }
            }
        }
    });
}
