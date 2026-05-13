<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Promotion;
use App\Services\ReservationService;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class ReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService)
    {
    }

    public function index(Request $request)
    {
        $reservations = Reservation::with(['room.roomType', 'room.images', 'payment'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'required|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'promo_code' => 'nullable|string',
            'services' => 'nullable|array',
            'payment_method' => 'required|string|in:card,on_arrival',
        ]);

        $reservation = $this->reservationService->create(auth()->user(), $data);

        // Notifier les administrateurs
        $admins = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['admin', 'manager']);
        })->get();
        
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewReservationNotification($reservation));

        return response()->json($reservation->load(['room.roomType', 'payment']), 201);
    }

    public function show(Reservation $reservation)
    {
        // Vérifier que l'utilisateur peut voir cette réservation
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        return response()->json(
            $reservation->load(['room.roomType', 'room.images', 'services', 'payment', 'user'])
        );
    }

    public function cancel(Reservation $reservation, Request $request)
    {
        if ($reservation->user_id !== auth()->id()) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }
        $reservation = $this->reservationService->cancel($reservation, $request->reason);
        return response()->json($reservation);
    }

    public function checkPromo(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $promo = Promotion::where('code', $request->code)
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->where(fn($q) => $q->whereNull('user_id')->orWhere('user_id', auth()->id()))
            ->first();

        if (!$promo)
            return response()->json(['message' => 'Code promo invalide ou expiré'], 404);
        return response()->json($promo);
    }

    public function confirmArrival(Reservation $reservation, string $token)
    {
        if ($reservation->confirmation_token !== $token) {
            return response()->json(['message' => 'invalid'], 400);
        }

        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'already', 'reference' => $reservation->reference]);
        }

        $this->reservationService->confirmArrival($reservation);

        return response()->json([
            'status' => 'confirmed',
            'reference' => $reservation->fresh()->reference,
        ]);
    }

    public function cancelArrival(Reservation $reservation, string $token)
    {
        if ($reservation->confirmation_token !== $token) {
            return response()->json(['message' => 'invalid'], 400);
        }

        $this->reservationService->cancel($reservation, 'Annulé par le client via email');

        return response()->json(['status' => 'cancelled']);
    }

    public function showByReference(string $reference)
    {
        $reservation = Reservation::with(['room.roomType', 'room.images', 'services', 'payment', 'user'])
            ->where('reference', $reference)
            ->firstOrFail();

        return response()->json($reservation);
    }

    public function downloadReceipt(Reservation $reservation)
    {
        $reservation->load(['room.roomType', 'user', 'payment', 'services']);

        $pdf = Pdf::loadView('pdf.receipt', compact('reservation'));
        $filename = 'Recu_GRANDPALAIS_' . $reservation->reference . '.pdf';

        return $pdf->download($filename);
    }

    public function pendingReviews()
    {
        $reservations = Reservation::with(['room.roomType'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['checked_out', 'completed'])
            ->whereDoesntHave('review')
            ->latest()
            ->get();
            
        return response()->json($reservations);
    }
}


