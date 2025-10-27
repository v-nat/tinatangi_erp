<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Assuming any authenticated user can attempt this
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee_id'); // Get employee_id for uniqueness check on update

        // Base employee rules
        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            // Update email rule to ignore current employee on edit
            'email' => [
                'required',
                'email',
                $employeeId ? Rule::unique('users', 'email')->ignore($employeeId) : Rule::unique('users', 'email'),
            ],
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|digits:4', // Enforce exactly 4 digits
            'gender' => 'required|string',
            'birth_date' => 'required|date|before_or_equal:' . Carbon::now()->subYears(18)->format('Y-m-d'), // Must be at least 18
            // 'age' => 'required|integer|min:18', // Age is calculated, not required from form
            'phone_number' => ['required', 'string', 'regex:/^(09|\+639)\d{9}$/'], // Valid PH phone
            'citizenship' => 'required|string|max:50',
            'department' => 'required|integer|exists:departments,id', // Ensure department exists
            'level'=> 'required|string', // Consider Enum validation if using Laravel Enums
            'position_id' => 'required|integer|exists:positions,id', // Ensure position exists
            'supervisor_id' => 'required|integer|exists:employees,id', // Ensure supervisor exists
            'base_salary' => 'required|numeric|min:0',

            // --- ADDED Schedule Rules ---
            // Make schedule fields nullable or conditionally required
            // Example: required ONLY IF time_in or days_of_week is provided
            'schedule_title' => 'nullable|string|max:255',
            'days_of_week' => [
                'nullable', // Make the whole array nullable if schedule is optional
                'required_with:time_in,time_out', // Require if either time is set
                'array',
                'min:6', // Ensure at least 6 are selected if provided
                'max:6', // Ensure no more than 6 are selected if provided
            ],
            'days_of_week.*' => [ // Validate each item within the array
                'required_with:days_of_week', // Required if the array itself is present
                'integer',
                'min:0',
                'max:6'
            ],
            'time_in' => [
                'nullable', // Nullable if schedule is optional
                'required_with:days_of_week,time_out', // Require if days or end time is set
                'date_format:H:i', // Validate format (HH:MM or HH:MM:SS)
            ],
            'time_out' => [
                'nullable', // Nullable if schedule is optional
                'required_with:days_of_week,time_in', // Require if days or start time is set
                'date_format:H:i',
                'after:time_in', // Ensure end time is after start time
            ],
            'color' => 'nullable|string|max:7', // Basic validation for hex color
            'description' => 'nullable|string',
             // --- END Schedule Rules ---
        ];

         // Remove email validation rule for updates (handled by ignore rule above)
        // Adjust password handling for updates if necessary
        // if ($this->isMethod('put') || $this->isMethod('patch')) {
        //     // Example: Make password optional on update
        //     // $rules['password'] = 'nullable|string|min:8|confirmed';
        // }

        return $rules;

    }

     /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'birth_date.before_or_equal' => 'Employee must be at least 18 years old.',
            'phone_number.regex' => 'The phone number must be a valid 11-digit Philippine mobile number (starting with 09).',
            'days_of_week.required_with' => 'Days of week are required when setting a schedule time.',
            'days_of_week.size' => 'Please select exactly 6 days for the schedule.',
            'days_of_week.*.integer' => 'Invalid day selected.',
            'days_of_week.*.min' => 'Invalid day selected.',
            'days_of_week.*.max' => 'Invalid day selected.',
            'time_in.required_with' => 'Start time is required when setting schedule days or end time.',
            'time_out.required_with' => 'End time is required when setting schedule days or start time.',
            'time_out.after' => 'End time must be after the start time.',
        ];
    }

}
