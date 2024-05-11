<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
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
            'email' => 'required|string|min:6|max:50|email|unique:users',
            'password' => 'required|min:8|max:20',
            'confirm_password' => 'required|same:password',
            'name' => 'required|string|max:50',
            'phone' => 'required|string|regex:/^\d{10}$/|unique:users',
            'address' => 'required|string|min:40',
            'birthday' => ['nullable', 'date', function ($attribute, $value, $fail) {
                if (empty($value)) {
                    return;
                }
                $eighteenYearsAgo = now()->subYears(18);
                $fiftyFiveYearsAgo = now()->subYears(55);
                if (strtotime($value) > strtotime($eighteenYearsAgo) || strtotime($value) < strtotime($fiftyFiveYearsAgo)) {
                    $fail('Bạn phải từ 18 đến 55 tuổi');
                }
            }],
            'license_plates' => 'nullable|string|regex:/^\d{2}[A-Z]{1}-\d{3}\.\d{2}$/|unique:users',
            'driving_license_number' => 'nullable|string|regex:/^\d{12}$/|unique:users',
            'vehicles' => 'nullable|string|max:50',
            'payload' => 'nullable|numeric|min:100|max:10000',
            'role' => 'required|integer|in:1,2,3',
            'status' => 'required|integer|in:0,1'
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
