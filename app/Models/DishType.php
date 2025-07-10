<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DishType extends Model
{
    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

}
