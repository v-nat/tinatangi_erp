<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee_id');
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'gender' => 'required|string',
            'birth_date' => 'required|date',
            'age' => 'required|integer|min:18',
            'phone_number' => ['required', 'string', 'max:13', 'regex:/^(09|\+639)\d{9}$/'],
            'citizenship' => 'required|string|max:50',
            'department' => 'required|integer',
            'level'=> 'required',
            'position_id' => 'required',
            'supervisor_id' => 'required',
            'sss' => 'required|numeric|min:0',
            'pagibig' => 'required|numeric|min:0',
            'philhealth' => 'required|numeric|min:0',
            'base_salary' => 'required|numeric|min:0',

            'schedule_title' => 'nullable|string|max:255',
            'days_of_week' => [
                'nullable',
                'required_with:time_in,time_out',
                'array',
                'min:6',
                'max:6',
            ],
            'days_of_week.*' => [
                'required_with:days_of_week',
                'integer',
                'min:0',
                'max:6'
            ],
            'time_in' => [
                'nullable',
                'required_with:days_of_week,time_out',
                'date_format:H:i', // Match HH:MM format
            ],
            'time_out' => [
                'nullable',
                'required_with:days_of_week,time_in',
                'date_format:H:i',
                'after:time_in',
            ],
            'color' => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.before_or_equal' => 'Employee must be at least 18 years old.',
            'phone_number.regex' => 'The phone number must be a valid 11-digit Philippine mobile number (starting with 09).',
            'days_of_week.required_with' => 'Days of week are required when setting a schedule time.',
            'days_of_week.min' => 'Please select exactly 6 days for the schedule.', // Use min/max for 6
            'days_of_week.max' => 'Please select exactly 6 days for the schedule.',
            'days_of_week.*.integer' => 'Invalid day selected.',
            'days_of_week.*.min' => 'Invalid day selected.',
            'days_of_week.*.max' => 'Invalid day selected.',
            'time_in.required_with' => 'Start time is required when setting schedule days or end time.',
            'time_out.required_with' => 'End time is required when setting schedule days or start time.',
            'time_out.after' => 'End time must be after the start time.',
        ];
    }

    /**
     * Get the column name for the unique rule.
     *
     * @return string
     */
    protected function getIdColumnName(): string
    {
        // Determine if the employeeId is the 'id' or 'user_id' in the 'users' table
        // Adjust this if your 'users' table's primary key is not 'id'
        return 'id';
    }
}
