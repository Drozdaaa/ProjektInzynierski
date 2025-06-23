<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
     protected $fillable = [
        'name',
        'description',
        'address_id'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
    public $timestamps = false;
}
