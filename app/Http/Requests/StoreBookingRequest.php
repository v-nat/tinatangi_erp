<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreBookingRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',

            // 2. Updated date rule
            'date' => [
                'required',
                'date',
                'after_or_equal:today',
                Rule::unique('bookings')->where(function ($query) {
                    return $query->where('time', $this->input('time'));
                })
            ],

            'time' => 'required|date_format:H:i',
            'people' => 'required|integer|min:1',
            'message' => 'nullable|string',
        ];
    }

    /**
     * Get the custom messages for validation errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            // 3. Add a custom message for the unique rule
            'date.unique' => 'This date and time slot is already booked. Please choose another.',
        ];
    }
}
