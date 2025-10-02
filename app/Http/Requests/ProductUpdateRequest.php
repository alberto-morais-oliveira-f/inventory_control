<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sku' => ['required', 'string'],
            'name'=> ['required', 'string'],
            'description' => ['required', 'string'],
            'cost_price' => ['required', 'numeric'],
            'sale_price' => ['required', 'numeric'],
        ];
    }
}
