<?php

namespace App\Http\Requests;

class UpdateBasicInformationRequest extends Base
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
            'given_name' => 'required|string',
            // 'title' => '|string',
            'gender' => 'required|string',
            'birthday' => 'required|string',
            'activity_base_id' => 'required|numeric',
        ];
    }
}