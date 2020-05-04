<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class CompetitionSaveRequest extends FormRequest
{
    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => __('competitions/create.name'),
            'info' => __('competitions/create.info'),
            'registration_end' => __('competitions/create.registration_end'),
            'max_teams' => __('competitions/create.max_teams'),
            'tops_number' => __('competitions/create.num_winners'),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->user()->commissioner;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $types = $this->getCompetitionTypes();

        return [
            'name' => 'required_unless:request,edit|max:255|unique:competitions,name',
            'info' => 'max:1000',
            'registration_end' => 'required|date|after:today',
            'self_confirm' => 'required|integer|min:0|max:3',
            'any_max_teams' => 'boolean',
            'max_teams' => 'integer|min:2|max:1024',
            'winner_points' => 'required|integer|min:2|max:3',
            'tops_number' => 'required|min:1|max:10',
            'races' => 'required|array',
            'races.*' => 'exists:races,id|distinct',
            'type' => 'required_unless:request,edit|in:' . $types,
        ];
    }

    /**
     * Auto discovery of the competition types available.
     *
     * @return string
     */
    private function getCompetitionTypes()
    {
        $result = implode(',', competitionTypes());

        return $result;
    }
}
