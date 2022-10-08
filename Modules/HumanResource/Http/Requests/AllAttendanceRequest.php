<?php

namespace Modules\HumanResource\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AllAttendanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'day' => 'required|date',
            'employee.*' => 'required',
            'shift.*' => 'required_if:attendance.*,present',
            'time_in.*' => 'required_if:attendance.*,present',
            'time_out.*' => 'nullable|after:time_in.*',
        ];
    }

    public function messages()
    {
        return [
            'day.required' => 'Date is required.',
            'time_in.*' => 'Check In time is required',
            'time_out.before' => 'Check In time should be less than Check Out Time',
            'time_out.after' => 'Check Out time should be greater than Check In Time',
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
