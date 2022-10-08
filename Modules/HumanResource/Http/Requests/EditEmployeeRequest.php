<?php

namespace Modules\HumanResource\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditEmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required',
            'name' => 'required|min:3|max:255',
            'father_name' => 'required|min:3|max:255',
            'cnic' => 'required|min:13',
            'address' => 'required|min:5|max:255',
            'phone' => 'required|min:7',
            'position' => 'required|min:3|max:255',
            'type' => 'required',
            'salary' => 'numeric|min:1',
            'date_of_joining' => 'required|date',
            'profile_pic' => 'image|mimes:jpg,png,jpeg|dimensions:min_width=100,min_height=100|max:2048',
            // 'cnic_front' => 'image|mimes:jpg,png,jpeg',
            // 'cnic_back' => 'image|mimes:jpg,png,jpeg',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
