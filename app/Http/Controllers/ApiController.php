<?php

namespace App\Http\Controllers;

use App\Object;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function api(Request $request)
    {
        $data = Object::with(
            array(
                'category' => function ($query) {
                    $query->select('id', 'name');
                },
                'customer' => function ($query) {
                    $query->select('id', 'name', 'identification_customer');
                },
                'contractor' => function ($query) {
                    $query->select('id', 'name', 'identification_contractor');
                },
                'finances' => function ($query) {
                    $query->select('id', 'object_id', 'suma', 'status', 'date', 'description');
                },
            ))
            ->filter($request->all())
            ->get()
            ->toArray();


//        ADD and Delete some keys and values
        foreach ($data as $item) {

        }
//        ADD and Delete some keys and values

        dd($data);


        return
            [
                'data' => $data
            ];
    }
}
