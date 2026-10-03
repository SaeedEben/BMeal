<?php

namespace App\Http\Requests\Panel\Country;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enum\Common\StatusEnum;
use App\Models\Country\Country;
use Illuminate\Validation\Rule;

/**
 * @property Country $country
 */
class CountryUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelUpdate', $this->country);
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
                Rule::unique('countries', 'code')->ignore($this->route('country')),
            ],
            'slug'        => [
                'required',
                'string',
                'max:150',
                Rule::unique('countries', 'slug')->ignore($this->route('country')),
            ],
            'description' => 'nullable|string',
            'flag_id'     => 'nullable|uuid|exists:files,id',
            'status' => 'nullable|string|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
