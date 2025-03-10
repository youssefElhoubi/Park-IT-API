<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\parking;
use App\Models\user;
use Illuminate\Validation\ValidationException;

class parking_controller extends Controller
{
    public function addPrking(Request $req)
    {
        try {
            $req->validate([
                'name' => 'required|string|min:3|max:255',
                "totale_spost" => 'required|numeric|gt:0',
            ]);
            $parkingSpot = parking::create([
                'name' => $req->name,
                'totale_spost' => $req->totale_spost,
                "availabel_spots" => $req->totale_spost,
            ]);
            return response()->json(["message" => "new parking added successfully", "marpking" => $parkingSpot], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'details' => $e->errors()
            ], 422);
        }
    }
}
