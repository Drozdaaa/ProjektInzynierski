<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DishType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

}
