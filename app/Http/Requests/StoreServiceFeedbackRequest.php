<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceFeedbackRequest extends FormRequest
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
            // Assuming you have these fields in your form
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'nullable|string|max:1000',

            'food_rating' => 'required|numeric|min:0.5|max:5',
            'staff_rating' => 'required|numeric|min:0.5|max:5',
            'environment_rating' => 'required|numeric|min:0.5|max:5',
            'overall_rating' => 'required|numeric|min:0.5|max:5',

            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * (Optional) Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'food_rating.required' => 'Please provide a rating for the food.',
            'staff_rating.required' => 'Please provide a rating for our staff.',
            'environment_rating.required' => 'Please provide a rating for the environment.',
        ];
    }
}
