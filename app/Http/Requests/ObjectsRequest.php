<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObjectsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id'                => 'integer',
            'name'              => 'string',
            'address'           => 'string',
            'category_id'       => 'integer',
            'city_id'           => 'integer',
            'customer_id'       => 'integer',
            'contractor_id'     => 'integer',
            'price'             => 'digits',
            'price_status'      => 'in:provided,paid,pending',
            'description'       => '',
            'work_description'  => '',
            'additional_info'   => 'string',
            'maps_lat'          => 'string',
            'maps_lng'          => 'string',
            'finished_at'       => 'date',
        ];
    }
}
