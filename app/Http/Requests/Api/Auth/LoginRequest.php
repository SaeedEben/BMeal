<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'    => ['required', 'string', 'email','exists:users,email'],
            'password' => ['required', 'string'],
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
            'email.exists' => __('responses.errors.auth.login_failed'),
        ];
    }
}
