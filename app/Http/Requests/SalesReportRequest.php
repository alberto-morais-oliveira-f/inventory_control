<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SalesReportRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'start_date'   => ['nullable', 'date'],
            'end_date'     => ['nullable', 'date'],
            'product_sku'  => ['nullable', 'string'],
        ];
    }
}
