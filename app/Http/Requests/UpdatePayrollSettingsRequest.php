<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Set this to true if you handle authorization elsewhere (e.g., in routes)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'sss_employee_rate'        => 'required|numeric|min:0|max:1',
            'sss_employer_rate'        => 'required|numeric|min:0|max:1',
            'philhealth_employee_rate' => 'required|numeric|min:0|max:1',
            'philhealth_employer_rate' => 'required|numeric|min:0|max:1',
            'pagibig_employee_rate'    => 'required|numeric|min:0|max:1',
            'pagibig_employer_rate'    => 'required|numeric|min:0|max:1',
        ];
    }
}
