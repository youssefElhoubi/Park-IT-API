<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;
use App\Http\Controllers\parking_controller;
use App\Http\Controllers\Reservaton_controller;

Route::middleware("unAuth")->group(function () {
    Route::post('/signup', [auth::class, "signUp"]);
    Route::post('/signin', [auth::class, "login"]);
});
Route::middleware(["auth:sanctum","admin"])->group(function(){
    Route::post('parking/create', [parking_controller::class, "addPrking"]);
    Route::patch('parking/update/{id}', [parking_controller::class, "updateParking"]);
    Route::delete('parking/delete/{id}', [parking_controller::class, "updateParking"]);
    Route::get('parking/statistics', [parking_controller::class, "parkingStatistics"]);
});
Route::middleware(["auth:sanctum","user"])->group(function(){
    Route::post("reservation/reserve/{id}",[Reservaton_controller::class,"Reserve"]);
    Route::get("parking/search/{search}",[parking_controller::class, "search"]);
    Route::post("reservation/cancel/{id}",[Reservaton_controller::class,"cancelReservation"]);
    Route::patch("reservation/update/{id}",[Reservaton_controller::class,"editReservation"]);
    Route::get("reservation/myresrvation",[Reservaton_controller::class,"myReservations"]);
});
