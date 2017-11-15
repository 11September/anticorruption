<?php

namespace App;

//use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $guarded = [];

    protected $with = 'user';

//    public function getCreatedAtAttribute($date)
//    {
//        return $this->attributes['created_at'] = Carbon::parse($date)->diffForHumans();
//    }

    public function userId()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function objectId()
    {
        return $this->belongsTo(Object::class, 'id');
    }

    public function object()
    {
        return $this->belongsTo(Object::class);
    }

    public static function markNotRead()
    {
        return $comments = Comment::where('status_admin', '=' ,'UNREAD');
    }
}
