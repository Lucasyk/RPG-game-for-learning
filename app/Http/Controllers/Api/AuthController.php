<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $validated = $request->validate(
            [
                "name" => "required|string|max:255",
                "email" => "required|string|email|max:255|unique:users",
                "password" => "required|string|max:255",
            ]
        );

        $user = User::create([
            "name"=>$validated["name"],
            "email"=>$validated["email"],
            "password"=>Hash::make($validated["password"]),
        ]);

        Auth::login($user);

        return redirect("/dashboard")->with("success", "Welcome to my ridiculous app");
    }

    public function login(Request $request){
        $validated = $request->validate(
            [
                "email"=>"required|email",
                "password"=>"required"
            ]
        );

        if(Auth::attempt($validated, $request->boolean("remember"))){
            $request->session()->regenerate();

            return redirect()->intended("/dashboard")->with("success", "Welcome back!!");
        }

        return back()->withErrors(["email"=>"The credentials did not match..."])
        ->onlyInput("email");

    }

    public function loginShow(){
        return view("login");
    }

    public function show(){
        return view("register");
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect("/")->with("success", "You have been logged out!!");
    }
}
