<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\EquipmentReturn;
use Illuminate\Support\Facades\Auth;

class BookingReturnApiController extends Controller
{
    public function submitReturn(Request $request, Booking $booking)
    {
        $request->validate([
            'return_photo' => 'required|image|max:5120',
            'user_condition' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($booking->equipmentReturn) {
            return response()->json(['error' => 'Return already submitted.'], 409);
        }

        $now = now();

        if ($now->lt($booking->start_datetime)) {
            return response()->json(['error' => 'Cannot return before the booking start time.'], 400);
        }

        $photoPath = $request->file('return_photo')->store('equipment_returns', 'public');

        $equipmentReturn = EquipmentReturn::create([
            'booking_id' => $booking->id,
            'return_date' => $now,
            'return_photo_path' => $photoPath,
            'user_condition' => $request->user_condition,
            'condition_status' => 'pending',
            'notes' => $request->notes,
        ]);

        $booking->update([
            'status' => 'return submitted' // Use this if you add this status to your enum
            // OR use an existing status that fits your workflow, for example:
            // 'status' => 'pending' // If you want admin to review it again
        ]);

        return response()->json([
            'message' => 'Return submitted successfully. Awaiting staff confirmation.',
            'data' => $equipmentReturn
        ], 201);
    }

    public function verifyReturn(Request $request, Booking $booking)
    {
        $request->validate([
            'condition_status' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $equipmentReturn = $booking->equipmentReturn;
        if (!$equipmentReturn) {
            return response()->json(['error' => 'No return to verify.'], 404);
        }

        $equipmentReturn->update([
            'condition_status' => $request->condition_status,
            'notes' => $request->notes ?? $equipmentReturn->notes,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);

        $facilityItem = $booking->facilityItem;
        $facilityItem->condition_status = $request->condition_status;
        $facilityItem->save();

        $booking->update([
            'status' => 'completed'
        ]);

        return response()->json([
            'message' => 'Return verified and equipment condition updated.',
            'data' => $equipmentReturn
        ]);
    }
}