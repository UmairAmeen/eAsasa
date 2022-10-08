<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateCustomerRequest extends Request
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
            'name'=>'required|min:3',
            'type'=>'required|max:10',
            //'phone'=>'unique:customer,phone,NULL,id,deleted_at,NULL',
            'openingbalance'=>'numeric|min:0',
            'remainder_days'=>'integer|min:0|required',
            'last_contact_on'=>'date',
            'notes'=>'max:500'
        ];
    }
}
