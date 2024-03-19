<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'username'  => ['required','string','unique:users,username'],
            'name'      => ['required','string'],
            'npk'       => ['nullable','string','unique:users,npk'],
            'email'     => ['nullable','email','unique:users,email'],
            'password'  => ['required','string'],
            'role'      => ['nullable','array'],
        ];
    }
}