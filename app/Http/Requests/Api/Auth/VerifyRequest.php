<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifyRequest extends FormRequest
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
            'challenge_token' => ['required', 'string'],
            'purpose' => ['required', Rule::in(['register', 'forget_password'])],
            'code' => ['required', 'digits:6'],
            'password' => [
                'required_if:purpose,forget_password',
                'string',
                'min:8',
                'confirmed',
                'prohibited_unless:purpose,forget_password',
            ],
            'password_confirmation' => ['prohibited_unless:purpose,forget_password'],
        ];
    }
}
