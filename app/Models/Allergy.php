<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergy extends Model
{
    protected $fillable = [
        'name',
        'description',
        'menu_id'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_allergy');
    }
    public $timestamps = false;
}
