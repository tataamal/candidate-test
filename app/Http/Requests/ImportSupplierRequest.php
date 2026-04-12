<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSupplierRequest extends FormRequest
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
            'layups' => ['required', 'array', 'min:1'],

            'layups.*.id' => ['nullable', 'integer'],
            'layups.*.name' => ['required', 'string', 'max:255'],
            'layups.*.layers' => ['required', 'array', 'min:1'],

            'layups.*.layers.*.id' => ['nullable', 'integer'],
            'layups.*.layers.*.layer_order' => ['required', 'integer', 'min:1'],
            'layups.*.layers.*.thickness' => ['required', 'numeric', 'min:0'],
            'layups.*.layers.*.width' => ['required', 'numeric', 'min:0'],
            'layups.*.layers.*.angle' => ['required', 'numeric'],
        ];
    }
}
