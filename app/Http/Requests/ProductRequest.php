<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ProductRequest extends Request
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
            'name.*'=>'required|min:3|max:100',
            'barcode.*'=>'max:255',
            'sale_price.*'=>'required|numeric|min:0',
            'notify_quantity.*'=>'required|integer|min:0',
            'warehouse.*'=>'required|exists:warehouse,id',
            'initial_stock.*'=>'integer|min:0',
            'purchase_price.*'=>'numeric|min:0',
            'supplier.*'=>'exists:supplier,id',
            // 'pct_code'=>'required|max:8',
            // 'tax_rate'=>'required|number',
        ];
    }
}
