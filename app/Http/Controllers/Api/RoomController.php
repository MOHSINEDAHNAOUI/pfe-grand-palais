<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\DynamicPrice;
use Illuminate\Http\Request;
 
class RoomController extends Controller
{
    public function index(Request $request)
    {
        $checkIn  = $request->check_in;
        $checkOut = $request->check_out;

        $rooms = Room::with(['roomType', 'images', 'amenities'])
            ->when($request->room_type_id, fn($q) => $q->where('room_type_id', $request->room_type_id))
            ->get()
            ->map(function ($room) use ($checkIn, $checkOut) {
                // Determine dynamic status if dates provided
                if ($checkIn && $checkOut) {
                    $isBooked = $room->reservations()
                        ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                        ->where('check_in', '<', $checkOut)
                        ->where('check_out', '>', $checkIn)
                        ->exists();
                    
                    if ($isBooked) {
                        $room->status = 'occupied';
                    } elseif ($room->status === 'occupied') {
                        // If it was statically occupied but no reservation for these dates, mark as available for the 3D model
                        $room->status = 'available';
                    }
                }
                return $room;
            });

        return response()->json($rooms);
    }
 
    public function search(Request $request)
    {
        $request->validate([
            'check_in'  => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults'    => 'required|integer|min:1',
            'children'  => 'nullable|integer|min:0',
        ]);
 
        $guests   = $request->adults + ($request->children ?? 0);
        $checkIn  = $request->check_in;
        $checkOut = $request->check_out;
 
        $rooms = Room::with(['roomType', 'images', 'amenities'])
            ->where('status', 'available')
            ->whereHas('roomType', fn($q) => $q->where('max_capacity', '>=', $guests))
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                  ->where('check_in', '<', $checkOut)
                  ->where('check_out', '>', $checkIn);
            })
            ->get()
            ->map(function ($room) use ($checkIn, $checkOut) {
                $nights = \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut);
                $price  = DynamicPrice::where('room_type_id', $room->room_type_id)
                    ->whereBetween('date', [$checkIn, $checkOut])
                    ->avg('price') ?? $room->roomType->base_price;
                $room->period_price = round($price * $nights, 2);
                $room->nights       = $nights;
                return $room;
            });

        return response()->json(['data' => $rooms]);
    }

    public function types()
    {
        return response()->json(RoomType::where('is_active', true)->get());
    }
 
    public function show(Room $room)
    {
        $room->load(['roomType', 'images', 'amenities', 'reviews' => function ($q) {
            $q->where('is_approved', true)->with('user')->latest()->take(10);
        }]);
 
        $room->average_rating = $room->reviews->avg('rating');
        return response()->json($room);
    }
}