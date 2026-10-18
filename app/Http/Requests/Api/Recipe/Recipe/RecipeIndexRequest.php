<?php

namespace App\Http\Requests\Api\Recipe\Recipe;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RecipeIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tags'         => 'nullable,array',
            'tags.*'       => 'required|uuid|distinct|exists:tags,id',
            'categories'   => 'nullable,array',
            'categories.*' => 'required|uuid|distinct|exists:categories,id',
            'meal_type'    => 'nullable|string|in:' . implode(',', \App\Enum\Recipe\MealTypeEnum::values()),
            'country_id'   => 'nullable|uuid|exists:countries,id',
            'search'       => 'nullable|string|max:255',
        ];
    }
}
