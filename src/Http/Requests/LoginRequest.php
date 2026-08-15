<?php

namespace Domain\DomainGenerator\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            config('domain-generator.auth.login_field', 'email') => ['required'],
            'password' => ['required', 'string'],
        ];
    }
}