<?php

namespace App\Http\Requests\Panel\Recipe\Category;

use App\Models\Recipe\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enum\Common\StatusEnum;


/**
 * @property Category $category
 */
class CategoryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelUpdate', $this->category);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'slug'        => [
                'required',
                'string',
                'max:150',
                Rule::unique('categories' , 'slug')->ignore($this->route('category'))
            ],
            'status'      => 'nullable|string|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
