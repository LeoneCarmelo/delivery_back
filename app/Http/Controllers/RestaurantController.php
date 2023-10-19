<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
            return redirect()->route('admin.restaurants.show', $existingRestaurant);
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
        $data = $this->validation($request->all());
        $img_path = Storage::disk('public')->put('uploads', $data['photo']);
        $data['photo'] = $img_path;

        $slug = Restaurant::generateSlug($val_data['name']);
        $val_data['slug'] = $slug;
      
        $restaurant = new Restaurant;
        // associo ristorante all'utente loggato
        $restaurant->user_id = $request->user()->id;
        $restaurant->fill($data);
        $restaurant->save();
        return redirect()->route('admin.restaurants.show', $restaurant)->with('message', 'A new dish has been added successfully');
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
        $data = $this->validation($request->all());
        if($request->hasFile('photo')){
            $img_path = Storage::disk('public')->put('uploads', $data['photo']);
            $data['photo'] = $img_path;
        };
        $restaurant->update($data);
        return redirect()->route('admin.restaurants.show', $restaurant)->with('message', 'The restaurant has been edited successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Restaurant  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Restaurant $restaurant)
    {
                // Delete image from storage
                if($restaurant->photo) Storage::delete($restaurant->photo);
                $restaurant->save();
        
        
        $restaurant->delete();
        return redirect()->route('admin.restaurants.index')->with('message', 'The restaurant has been deleted successfully');
    }


    private function validation($data)
    {
        $validator = Validator::make(
            $data,
            [
                'name' => 'required|max:60',
                'address' => 'required|min:5',
                'photo' => 'image|mimes:jpg,png,jpeg,gif,svg',
                'piva' => 'required|size:11'
              
            ],
            [
                'name.required' => 'Name is required.',
                'name.max' => 'The name must have a maximum of 60 characters.',
            
                'address.required' => 'The address is required.',
                'address.min' => 'The address must have a minimum of 5 characters.',

                'photo.image' => 'Must be an image.',
                'photo.mimes' => 'The image must be JPG, PNG, JPEG, GIF or SVG format.',

                'piva.required' => 'Vat is required',
                'piva.size' => 'Vat must have 11 characters',

            ]
        )->validate();
        return $validator;
    }
}