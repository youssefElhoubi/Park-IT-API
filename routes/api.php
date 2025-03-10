<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;
use App\Http\Controllers\parking_controller;

Route::middleware("unAuth")->group(function () {
    Route::post('/singup', [auth::class, "signUp"]);
    Route::post('/singin', [auth::class, "login"]);
});
Route::middleware(["auth:sanctum","admin"])->group(function(){
    Route::post('parking/creat', [parking_controller::class, "addPrking"]);
    Route::patch('parking/update/{id}', [parking_controller::class, "updateParking"]);
    // Route::delete('parking/delete/{id}', [parking_controller::class, "updateParking"]);

});
