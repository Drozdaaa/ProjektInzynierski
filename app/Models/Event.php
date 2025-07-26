<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'date',
        'number_of_people',
        'description',
        'status_id',
        'event_type_id',
        'restaurant_id',
        'user_id',
        'menu_id',
        'manager_id',
    ];

    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
