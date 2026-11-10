<?php

declare(strict_types=1);

namespace App\Http\Requests\v1\Auth;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordResetRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, Rule|ValidationRule|list<Rule|ValidationRule|string>|string>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->max(64)],
            'password_confirmation' => ['required'],
            'code' => ['required', 'string', 'exists:password_resets,code'],
        ];
    }
}
