<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObjectsFilterRequest extends FormRequest
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
            'address'           => 'string',
            'category_id'       => 'array',
            'city_id'           => 'array',
            'customer_id'       => 'array',
            'contractor_id'     => 'array',
            'year'              => 'array',
            'price_from'        => 'string',
            'price_to'          => 'string',
        ];
    }
}
