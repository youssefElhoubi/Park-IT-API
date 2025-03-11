<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\reservation;

class Reservaton_controller extends Controller
{
    public function Reserve(Request $req, int $id)
    {
        try {
            $UserId = auth()->user()->id;
            $validatedData = $req->validate([
                'startDate' => "required|date_format:Y-m-d H:i:s|after_or_equal:now",
                'endDate' => "required|date_format:Y-m-d H:i:s|after:startDate"
            ]);
            $startDate = $req->startDate;
            $endDate = $req->endDate;
            $overlap = reservation::where('parking_id', $id)
                ->where(function ($query) use ($validatedData) {
                    $query->whereBetween('startDate', [$validatedData['startDate'], $validatedData['endDate']])
                        ->orWhereBetween('endDate', [$validatedData['startDate'], $validatedData['endDate']])
                        ->orWhere(function ($q) use ($validatedData) {
                            $q->where('startDate', '<=', $validatedData['startDate'])
                                ->where('endDate', '>=', $validatedData['endDate']);
                        });
                })
                ->exists();
            if ($overlap) {
                return response()->json(['message' => 'some one have alredy taken this date in this parking'], Response::HTTP_CONFLICT);
            }
            $reservation = reservation::create([
                "user_id" => $UserId,
                "parking_id" => $id,
                "start_time" => $startDate,
                "end_time" => $endDate
            ]);
            if ($reservation) {
                return response()->json(['message' => 'reservation created successfully', "reservation" => $reservation], Response::HTTP_CREATED);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
   
}
