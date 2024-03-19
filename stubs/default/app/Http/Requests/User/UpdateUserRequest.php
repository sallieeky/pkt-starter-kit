<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'username'  => ['required','string', 'unique:users,username,'.$this->user->user_id.',user_id'],
            'name'      => ['required','string'],
            'npk'       => ['nullable','string','unique:users,npk,'.$this->user->user_id.',user_id'],
            'email'     => ['nullable','email', 'unique:users,email,'.$this->user->user_id.',user_id'],
            'role'      => ['nullable','array'],
        ];
    }
}
