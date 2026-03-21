<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'base_price' => 'required|numeric|min:0',
            'product_category_id' => 'required|integer|exists:product_categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.string' => 'The product name must be a valid string.',
            'name.max' => 'The product name cannot be longer than 255 characters.',
            'name.unique' => 'A product with this name already exists.',

            'base_price.required' => 'The base price is required.',
            'base_price.numeric' => 'The base price must be a number.',
            'base_price.min' => 'The base price must be at least 0.',

            'product_category_id.required' => 'Please select a product category.',
            'product_category_id.integer' => 'The category ID is invalid.',
            'product_category_id.exists' => 'The selected category does not exist in our records.',

            'description.string' => 'The description must be a valid string.',

            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'The image must be in one of the following formats: jpeg, png, jpg, gif.',
            'image.max' => 'The image cannot be larger than 10MB.',
        ];
    }
}
