<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'unit_id' => ['required', 'exists:item_units,id'],
            'inventory_location_id' => ['nullable', 'exists:inventory_locations,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'integer'],
            'is_perishable' => ['nullable', 'boolean'],
        ];
    }
}
