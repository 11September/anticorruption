<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    public static function pages()
    {
        $pages = Page::select('title', 'slug')->where('status', 'ACTIVE')->get();

        return $pages;
    }

    public function scopePublished($query)
    {
        $query->where('status', '=', 'ACTIVE');
    }
}
