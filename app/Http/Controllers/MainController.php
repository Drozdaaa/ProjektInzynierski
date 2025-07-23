<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        return view('main.index', [
            'restaurants' => Restaurant::all()
        ]);
    }
}
