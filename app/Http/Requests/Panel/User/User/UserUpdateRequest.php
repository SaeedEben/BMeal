<?php

namespace App\Http\Requests\Panel\User\User;

use App\Models\User\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property User   $user
 * @property string $city_id
 * @property string $role_id
 */
class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelUpdate', $this->user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'     => [
                'required',
                'string',
                'email',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'full_name' => 'nullable|string',
            'role_id'   => 'required|string|exists:roles,uuid',
        ];
    }
}
