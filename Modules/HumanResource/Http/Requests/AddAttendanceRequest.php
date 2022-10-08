<?php

namespace Modules\HumanResource\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddAttendanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'employee' => 'required',
            'shift' => 'required',
            'day' => 'required|date',
            'time_in' => 'required|date_format:Y-m-d H:i:s',
            'time_out' => 'date_format:Y-m-d H:i:s|after:time_in',
        ];
    }

    public function messages()
    {
        return [
            'day.required' => 'Date is required.',
            'time_in.required' => 'Check In time is required.',
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
