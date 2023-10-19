<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    $user = Auth::user(); 
    $user_id = $user->id; 

    // Ora puoi usare $user_id nella tua query
    $restaurants = Restaurant::where('user_id', $user_id)->paginate(5);
    return view('admin.restaurants.index', compact('restaurants'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $user_id = $user->id;
    
        // Verifico se esiste già un ristorante associato all'utente loggato
        $existingRestaurant = Restaurant::where('user_id', $user_id)->first();
    
        // Se esiste già un ristorante, redirect alla show
        if ($existingRestaurant) {
            return redirect()->route('admin.restaurant.show', $existingRestaurant);
        }
    
        // Se non esiste alcun ristorante, mostra il form per crearne uno nuovo
        return view('admin.restaurants.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRestaurantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $restaurant = new Restaurant;
        // associo ristorante all'utente loggato
        $restaurant->user_id = $request->user()->id;
        $restaurant->fill($data);
        $restaurant->save();
        return redirect()->route('admin.restaurant.show', $restaurant);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.edit ', compact('restaurant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRestaurantRequest  $request
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $data = $request->all();
        $restaurant->update($data);
        return redirect()->route('admin.restaurant.show', $restaurant);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return redirect()->route('admin.restaurant.index');
    }
}