<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ObjectCategory extends Model
{
    protected $table = "object_categories";
    protected $fillable = [];

    public function objects()
    {
        return $this->hasMany(Object::class, 'category_id');
    }

    public static function categories()
    {
        $categories = static::select('id', 'name', 'image')->has('objects')->get();

        return $categories;
    }
}
