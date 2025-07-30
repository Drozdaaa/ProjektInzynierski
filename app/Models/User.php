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

    public $timestamps = false;

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

    public function restaurants()
    {
        return $this->hasOne(Restaurant::class);
    }

    public function isAdmin(): bool
    {
        return $this->role_id == 1;
    }

    public function isUser()
    {
        return $this->role_id == 2;
    }

    public function isManager(): bool
    {
        return $this->role_id == 3;
    }
    public function managedEvents()
    {
        return $this->hasMany(Event::class, 'manager_id');
    }
}
