<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class AdminPromotionController extends Controller
{
    public function index()
    {
        return response()->json(Promotion::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'         => 'required|string|unique:promotions,code',
            'name'         => 'required|string',
            'description'  => 'nullable|string',
            'company_name' => 'nullable|string',
            'logo'         => 'nullable|image|max:2048',
            'type'         => 'required|in:percentage,fixed',
            'value'        => 'required|numeric|min:0',
            'min_amount'   => 'nullable|numeric|min:0',
            'max_uses'     => 'nullable|integer|min:1',
            'starts_at'    => 'nullable|date',
            'expires_at'   => 'nullable|date|after_or_equal:starts_at',
            'is_active'    => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('promotions', 'public');
        }

        $promotion = Promotion::create($data);
        return response()->json($promotion, 201);
    }

    public function update(Request $request, Promotion $promotion)
    {
        $data = $request->validate([
            'code'         => 'required|string|unique:promotions,code,' . $promotion->id,
            'name'         => 'required|string',
            'description'  => 'nullable|string',
            'company_name' => 'nullable|string',
            'logo'         => 'nullable|image|max:2048',
            'type'         => 'required|in:percentage,fixed',
            'value'        => 'required|numeric|min:0',
            'min_amount'   => 'nullable|numeric|min:0',
            'max_uses'     => 'nullable|integer|min:1',
            'starts_at'    => 'nullable|date',
            'expires_at'   => 'nullable|date|after_or_equal:starts_at',
            'is_active'    => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('promotions', 'public');
        }

        $promotion->update($data);
        return response()->json($promotion);
    }

    public function destroy(Promotion $promotion)
    {
        if ($promotion->used_count > 0) {
            return response()->json(['message' => 'Impossible de supprimer une promotion déjà utilisée'], 422);
        }
        $promotion->delete();
        return response()->json(['message' => 'Promotion supprimée']);
    }

    public function toggle(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);
        return response()->json($promotion);
    }
}
