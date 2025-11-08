<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableForReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|integer|exists:status,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ];
    }
}
