<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\parking;
use App\Models\user;
use Symfony\Component\HttpFoundation\Response;
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
            ], 400);
        }
    }
    public function updateParking(Request $req, $id)
    {
        try {
            $validatedData = $req->validate([
                'name' => 'sometimes|string|min:3|max:255',
                'totale_spost' => 'sometimes|numeric|gt:0',
            ]);
            $parkingSpot = parking::find($id);
            if (!$parkingSpot) {
                return response()->json(["messag" => "parking not found"], 404);
            }

            if ($req->has('name')) {
                $parkingSpot->name = $req->name;
            }
            if ($req->has('totale_spost')) {
                $parkingSpot->totale_spost = $req->totale_spost;
                // Ensure available spots do not exceed total spots
                if ($req->totale_spost - $parkingSpot->available_spots < 0) {
                    return response()->json(["messag" => "you have reservation more than the reservation wate for the reservatino to end chance it "], 400);
                }
                $parkingSpot->available_spots = $req->totale_spost;
            }
            $parkingSpot->save();
            return response()->json(["messag" => "parking have been updated succefuly "], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'details' => $e->errors()
            ], 400);
        }
    }
    public function deleteParking($id)
    {
        try {
            $parkingSpot = Parking::find($id);

            if (!$parkingSpot) {
                return response()->json(["message" => "Parking not found"], Response::HTTP_NOT_FOUND);
            }

            $parkingSpot->delete();

            return response()->json(["message" => "Parking deleted successfully"], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                "error" => "Something went wrong",
                "details" => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
}
