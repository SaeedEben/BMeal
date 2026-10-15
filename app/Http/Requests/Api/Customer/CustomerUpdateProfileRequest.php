<?php

namespace App\Http\Requests\Api\Customer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CustomerUpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file'                     => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            'meal_reminder'            => 'nullable|boolean',
            'shopping_list_reminder'   => 'nullable|boolean',
            'weakly_planning_reminder' => 'nullable|boolean',
            'categories'               => 'nullable|array',
            'categories.*'             => 'required|uuid|distinct|exists:categories,id',
        ];
    }
}
