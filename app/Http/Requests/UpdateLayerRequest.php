<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLayerRequest extends FormRequest
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
            'layer_order' => 'sometimes|integer|min:1',
            'thickness'   => 'sometimes|numeric|min:0',
            'width'       => 'sometimes|numeric|min:0',
            'angle'       => 'sometimes|numeric|between:-180,180',
        ];
    }
}
