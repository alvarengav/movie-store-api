<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
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
            'title' => 'string',
            'description' => 'string|nullable',
            'stock' => 'numeric|min:0',
            'rental_price' => 'numeric|min:0',
            'sale_price' => 'numeric|min:0',
            'availability' => 'boolean'
        ];
    }
}
