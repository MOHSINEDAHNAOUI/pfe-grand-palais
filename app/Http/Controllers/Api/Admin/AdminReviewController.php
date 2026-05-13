<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin|manager']);
    }

    public function index(Request $request)
    {
        $reviews = Review::with(['user', 'room.roomType', 'reservation'])
            ->when($request->approved !== null, fn($q) =>
                $q->where('is_approved', $request->boolean('approved'))
            )
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($reviews);
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return response()->json($review);
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['message' => 'Avis supprimé']);
    }
}