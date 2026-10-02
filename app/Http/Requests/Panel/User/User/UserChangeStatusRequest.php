<?php

namespace App\Http\Requests\Panel\User\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User\User;
use App\Enum\Common\StatusEnum;
/**
 * @property User $user
 * @property string $status
 */
class UserChangeStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('PanelChangeStatus', $this->user);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "status"    => 'required|string|in:' . implode(',', array_column(StatusEnum::cases(), 'value')),
        ];
    }
}
