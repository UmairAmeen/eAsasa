<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ValidateAppointmentRequest extends Request
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
            //
            'title'=>'required|max:150',
            'start'=>'required|date',
            // 'end'=>'date|after:start',
            'background_color'=>'required'
        ];
    }
}
