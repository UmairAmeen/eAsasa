<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateChequeRequest extends Request
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
            'date'=>'required|date',
            'type'=>'required',
            'customer.*'=>'integer|min:0',
            // 'warehouse.*'=>'required|exists:warehouse,id',
            'amount.*'=>'required|numeric|min:0',
            'bank.*'=>'max:190',
            'release_date.*'=>'date'
        ];
    }
}
