<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'description',
        'price',
        'cleaning_duration',
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
