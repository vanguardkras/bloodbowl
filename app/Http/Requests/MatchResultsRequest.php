<?php

namespace App\Http\Requests;

use App\Models\Competition;
use Illuminate\Foundation\Http\FormRequest;

class MatchResultsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'team_1' => 'required|integer',
            'team_2' => 'required|integer',
            'touchdowns_1' => 'required|integer|between:0,8',
            'touchdowns_2' => 'required|integer|between:0,8',
        ];
    }
}
