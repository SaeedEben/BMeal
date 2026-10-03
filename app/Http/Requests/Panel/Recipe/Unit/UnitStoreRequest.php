<?php

namespace App\Http\Requests\Panel\Recipe\Unit;

use App\Models\Recipe\Unit;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enum\Recipe\UnitTypeEnum;
use App\Enum\Common\StatusEnum;

class UnitStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelStore', Unit::class);
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
            "slug"              => 'required|string|max:100|unique:units,slug',
            "type"              => 'required|string|in:' . implode(',', UnitTypeEnum::values()),
            "conversion_factor" => 'nullable|numeric',
            "is_metric"         => 'required|boolean',
            "status"            => 'nullable|string|in:' . implode(',', StatusEnum::values()),
        ];
    }
}
