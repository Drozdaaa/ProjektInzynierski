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

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class);
    }
    public $timestamps = false;
}
