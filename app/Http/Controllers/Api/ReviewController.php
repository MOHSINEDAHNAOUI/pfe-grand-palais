<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Review;
use Illuminate\Http\Request;
 
class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'room.roomType'])
            ->approved()
            ->latest()
            ->take(10)
            ->get();
            
        return response()->json($reviews);
    }

    public function store(Reservation $reservation, Request $request)
    {
        // On ne vérifie pas 'view' ici car c'est une API publique de soumission, 
        // mais on vérifie que la réservation appartient à l'utilisateur
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
 
        if ($reservation->status !== 'completed' && $reservation->status !== 'checked_out') {
            return response()->json(['message' => 'Vous ne pouvez laisser un avis qu\'après votre séjour'], 422);
        }

        // Vérifier si un avis existe déjà
        if (Review::where('reservation_id', $reservation->id)->exists()) {
            return response()->json(['message' => 'Vous avez déjà laissé un avis pour ce séjour'], 422);
        }
 
        $data = $request->validate([
            'rating'              => 'required|integer|min:1|max:5',
            'cleanliness_rating'  => 'nullable|integer|min:1|max:5',
            'comfort_rating'      => 'nullable|integer|min:1|max:5',
            'service_rating'      => 'nullable|integer|min:1|max:5',
            'comment'             => 'nullable|string|max:2000',
        ]);
 
        $review = Review::create([
            ...$data,
            'user_id'        => auth()->id(),
            'room_id'        => $reservation->room_id,
            'reservation_id' => $reservation->id,
            'is_approved'    => true, // On approuve automatiquement pour le test, ou on laisse false si besoin de modération
        ]);
 
        return response()->json($review, 201);
    }

    public function storeGeneral(Request $request)
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        $review = Review::create([
            ...$data,
            'user_id'     => auth()->id(),
            'is_approved' => true, // Approbation automatique
        ]);

        return response()->json($review, 201);
    }

    public function userReviews()
    {
        $reviews = Review::with(['room.roomType'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
            
        return response()->json($reviews);
    }
}