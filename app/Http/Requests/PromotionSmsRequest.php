<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PromotionSmsRequest extends Request
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
            // 'customer_id'=>'required',
            'message'=>'required',
        ];
    }
    public function messages()
    {
        return [
            // 'customer_id.required'=>'Please Select a Customer to Send SMS',
            'message.required'=>'Please Write Message to Send SMS',
        ];
    }
}
