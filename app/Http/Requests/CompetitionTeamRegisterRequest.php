<?php

namespace App\Http\Requests;

use App\Models\Competition;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;

class CompetitionTeamRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $team = Team::find(request()->team_id);
        return $this->user()->id === $team->user_id &&
            !$team->competition_id &&
            $team->registeredCompetition->isEmpty() &&
            $this->route('competition')->races->contains($team->race);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'team_id' => 'required|integer',
        ];
    }
}
