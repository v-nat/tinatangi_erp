<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');
        $ignoreUserId = optional($supplier)->user_id;

        return [
            'supplier_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($ignoreUserId),
            ],
            'phone_number' => [
                'required',
                'string',
                'max:13',
                'regex:/^(09|\+639)\d{9}$/',
            ],
        ];
    }
}
