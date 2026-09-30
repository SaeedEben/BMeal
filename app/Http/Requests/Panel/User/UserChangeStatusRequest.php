<?php

namespace App\Http\Requests\Panel\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;
use App\Enum\User\UserStatusEnum;
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
            "status"    => 'required|string|in:' . implode(',', array_column(UserStatusEnum::cases(), 'value')),
        ];
    }
}
