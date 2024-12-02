<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $alamats = Alamat::paginate(10);
        if (Auth::user()->role == 'KONSUMEN'){
            $alamats = Alamat::where('user_id', Auth::user()->id)->paginate(10);
        }
        return view('alamat.index', ['alamats' => $alamats]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $users = User::paginate(10);
        $provinces = Province::all();
        $cities = City::all();
        return view('alamat.create', ['users' => $users, 'provinces' => $provinces, 'cities' => $cities]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $data = $request->all();
        Alamat::create($data);
        return redirect('/alamat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $alamat = Alamat::find($id);
        $users = User::paginate(10);
        $provinces = Province::all();
        $cities = City::all();
        return view('alamat.edit', [
            'alamat' => $alamat,
            'users' => $users,
            'provinces' => $provinces,
            'cities' => $cities
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data = $request->all();
        $alamat = Alamat::find($id);
        $alamat->update($data);
        return redirect('/alamat');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
