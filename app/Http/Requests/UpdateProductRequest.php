<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- Make sure to import this

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Assuming your route middleware handles auth
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                // This rule ensures the name is unique,
                // but ignores the product currently being edited.
                Rule::unique('products')->ignore($this->route('product')->id),
            ],
            'base_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'product_category_id' => [
                'required',
                'integer',
                'exists:product_categories,id',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            // For updates, the image is optional (nullable)
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:10240', // 10MB
            ],
        ];
    }
}
