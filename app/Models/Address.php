<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'city',
        'street',
        'postal_code',
        'building_number'
    ];

    public $timestamps = false;

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }
}
