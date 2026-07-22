<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\PlayerController;

//First page here
Route::get('/', function () {
    return view('welcome');
});
//Registration route here
Route::get("/register", [AuthController::class, "show"])->name("registerShow");
Route::post("/register", [AuthController::class, "register"])->name("register");
//Login if the user already has an account
Route::get("/login", [AuthController::class, "loginShow"])->name("loginShow");
Route::post("/login", [AuthController::class, "login"])->name("login");
//Logout route
Route::post("/logout", [AuthController::class, "logout"])->name("logout");
//After login should be protected with auth.
Route::middleware("auth")->group(function(){
    Route::get("/dashboard", [BattleController::class, "index"])->name("dashboard");
    Route::get('/player/create', [PlayerController::class, 'create'])->name('createPlayer');
    Route::post('/player', [PlayerController::class, 'store'])->name('player.store');
    //battle routes here.
    Route::prefix("battle")->name("battle.")->group(function (){
        Route::get("/", [BattleController::class, "show"])->name("show");
        
        Route::post("/attack", [BattleController::class, "attack"])->name("attack");

        Route::post("/end", [BattleController::class, "endBattle"])->name("end");
    });
});
