<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public function objectId()
    {
        return $this->belongsTo(Object::class, 'id');
    }
}
