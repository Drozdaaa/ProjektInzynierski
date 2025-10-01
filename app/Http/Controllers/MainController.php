<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(Request $request)
    {
        $restaurants = Restaurant::with('address')
            ->when(
                $request->city,
                fn($q, $city) =>
                $q->whereHas('address', fn($a) => $a->where('city', 'like', "%$city%"))
            )
            ->when(
                $request->street,
                fn($q, $street) =>
                $q->whereHas('address', fn($a) => $a->where('street', 'like', "%$street%"))
            )
            ->paginate(6);

        if ($request->ajax()) {
            return view('shared.restaurant-list', compact('restaurants'));
        }

        return view('main.index', compact('restaurants'));
    }
}
