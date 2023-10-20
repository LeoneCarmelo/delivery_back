<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dishes = Dish::orderByDesc('id')->get();
        return view('admin.dishes.index', compact('dishes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.dishes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreDishRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDishRequest $request)
    {
        $val_data = $request->validated();
        if($request->hasFile('photo')){
            $img_path = Storage::disk('public')->put('uploads', $val_data['photo']);
            $val_data['photo'] = $img_path;
        };
        $slug = Dish::generateSlug($val_data['name']);
        $val_data['slug'] = $slug;
        $dish = new Dish;
        $dish->fill($val_data);
        $dish->save();
        return redirect()->route('admin.dishes.index', $dish)->with('message', 'Il nuovo piatto è stato aggiunto correttamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function show(Dish $dish)
    {
        return view('admin.dishes.show', compact('dish'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function edit(Dish $dish)
    {
        return view('admin.dishes.edit', compact('dish'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateDishRequest  $request
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateDishRequest $request, Dish $dish)
    {
        $visible = $request->has('visible') ? 1 : 0;
        $val_data = $request ->validated();
        if($request->hasFile('photo')){
            $img_path = Storage::disk('public')->put('uploads', $val_data['photo']);
            $val_data['photo'] = $img_path;
        };
        $val_data['visible'] = $visible;
        $dish->update($val_data);
        return redirect()->route('admin.dishes.index', $dish)->with('message', 'Il nuovo piatto è stato modificato correttamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Dish  $dish
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dish $dish)
    {
        $dish->delete();
        return to_route('admin.dishes.index')->with('message', 'Il piatto è stato eliminato correttamente');
    }
}
