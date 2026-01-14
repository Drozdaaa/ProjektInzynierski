<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function events()
    {
        return $this->hasMany(Event::class);
    }

}
