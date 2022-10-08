<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StockAdjustmentRequest extends Request
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
            'product_id'=>'required|array|bail',
            'product_id.*'=>'required|exists:products,id',
            'is_purchase'=>'required|boolean',
            'warehouse_id'=>'required|integer|exists:warehouse,id',
            'quantity.*'=>'required|min:1',
            'date'=>'required|date',
            'supplier_id'=>'integer|min:1'
        ];
    }
}
