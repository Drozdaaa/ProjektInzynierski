<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_allergy');
    }

    public function dishes()
    {
        return $this->belongsToMany(Dish::class);
    }

}
