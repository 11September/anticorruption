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
        $categoriesWithRelations = [];

        $categories = static::with('objects')->get();
        
        foreach ($categories as $category) {
            if(count($category->objects) > 0) {
                array_push($categoriesWithRelations, $category);
            }
        }

        return $categoriesWithRelations;
    }

    public static function allCategories()
    {
        $categories = static::select('id', 'name', 'image')->get();

        return $categories;
    }
}
