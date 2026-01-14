<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;
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
