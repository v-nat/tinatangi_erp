<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollSettingsRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

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
