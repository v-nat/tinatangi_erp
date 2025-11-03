<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Carbon\Carbon;

class StoreBatchPayrollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Set this to true if you're handling authorization
        // (e.g., checking if Auth::user()->can('create_payroll'))
        // For now, true will allow the request to pass.
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
            'employee_ids'   => ['required', 'array', 'min:1'],
            'employee_ids.*' => ['required', 'integer', 'exists:employees,id'],
            'start_date'     => ['required', 'date_format:Y-m-d'],
            'end_date'       => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $startDate = $this->input('start_date');
            $endDate = $this->input('end_date');

            if ($startDate && $endDate && !$validator->errors()->has('start_date') && !$validator->errors()->has('end_date')) {
                try {
                    $start = Carbon::parse($startDate);
                    $end = Carbon::parse($endDate);
                    if ($start->diffInDays($end) < 15) {
                        $validator->errors()->add(
                            'end_date',
                            'The payroll period must be at least 15 days long.'
                        );
                    }
                } catch (\Exception $e) {
                    $validator->errors()->add('start_date', 'The start date or end date is invalid.');
                }
            }
        });
    }
}
