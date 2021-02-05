<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Base extends FormRequest
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

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $data  = [
            'status'    => false ,
            'message'   => $validator->errors()
        ];
        $response = response()->json($data, 422);
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

}

