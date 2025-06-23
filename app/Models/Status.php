<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = [
        'name'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
    public $timestamps = false;
}
