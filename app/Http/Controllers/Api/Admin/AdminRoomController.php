<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminRoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::with(['roomType', 'images', 'amenities'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->room_type_id, fn($q) => $q->where('room_type_id', $request->room_type_id))
            ->orderByRaw('CAST(number AS UNSIGNED)')
            ->get();

        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'         => 'required|string|unique:rooms,number',
            'floor'          => 'required|string',
            'room_type_id'   => 'required|exists:room_types,id',
            'surface'        => 'required|integer|min:1',
            'view'           => 'nullable|string',
            'smoking'        => 'nullable',
            'price_override' => 'nullable|numeric|min:0',
            'amenities'      => 'nullable|array',
            'images'         => 'nullable|array',
            'images.*'       => 'image|max:5120',
        ]);

        $room = Room::create([
            'number'         => $data['number'],
            'floor'          => $data['floor'],
            'room_type_id'   => $data['room_type_id'],
            'surface'        => $data['surface'],
            'view'           => $data['view'] ?? null,
            'smoking'        => $this->castBool($data['smoking'] ?? false),
            'price_override' => $data['price_override'] ?? null,
            'status'         => 'available',
        ]);

        if (!empty($data['amenities'])) {
            $room->amenities()->sync($data['amenities']);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('rooms/' . $room->id, 'public');
                $room->images()->create([
                    'path'       => $path,
                    'is_primary' => $index === 0,
                    'order'      => $index,
                ]);
            }
        }

        return response()->json($room->load('roomType', 'images', 'amenities'), 201);
    }

    public function show(Room $room)
    {
        return response()->json(
            $room->load(['roomType', 'images', 'amenities'])
        );
    }

    public function update(Request $request, Room $room)
    {
        $data = $request->validate([
            'number'         => 'sometimes|string|unique:rooms,number,' . $room->id,
            'floor'          => 'sometimes|string',
            'room_type_id'   => 'sometimes|exists:room_types,id',
            'surface'        => 'sometimes|integer|min:1',
            'view'           => 'nullable|string',
            'smoking'        => 'nullable',
            'price_override' => 'nullable|numeric|min:0',
            'amenities'      => 'nullable|array',
            'status'         => 'sometimes|in:available,occupied,maintenance,blocked',
            'images'         => 'nullable|array',
            'images.*'       => 'nullable|file|image|max:5120',
        ]);

        $room->update([
            'number'         => $data['number']       ?? $room->number,
            'floor'          => $data['floor']         ?? $room->floor,
            'room_type_id'   => $data['room_type_id'] ?? $room->room_type_id,
            'surface'        => $data['surface']       ?? $room->surface,
            'view'           => $data['view']          ?? $room->view,
            'smoking'        => array_key_exists('smoking', $data)
                                  ? $this->castBool($data['smoking'])
                                  : $room->smoking,
            'status'         => $data['status'] ?? $room->status,
            'price_override' => $data['price_override'] ?? null,
        ]);

        if (array_key_exists('amenities', $data)) {
            $room->amenities()->sync($data['amenities'] ?? []);
        }

        if ($request->hasFile('images')) {
            $currentCount = $room->images()->count();
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('rooms/' . $room->id, 'public');
                $room->images()->create([
                    'path'       => $path,
                    'is_primary' => $currentCount === 0 && $index === 0,
                    'order'      => $currentCount + $index,
                ]);
            }
        }

        return response()->json($room->load('roomType', 'images', 'amenities'));
    }

    public function destroy(Room $room)
    {
        if ($room->reservations()->whereIn('status', ['confirmed', 'checked_in'])->exists()) {
            return response()->json(['message' => 'Chambre avec des réservations actives'], 422);
        }
        $room->delete();
        return response()->json(['message' => 'Chambre supprimée']);
    }

    public function updateStatus(Request $request, Room $room)
    {
        $request->validate(['status' => 'required|in:available,occupied,maintenance,blocked']);
        $room->update(['status' => $request->status]);
        return response()->json($room);
    }

    // Cast smoking value — handles true/false/1/0/"true"/"false"
    private function castBool($value): bool
    {
        if (is_bool($value)) return $value;
        if (is_int($value))  return (bool) $value;
        if (is_string($value)) {
            return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
        }
        return false;
    }
    
}