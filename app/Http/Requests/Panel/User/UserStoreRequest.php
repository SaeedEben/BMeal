<?php

namespace App\Http\Requests\Panel\User;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $city_id
 * @property string $role_id
 */
class UserStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelStore', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone'     => 'required|string|unique:users',
            'full_name' => 'nullable|string',
            'username'  => 'nullable|string|unique:users',
            'city_id'   => 'required|exists:cities,id',
            'password'  => 'required|string|min:8|confirmed',
            'role_id'   => 'required|string|exists:roles,uuid',
        ];
    }
}
