<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PurchaseRequest extends Request
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
            'product'=>'array|required',
            'product.*' => 'integer|required|min:0',
            'customer' => 'integer|required|min:0',
            'stock.*' => 'integer|required|min:0',
            'price.*'=>'required|numeric|min:0',
            'date'=>'required|date',
            'warehouse.*'=>'required|integer|min:0'
        ];
    }
}
