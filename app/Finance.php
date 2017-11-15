<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $table = 'finances';

    public function objectId()
    {
        return $this->belongsTo(Object::class, 'id');
    }

    public function object()
    {
        return $this->belongsTo(Object::class);
    }
}
