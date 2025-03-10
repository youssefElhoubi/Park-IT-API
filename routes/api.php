<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth;

Route::post('/singup', [auth::class,"signUp"]);
Route::post('/singin', [auth::class,"login"]);
