<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ToornamentRankTeam extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tournament_ids' => 'required|numeric',
            'stage_ids' => 'required|numeric',
            'group_ids' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'team_id.required' => 'Team id is required!',
            'team_id.numeric' => 'Team id must be numeric!',
            'stage_ids.required' => 'Stage id is required!',
            'stage_ids.numeric' => 'Stage id must be numeric!',
            'group_ids.required' => 'Group id is required!',
            'group_ids.numeric' => 'Group id must be numeric!',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'errors' => $validator->errors(),
        ], 422);

        throw new HttpResponseException($response);
    }
}
