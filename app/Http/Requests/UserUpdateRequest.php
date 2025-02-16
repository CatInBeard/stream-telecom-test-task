<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('user');

        return [
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $userId . ',id' ,
            'password' => 'string|min:6',
            'role' => 'string|in:client,admin',
        ];
    }
}
