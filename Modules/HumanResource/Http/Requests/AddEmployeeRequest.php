<?php

namespace Modules\HumanResource\Http\Requests;
// use Illuminate\validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AddEmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'father_name' => 'required|min:3|max:255',
            'cnic' => 'unique:employee|max:16|min:13',
            'address' => 'required|min:5|max:255',
            'phone' => 'numeric|min:7',
            'position' => 'required|min:2|max:255',
            // 'type' => ['required', Rule::in(['Daily Wage', 'Contract', 'Salary']),],
            'salary' => 'numeric|min:1',
            // 'description' => 'nullable',
            'date_of_joining' => 'required|date',
            // 'profile_pic' => 'nullable|image|mimes:jpg,png,jpeg|dimensions:min_width=100,min_height=100|max:2048',
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
