<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientifyRequest extends FormRequest
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
            'hook' => 'required|array:event,target,id',
            'hook.event' => 'required|string',
            'hook.target' => 'required|string',
            'hook.id' => 'required|integer',
            'data' => 'required|array',
            'data.id' => 'required|integer',
            'data.status' => 'required|string',
            'data.Custom_Field' => 'array',
        ];
    }
}
