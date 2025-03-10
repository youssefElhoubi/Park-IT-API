<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class auth extends Controller
{
    public function signUp(Request $req)
    {
        try {
            $validated = $req->validate([
                "email" => "required|email",
                "password" => "required|min:6",
                "name" => "required|max:255",
            ]);
            $user = User::where('email', $req->email)->first();
            if ($user) {
                return response()->json(['message' => 'Email already exists'], 400);
            }
            $user = User::create([
                'name' => $req->name,
                'email' => $req->email,
                'password' => Hash::make($req->password),
            ]);
            $token = $user->createToken(env("INCREPTION_TOKEN"))->plainTextToken;
            return response(["token" => $token]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                "details" => $e->errors()
            ], 400);
        }
    }
    public function login(Request $req)
    {
        try {
            $validated = $req->validate([
                "email" => "required|email",
                "password" => "required|min:6",
            ]);
            $user = User::where('email', $req->email)->first();
            if (!$user) {
                return response()->json(['message' => 'somthing is wrong try again '], 400);
            }
            if ($user) {
                if (!Hash::check($req->password, $user->password)) {
                    return response()->json(['message' => 'somthing is wrong try again '], 400);
                } else {
                    $token = $user->createToken(env("INCREPTION_TOKEN"))->plainTextToken;
                    return response(["token" => $token]);
                }
            }
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                "details" => $e->errors()
            ], 400);
        }
    }
}
