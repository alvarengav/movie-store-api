<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'string|nullable',
            'stock' => 'numeric|min:0',
            'rental_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'default_rate' => 'required|numeric|min:0',
            'availability' => 'boolean'
        ];
    }
}
