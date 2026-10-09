<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->where(fn ($query) => $query->whereNotNull('email_verified_at')),
            ],
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Get custom messages for registration validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => __('responses.errors.auth.login_failed'),
        ];
    }
}
