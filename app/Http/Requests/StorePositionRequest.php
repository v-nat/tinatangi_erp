<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name'          => 'required|string|max:255|unique:positions,name',
            'department_id' => 'required|integer|exists:departments,id',
            'level'         => 'required|string|in:staff,supervisor,manager,ceo',
            'base_salary'   => 'required|numeric|min:0',
        ];
    }
}
