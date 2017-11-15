<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	public $timestamps = false;
	
    public function objects(){
    	return $this->hasMany(Object::class);
    }

    protected static function customers()
    {
        $customersWithRelations = [];

        $customers = static::with('objects')->get();
        
        foreach ($customers as $customer) {
            if(count($customer->objects) > 0) {
                array_push($customersWithRelations, $customer);
            }
        }

        return $customersWithRelations;
    }
}
