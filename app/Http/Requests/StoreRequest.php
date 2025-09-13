<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
        return [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'gender' => 'required|string',
            'birth_date' => 'required|date',
            'age' => 'required|integer|min:18',
            'phone_number' => ['required', 'string', 'max:13', 'regex:/^(09|\+639)\d{9}$/'],
            'citizenship' => 'required|string|max:50',
            'department' => 'required|integer',
            'position' => 'required',
            'direct_supervisor' => 'required',
            'sss' => 'required|integer',
            'pagibig' => 'required|integer',
            'philhealth' => 'required|integer',
            'salary' => 'required|numeric|min:0',
        ];
    }
}
