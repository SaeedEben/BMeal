<?php

namespace App\Http\Requests\Api\Plan;

use App\Enum\Recipe\MealTypeEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MealPlanStoreRequest extends FormRequest
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
            'week_start_date' => 'required|date_format:Y-m-d|after_or_equal:tomorrow',
            'items' => 'required|array',
            'items.*' => 'required|array',
            'items.*.recipe_id' => 'required|uuid|exists:recipes,id',
            'items.*.date' => 'required|date_format:Y-m-d H:i',
            'items.*.meal_type' => 'required|string|in:'.implode(',', array_column(MealTypeEnum::cases(), 'value')),
            'items.*.servings' => 'required|integer',
        ];
    }
}
