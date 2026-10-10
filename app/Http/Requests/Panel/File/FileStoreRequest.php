<?php

namespace App\Http\Requests\Panel\File;

use App\Enum\File\TypeEnum;
use App\Models\File\File;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FileStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelStore', File::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file'      => 'required|file|max:10240',
            'file_type' => 'required|string|in:' . implode(',', array_column(TypeEnum::cases(), 'value')),
        ];
    }
}
