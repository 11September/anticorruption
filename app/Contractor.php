<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
	public $timestamps = false;
	
    public function objects(){
    	return $this->hasMany(Object::class);
    }

    public static function contractors()
    {
        $contractorsWithRelations = [];

        $contractors = static::with('objects')->get();
        
        foreach ($contractors as $contractor) {
            if(count($contractor->objects) > 0) {
                array_push($contractorsWithRelations, $contractor);
            }
        }

        return $contractorsWithRelations;
    }
}
