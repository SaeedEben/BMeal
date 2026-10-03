<?php

namespace App\Http\Requests\Panel\Recipe\Tag;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Recipe\Tag;
use Illuminate\Validation\Rule;
use App\Enum\Common\StatusEnum;

class TagStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelStore', Tag::class);
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
                Rule::unique('tags' , 'slug'),
            ],
            'status'      => 'nullable|string|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
