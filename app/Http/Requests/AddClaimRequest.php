<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AddClaimRequest extends Request
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
            'product'=>'required|exists:products,id',
            'customer'=>'exists:customer,id',
            'supplier'=>'required|exists:supplier,id',
            'warehouse'=>'required|exists:warehouse,id',
            'stock'=>'required|integer|min:0',
            'price'=>'required|numeric|min:0',
            'description'=>'max:300'
        ];
    }
}
