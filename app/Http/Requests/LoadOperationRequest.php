<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoadOperationRequest extends FormRequest
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
            'type' => ['required', 'string', 'exists:types,name'],
            'cost' => ['required', 'int', 'min:1'],
            'remaining_balance' => ['required', 'numeric', 'min:0'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
            'ref_no' => ['required', 'integer'],
            'item' => ['required', 'string'],
        ];
    }
}
