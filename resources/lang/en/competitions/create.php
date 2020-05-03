<?php

return [
    'header' => 'Your competitions',
    'name' => 'Competition name',
    'name_placeholder' => 'Enter your competition unique name',
    'info' => 'Competition information',
    'info_placeholder' => 'Enter your competition description',
    'logo' => 'Competition logo (optional)',
    'logo_placeholder' => 'Select your competition logo',
    'registration_end' => 'Registration end date',
    'registration_end_desc' =>
        'Teams won\'t be allowed to register after this date.
                        You can change this parameter later.',
    'confirmation' => [
        'label' => 'Matches confirmation',
        'any' => 'Any team',
        'both' => 'Only both teams',
        'commissioner' => 'Only commissioner',
        'description' =>
            'A settings for matches confirmation. You can forbid coaches to confirm results
             without your review first, only after both players confirm, or allow any coach to
             confirm results. As a commissioner,
             you will always be able to cancel any match results in the current round.',
    ],
    'max_teams' => 'Maximum number of teams',
    'no_limit' => 'No limit',
    'winner_points' => 'Winner points',
    'winner_points_desc' => 'Draw - 1 point, lose - 0 points, and you can change winner points to 2.',
    'num_winners' => 'Number of winners',
    'num_winners_desc' => 'Number of prize places. Use this to add a selected
                            number of top teams to the competition history.',

    'races' => 'Allowed races',
    'types' => [
        'name' => 'Competition types',
        'open_league' => 'Open league',
        'play_off' => 'Play Off',
        'group_rounds' => 'Group Rounds',
    ],

    'open_league' => [
        'max_games' => 'Maximum number of games per team',
        'no_limit' => 'No limit',
        'max_games_one_team' => 'Maximum number of games against one team',
        'one_team_row' => 'Allow to play against one team in a row',
        'play_off_num' => 'Play off teams number',
        'wo_po' => 'Without Play Off',
    ],

    'group_rounds' => [
        'size' => 'Group size',
        'play_off_num' => 'Play off teams number',
        'wo_po' => 'Without Play Off',
    ],

    'button' => 'Create competition',

];
