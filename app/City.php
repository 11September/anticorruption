<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = "cities";

    protected $fillable = [];

    public function objects(){
        return $this->hasMany(Object::class);
    }

    public static function cities()
    {
        $citiesWithRelations = [];

        $cities = static::with('objects')->get();
        
        foreach ($cities as $city) {
            if(count($city->objects) > 0) {
                array_push($citiesWithRelations, $city);
            }
        }

        return $citiesWithRelations;
    }

    public static function allCities()
    {
        $cities = static::select('id', 'name')->get();

        return $cities;
    }

}
