<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set to true if authorization is handled elsewhere (e.g., middleware)
        // Or add logic here: return auth()->user()->can('create schedules');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_id'  => ['required', 'integer', 'exists:employees,id'],
            'title'        => ['nullable', 'string', 'max:255'],
            'days_of_week' => ['required', 'array', 'min:1'],
            'days_of_week.*' => ['required', 'integer', 'min:0', 'max:6'],
            'time_in'      => ['required', 'date_format:H:i:s'],
            'time_out'     => ['required', 'date_format:H:i:s', 'after:time_in'],
            'color'        => ['nullable', 'string', 'max:7'],
            'description'  => ['nullable', 'string'],
            // 'startRecur'   => ['nullable', 'date'],
            // 'endRecur'     => ['nullable', 'date', 'after_or_equal:startRecur'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'days_of_week.required' => 'At least one day of the week must be selected.',
            'days_of_week.*.integer' => 'Invalid day selected.',
            'days_of_week.*.min' => 'Invalid day selected.',
            'days_of_week.*.max' => 'Invalid day selected.',
            'time_out.after' => 'The end time must be after the start time.',
        ];
    }
}
