<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name',
        'capacity',
        'description',
        'price',
        'restaurant_id',
    ];

    public $timestamps = false;

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_room');
    }
}
