<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';
    protected $fillable = [];

    public function objects()
    {
        return $this->belongsToMany(Object::class, 'object_asset');
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class);
    }


}
