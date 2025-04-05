<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'unique:App\Models\User,email', 'email', 'max:255'],
            'password' => ['required', 'min:6', 'string', 'confirmed'],
            'rememberMe' => ['string'],
        ];
    }


    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'login' => 'Логин',
            'password' => 'Пароль',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => 'Поле является обязательным',
            'unique' => 'Аккаунт с такой почтой уже существует',
            'email' => 'Проверьте правильность почты',
            '*.string' => 'Поле должно быть строкой',
            'confirmed' => 'Пароли не совпадают',
            'email.max' => 'Слишком длинное значение почты',
            'password.min' => 'Пароль должен быть не менее 6 символов'
        ];
    }
}
