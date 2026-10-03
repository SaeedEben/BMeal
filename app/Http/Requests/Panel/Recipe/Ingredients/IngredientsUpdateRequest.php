<?php

namespace App\Http\Requests\Panel\Recipe\Ingredients;

use App\Enum\Common\StatusEnum;
use App\Models\Recipe\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngredientsUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $ingredient = $this->route('ingredient');

        return $ingredient instanceof Ingredient
            && ($this->user()?->can('PanelUpdate', $ingredient) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'slug' => [
                'required',
                'string',
                'max:150',
                Rule::unique('ingredients', 'slug')->ignore($this->route('ingredient')),
            ],
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:'.implode(',', StatusEnum::values()),
        ];
    }
}
