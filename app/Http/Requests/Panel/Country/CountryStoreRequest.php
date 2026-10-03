<?php

namespace App\Http\Requests\Panel\Country;

use App\Enum\Common\StatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Country\Country;
use Illuminate\Validation\Rule;

class CountryStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelStore', Country::class);
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
            'code'        => [
                'required',
                'string',
                'max:10',
                Rule::unique('countries', 'code'),
            ],
            'slug'        => [
                'required',
                'string',
                'max:150',
                Rule::unique('countries', 'slug'),
            ],
            'description' => 'nullable|string',
            'flag_id'     => 'nullable|uuid|exists:files,id',
            'status' => 'nullable|string|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
