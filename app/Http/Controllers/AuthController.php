<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        //
        return view('login');
    }
    public function dologin(Request $request){
        // $email = $request->email;
        // $password = $request->password;
        // $user = \App\Models\User::Where('email', $email)->first();
        // if (empty($user)) {
        //     return redirect('/login')->with(
        //         'status_message',
        //         ['type' => 'danger', 'text' => 'email tidak ditemukan']
        //     );
        // }
        // return redirect('/user');

        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect('/user');
        }
        return redirect('/login')->with(
            'status_message',
            ['type' => 'danger', 'text' => 'user tidak ditemukan']
        );
    }
}
