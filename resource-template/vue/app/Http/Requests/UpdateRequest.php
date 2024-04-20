<?php

namespace App\Http\Requests\ModelName;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModelNameRequest extends FormRequest
{
    /**
     * Determine if authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            Rules
        ];
    }
}
