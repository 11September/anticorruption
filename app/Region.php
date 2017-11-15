<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function Objects()
    {
        return $this->hasMany(Object::class);
    }

    static function countRelatedObjects($objects){
    	$regions = [];
    	$objectsAmount = [];
        $count = 0;
        
        foreach ( $objects as $object ) {
            $region = $object->region;
            if( $region !== null && strlen( $object->maps_lat ) > 0 && strlen( $object->maps_lng ) > 0) {
                array_push($regions, $region->id);
            }
    	}
        $objectsAmount = array_count_values($regions);
    	return $objectsAmount;
    }
}
