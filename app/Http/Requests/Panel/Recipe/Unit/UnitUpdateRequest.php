<?php

namespace App\Http\Requests\Panel\Recipe\Unit;

use App\Models\Recipe\Unit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enum\Recipe\UnitTypeEnum;
use App\Enum\Common\StatusEnum;
use Illuminate\Validation\Rule;

/**
 * @property Unit $unit
 */
class UnitUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelUpdate', $this->unit);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
         return [
            "name"              => 'required|string|max:100',
            "symbol"            => 'required|string|max:50',
            "slug"              => [
                'required',
                'string',
                'max:100',
                Rule::unique('units' , 'slug')->ignore($this->route('unit'))
            ],
            "type"              => 'required|string|in:' . implode(',', UnitTypeEnum::values()),
            "conversion_factor" => 'nullable|numeric',
            "is_metric"         => 'required|boolean',
            "status"            => 'nullable|string|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
