<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $restaurants = Restaurant::with('address')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->when($request->name, function($query, $name) {
                $query->where('name', 'like', "%$name%");
            })
            ->when($request->city, function($query, $city) {
                $query->whereHas('address', function($q) use ($city) {
                    $q->where('city', 'like', "%$city%");
                });
            })
            ->when($request->street, function($query, $street) {
                $query->whereHas('address', function($q) use ($street) {
                    $q->where('street', 'like', "%$street%");
                });
            })
            ->when($request->postal_code, function($query, $postal) {
                $query->whereHas('address', function($q) use ($postal) {
                    $q->where('postal_code', 'like', "%$postal%");
                });
            })
            ->paginate(6)
            ->withQueryString();

        if ($request->ajax()) {
            return view('shared.restaurant-list', compact('restaurants'));
        }

        return view('main.index', compact('restaurants'));
    }
}
