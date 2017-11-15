<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Object extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city_id',
        'category_id',
        'customer_id',
        'contractor_id',
        'region_id',
        'price',
        'status',
        'description',
        'work_description',
        'additional_info',
        'maps_lat',
        'maps_lng',
        'finished_at',
        'finished_year',
    ];

    protected $table = "objects";

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function regionId()
    {
        return $this->belongsTo(Region::class);
    }

    public function category()
    {
        return $this->belongsTo(ObjectCategory::class, 'category_id');
    }

    public function categoryId()
    {
        return $this->belongsTo(ObjectCategory::class);
    }

    public function documentId()
    {
        return $this->belongsTo(Document::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'object_asset');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerId()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function contractorId()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function cityId()
    {
        return $this->belongsTo(City::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getCreatedAtAttribute($date)
    {
        return $this->attributes['created_at'] = Carbon::parse($date)->toDateString();
    }

    public function scopePublished($query)
    {
        $query->where('status', '=', 'PUBLISHED');
    }

    public function scopeUnpublished($query)
    {
        $query->where('status', '=', 'PUBLISHED');
    }

    public function scopeFields($query)
    {
        return $query->select('id', 'name', 'address', 'price', 'price_status', 'description', 'work_description', 'additional_info', 'maps_lat', 'maps_lng', 'category_id', 'region_id');
    }

    public static function yearsInterval()
    {
        return static::select('finished_at')->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->finished_at)->format('Y');
            });
    }

    public static function addresses()
    {
        $results = array_column(Object::select('address')->get()->toArray(), 'address');
        return $results;
    }

    public static function sumaRepairs($data)
    {
        $suma = Object::select(DB::raw("SUM(price) as suma"))
            ->whereIn('id', $data)
            ->get()
            ->toArray();

        $suma = array_column($suma, 'suma');

        $total_suma = (integer)$suma[0];

        return $total_suma;
    }

    public static function sumaRepairsAll()
    {
        $suma = Object::select(DB::raw("SUM(price) as suma"))
            ->get()
            ->toArray();

        $suma = array_column($suma, 'suma');

        return $suma;
    }

    public function scopeSelected($query)
    {
        $query->select('id', 'name', 'address', 'maps_lat', 'maps_lng', 'price', 'category_id', 'region_id');

        $query->where('status', '=', 'PUBLISHED');

        return $query;
    }

    public function scopeFilter($query, $params)
    {
        if ($id = array_get($params, 'id')) {
            $query = $query->where('id', '=', $id);
        }

        if ($address = array_get($params, 'address')) {
            $query = $query->where('address', '=', $address);
        }

        if ($title_like = array_get($params, 'title_like')) {
            $query = $query->where('title', 'like', ('%' . $title_like . '%'));
        }

        if ($category_id = array_get($params, 'category_id')) {
            $query = $query->whereIn('category_id', $category_id);
        }

        if ($city_id = array_get($params, 'city_id')) {
            $query = $query->whereIn('city_id', $city_id);
        }

        if ($customer_id = array_get($params, 'customer_id')) {
            $query = $query->whereIn('customer_id', $customer_id);
        }

        if ($contractor_id = array_get($params, 'contractor_id')) {
            $query = $query->whereIn('contractor_id', $contractor_id);
        }

        if ($years = array_get($params, 'year')) {
            $query = $query->whereIn('finished_year', $years);
        }

        if (array_has($params, 'price_from') && array_has($params, 'price_to')) {
            $query = $query->whereBetween('price', [array_get($params, 'price_from'), array_get($params, 'price_to')]);
        }

        $query->where('status', '=', 'PUBLISHED');

        return $query;
    }
}
