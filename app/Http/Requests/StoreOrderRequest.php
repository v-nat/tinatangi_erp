<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Change this to true if authorization isn't needed, or add role/permission checks
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'order_items' => ['required', 'array', 'min:1'],
            'grand_total' => ['required', 'numeric', 'min:0'],

            'order_items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'order_items.*.quantity' => ['required', 'integer', 'min:1'],

            'order_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'order_items.*.total_price' => ['required', 'numeric', 'min:0'],

            'order_type' => 'required|string|in:dine-in,take-out,delivery',
        ];
    }
}
