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

    public $timestamps = false;

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
        return $this->belongsToMany(Diet::class, 'dish_diet');
    }
    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'dish_allergy');
    }
}
