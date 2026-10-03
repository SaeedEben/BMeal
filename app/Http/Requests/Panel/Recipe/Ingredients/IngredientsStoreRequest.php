<?php

namespace App\Http\Requests\Panel\Recipe\Ingredients;

use App\Enum\Common\StatusEnum;
use App\Models\Recipe\Ingredient;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IngredientsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelStore', Ingredient::class);
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
            'slug' => 'required|string|max:150|unique:ingredients,slug',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:'.implode(',', StatusEnum::values()),
        ];
    }
}
