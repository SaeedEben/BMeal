<?php

namespace App\Http\Requests\Api\Shopping;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ShoppingListCheckRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        $date = $this->input('date');

        if (is_string($date)) {
            $this->merge(['date' => str_replace(' ', '_', strtolower(trim($date)))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => [
                'required',
                'string',
                Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'all_day']),
            ],
            'week' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'array'],
            'items.*.item_name' => ['required', 'string', 'max:150', 'distinct'],
            'items.*.is_completed' => ['required', 'boolean'],
        ];
    }
}
