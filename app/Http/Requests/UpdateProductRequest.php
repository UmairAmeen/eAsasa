<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateProductRequest extends Request
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
        if (session()->get('settings.products.enable_advance_fields')) {
            return [
                'name'=>'required|min:3|max:100',
                'barcode'=>'max:255',
                'sale_price'=>'required|numeric|min:0',
                'notify_quantity'=>'required|integer',
                'brand'=>'max:255',
                // 'pct_code'=>'required|max:8',
                // 'tax_rate'=>'required|number',
            ];    
        }
        else{
            return [
                'name'=>'required|min:3|max:100',
                'barcode'=>'max:255',
                'sale_price'=>'numeric|min:0',
                'notify_quantity'=>'integer',
                'brand'=>'max:255',
                // 'pct_code'=>'required|max:8',
                // 'tax_rate'=>'required|number',
            ];
    
        }
    }
}
