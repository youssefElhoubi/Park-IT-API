<?php

namespace App\Http\Controllers;

use App\Models\parking;
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
            parking::find($id)->update([
                "available_spots" => parking::find($id)->available_spots - 1
            ]);
            if ($reservation) {
                return response()->json(['message' => 'reservation created successfully', "reservation" => $reservation], Response::HTTP_CREATED);
            }
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function cancelReservation(Request $req, $id)
    {
        try {
            $userId = auth()->user()->id;
            $reservation = reservation::find($id);
            if (!$reservation) {
                return response()->json(['message' => 'reservation not found'], Response::HTTP_NOT_FOUND);
            }
            if ($reservation->user_id != $userId) {
                return response()->json(['message' => 'you dont have permission to cancel this reservation'], Response::HTTP_FORBIDDEN);
            }
            $parkingId = $reservation->parking_id;
            parking::find($parkingId)->update([
                "available_spots" => parking::find($id)->available_spots + 1
            ]);
            $reservation->delete();
            return response()->json(['message' => 'reservation cancelled successfully'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function editReservation(Request $req, $id)
    {
        try {
            $userId = auth()->user()->id;
            $reservation = Reservation::find($id);

            // Check if reservation exists
            if (!$reservation) {
                return response()->json(['message' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
            }

            // Check if the authenticated user owns the reservation
            if ($reservation->user_id != $userId) {
                return response()->json(['message' => 'You do not have permission to modify this reservation'], Response::HTTP_FORBIDDEN);
            }

            // Validate request
            $validatedData = $req->validate([
                'startDate' => "sometimes|date_format:Y-m-d H:i:s|after_or_equal:now",
                'endDate' => "sometimes|date_format:Y-m-d H:i:s|after:startDate"
            ]);

            // Check for overlapping reservations
            $overlap = Reservation::where('parking_id', $reservation->parking_id)
                ->where('id', '!=', $id) // Exclude current reservation
                ->where(function ($query) use ($validatedData) {
                    if (isset($validatedData['startDate']) && isset($validatedData['endDate'])) {
                        $query->whereBetween('start_time', [$validatedData['startDate'], $validatedData['endDate']])
                            ->orWhereBetween('end_time', [$validatedData['startDate'], $validatedData['endDate']])
                            ->orWhere(function ($q) use ($validatedData) {
                                $q->where('start_time', '<=', $validatedData['startDate'])
                                    ->where('end_time', '>=', $validatedData['endDate']);
                            });
                    }
                })
                ->exists();

            if ($overlap) {
                return response()->json(['message' => 'Another reservation conflicts with the selected time'], Response::HTTP_CONFLICT);
            }

            // Update reservation details
            if (isset($validatedData['startDate'])) {
                $reservation->start_time = $validatedData['startDate'];
            }
            if (isset($validatedData['endDate'])) {
                $reservation->end_time = $validatedData['endDate'];
            }

            $reservation->save();

            return response()->json(['message' => 'Reservation updated successfully', "reservation" => $reservation], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], Response::HTTP_BAD_REQUEST);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function myReservation(Request $req){
        $user = auth()->user();
        
    }
}
