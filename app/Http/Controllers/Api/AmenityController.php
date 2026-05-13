<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;

class AmenityController extends Controller
{
    public function index()
    {
        return response()->json(Amenity::orderBy('category')->orderBy('name')->get());
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $this->middleware('role:admin');
        $data = $request->validate([
            'name'     => 'required|string|max:100|unique:amenities',
            'icon'     => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
        ]);
        $amenity = Amenity::create($data);
        return response()->json($amenity, 201);
    }

    public function destroy(Amenity $amenity)
    {
        $this->middleware('role:admin');
        $amenity->delete();
        return response()->json(['message' => 'Équipement supprimé']);
    }
}