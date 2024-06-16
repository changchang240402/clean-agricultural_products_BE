<?php

namespace App\Http\Requests\Item;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateItemRequest extends FormRequest
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
            'item_name' => 'required|string|max:150',
            'product_id' => 'required|exists:products,id',
            'describe' => 'required|string',
            'total' => 'required|numeric',
            'price' => 'required|numeric|between:0.00,99999999.99',
            'type' => 'required|numeric|min:50|max:200',
            'price_type' => 'required|numeric|between:0.00,999999999999.99',
            'image' => 'required|file|mimes:jpeg,jpg,png,gif,bmp,svg,webp',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'data' => $validator->errors()
        ], 422));
    }
}
