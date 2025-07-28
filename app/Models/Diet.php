<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    protected $fillable = [
        'name',
        'description',
        ];

    public $timestamps = false;

    public function dishes()
    {
        return $this->belongsToMany(Dish::class);
    }

}
