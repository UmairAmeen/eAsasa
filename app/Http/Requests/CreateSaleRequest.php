<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateSaleRequest extends Request
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
            'sale_price.*'=>'required|numeric|min:0',
            'quantity.*'=>'required|numeric',
            'warehouse.*'=>'required|exists:warehouse,id',
            'product.*'=>'required|exists:products,id',
            'customer'=>'required|exists:customer,id',
            'date'=>'required|date',
            'shipping'=>'numeric|min:0'
        ];
    }
}
