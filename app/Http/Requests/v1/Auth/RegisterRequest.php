<?php

namespace App\Http\Requests\v1\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed:password_confirmation',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field must be a valid email address.',
            'email.max:255' => 'The email field must be of 255 characters.',
            'email.unique:users' => 'The email field must be unique.',
            'password.required' => 'The password field is required.',
            'password.min:8' => 'The password field must be at least 8 characters.',
            'password.confirmed' => 'The password and password confirmation does not match.',
            'password_confirmation.required' => 'The password confirmation field is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
