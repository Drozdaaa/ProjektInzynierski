<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'role_id',
    ];
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function allergies()
    {
        return $this->belongsToMany(Allergy::class, 'user_allergy');
    }

}
