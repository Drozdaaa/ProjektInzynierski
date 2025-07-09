<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{
    protected $fillable = [
        'name',
        'description',
        'menu_id',
        'dish_id'
        ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

}
