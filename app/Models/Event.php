<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        'number_of_people',
        'description',
        'status_id',
        'event_type_id',
        'restaurant_id',
        'user_id',
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

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'event_menu')
            ->withPivot('amount');
    }


    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'event_room');
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status && $status !== 'all') {
            $query->whereHas('status', fn($q) => $q->where('name', $status));
        }
        return $query;
    }
}
