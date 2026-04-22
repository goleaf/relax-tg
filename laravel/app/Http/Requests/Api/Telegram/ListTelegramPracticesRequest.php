<?php

namespace App\Http\Requests\Api\Telegram;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ListTelegramPracticesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'active_only' => ['sometimes', 'boolean'],
            'day' => ['nullable', 'integer', 'between:1,29'],
            'experience_level_id' => ['nullable', 'integer', 'exists:experience_levels,id'],
            'focus_problem_id' => ['nullable', 'integer', 'exists:focus_problems,id'],
            'locale' => ['nullable', 'string', 'max:10'],
            'meditation_type_id' => ['nullable', 'integer', 'exists:meditation_types,id'],
            'module_choice_id' => ['nullable', 'integer', 'exists:module_choices,id'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ];
    }
}
