<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request){

        $login = $request->validate([
            'email'=>'required',
            'password'=>'required',
        ]);
    
        if(!Auth::attempt($login)){
            return response(['message'=>'Invalid credentials.']);
        }
    
        $accessToken = Auth::user()->createToken('authToken')->accessToken;
    
        return response(['user'=>Auth::user(),'access_token'=> $accessToken]);

    }
    
}
