<?php

namespace App\Http\Requests\Panel\Recipe\Tag;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Recipe\Tag;
use App\Enum\Common\StatusEnum;
use Illuminate\Validation\Rule;

/**
 * @property Tag $tag
 */
class TagUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelUpdate', $this->tag);
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
            'slug'        => [
                'required',
                'string',
                'max:150',
                Rule::unique('tags' , 'slug')->ignore($this->route('tag'))
            ],
            'status'      => 'nullable|string|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
