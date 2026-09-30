<?php

namespace App\Http\Requests\Panel\User;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelIndex', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search'   => 'nullable|string',
            'city'     => 'nullable|string|exists:cities,id',
            'role'     => 'nullable|string|exists:roles,uuid',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}
