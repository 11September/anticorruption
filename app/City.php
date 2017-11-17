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
        $cities = static::select('id', 'name')->has('objects')->get();

        return $cities;
    }

}
