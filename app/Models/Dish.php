<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'dish_type_id',
        'restaurant_id'
    ];

    public function dishType()
    {
        return $this->belongsTo(DishType::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_dish');
    }

    public function diets()
    {
        return $this->hasMany(Diet::class);
    }
    public $timestamps = false;
}
