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
            'sss' => 'required|numeric|min:0',
            'philhealth' => 'required|numeric|min:0',
            'pagibig' => 'required|numeric|min:0',
        ];
    }
}
