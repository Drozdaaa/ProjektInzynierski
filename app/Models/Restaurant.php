<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'description',
        'booking_regulations',
        'image',
        'address_id',
        'user_id'
    ];

    public $timestamps = false;

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
