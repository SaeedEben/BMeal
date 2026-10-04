<?php

namespace App\Http\Requests\Panel\Recipe\Recipe;

use App\Enum\Common\StatusEnum;
use App\Enum\Recipe\DifficultyEnum;
use App\Models\Recipe\Recipe;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property Recipe $recipe
 */
class RecipeUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelUpdate', $this->recipe);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:250',
            'slug' => [
                'required',
                'string',
                'max:250',
                Rule::unique('recipes', 'slug')->ignore($this->route('recipe')),
            ],
            'status'            => 'nullable|string|in:'.implode(',', StatusEnum::values()),
            'description'       => 'nullable|string',
            'image'             => 'nullable|uuid|exists:files,id',
            'prep_time_minutes' => 'nullable|numeric|max:1000',
            'cook_time_minutes' => 'nullable|numeric|max:1000',
            'servings'          => 'nullable|numeric|max:1000',
            'difficulty'        => [
                'required',
                'string',
                Rule::in(DifficultyEnum::values()),
            ],
            'calories_per_serving' => 'nullable|numeric',

            // Relations ----------------------------------------------
            'country_id'                  => 'required|uuid|exists:countries,id',
            'ingredients'                 => 'nullable|array',
            'ingredients.*'               => 'required|array:ingredient_id,unit_id,quantity,notes,sort_order,status',
            'ingredients.*.ingredient_id' => 'required|uuid|distinct|exists:ingredients,id',
            'ingredients.*.unit_id'       => 'nullable|uuid|exists:units,id',
            'ingredients.*.quantity'      => 'nullable|numeric|min:0|max:99999999.99',
            'ingredients.*.notes'         => 'nullable|string|max:255',
            'ingredients.*.sort_order'    => 'nullable|integer|min:0|max:65535',
            'ingredients.*.status'        => 'nullable|string|in:'.implode(',', StatusEnum::values()),
            'categories'                  => 'nullable|array',
            'categories.*'                => 'required|uuid|distinct|exists:categories,id',
            'tags'                        => 'nullable|array',
            'tags.*'                      => 'required|uuid|distinct|exists:tags,id',
            'steps'                       => 'nullable|array',
            'steps.*.step_number'         => 'required|integer|min:1|max:65535|distinct',
            'steps.*.instruction'         => 'required|string',
            'steps.*.image'               => 'nullable|string|max:500',
            'steps.*.status'              => 'nullable|string|in:'.implode(',', StatusEnum::values()),
        ];
    }
}
