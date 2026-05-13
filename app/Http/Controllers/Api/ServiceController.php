<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // Public — retourne les services actifs groupés par catégorie
    public function index(Request $request)
    {
        $services = Service::where('is_active', true)
            ->when($request->category, fn($q) => $q->where('category', $request->category))
            ->get();

        return response()->json($services);
    }

    // Admin — retourne tous les services (actifs + inactifs)
    public function adminIndex(Request $request)
    {
        $services = Service::when($request->category, fn($q) => $q->where('category', $request->category))
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return response()->json($services);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'price_type'  => 'required|in:per_night,per_person,per_stay,one_time',
            'category'    => 'required|string',
            'icon'        => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $data['is_active'] = $data['is_active'] ?? true;

        $service = Service::create($data);
        return response()->json($service, 201);
    }

    public function show(Service $service)
    {
        return response()->json($service);
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'price_type'  => 'sometimes|in:per_night,per_person,per_stay,one_time',
            'category'    => 'sometimes|string',
            'icon'        => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);
        return response()->json($service);
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return response()->json(['message' => 'Service supprimé']);
    }

    public function toggle(Service $service)
    {
        $service->update(['is_active' => !$service->is_active]);
        return response()->json($service);
    }
}