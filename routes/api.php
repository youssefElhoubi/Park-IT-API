<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;
use App\Http\Controllers\parking_controller;
use App\Http\Controllers\Reservaton_controller;

Route::middleware("unAuth")->group(function () {
    Route::post('/singup', [auth::class, "signUp"]);
    Route::post('/singin', [auth::class, "login"]);
});
Route::middleware(["auth:sanctum","admin"])->group(function(){
    Route::post('parking/creat', [parking_controller::class, "addPrking"]);
    Route::patch('parking/update/{id}', [parking_controller::class, "updateParking"]);
    Route::delete('parking/delete/{id}', [parking_controller::class, "updateParking"]);
});
Route::middleware(["auth:sanctum","user"])->group(function(){
    Route::post("reservation/reserve/{id}",[Reservaton_controller::class,"Reserve"]);
    Route::get("parking/search/{search}",[parking_controller::class, "search"]);
    Route::post("reservation/cancel/{id}",[Reservaton_controller::class,"cancelReservation"]);
    Route::patch("reservation/update/{id}",[Reservaton_controller::class,"editReservation"]);
    Route::patch("reservation/myresrvation",[Reservaton_controller::class,"myReservations"]);
});
