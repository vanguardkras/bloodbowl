<?php


namespace App\Services\CompetitionStrategy;


class GroupRounds extends Type
{
    /**
     * Validation rules for the parameters and parameters themselves.
     *
     * @var array
     */
    protected $validationRules = [
        'groups_size' => 'required|integer|min:2|max:512',
        'group_rounds_wo_po' => 'boolean',
        'group_rounds_play_off' => 'required|in:2,4,8,16,32,64',
    ];

    /**
     * Change competition instance parameters.
     */
    public function fillParameters()
    {
        $parameters = new \StdClass;
        $parameters->groups_size =  request()->groups_size;
        $parameters->group_rounds_play_off =  request()->group_rounds_wo_po
            ? 0 : request()->group_rounds_play_off;
        $this->competition->parameters = $parameters;
    }
}
