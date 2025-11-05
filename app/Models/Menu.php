<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'price',
        'user_id',
        'event_id',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'menu_dish');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_menu');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
