<?php
namespace App\Http\Controllers\Api\Admin;
 
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
 
class AdminReservationController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}
 
    public function index(Request $request)
    {
        $reservations = Reservation::with(['user', 'room.roomType', 'payment'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->where('reference', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q2) =>
                      $q2->where('name', 'like', "%{$request->search}%")
                         ->orWhere('email', 'like', "%{$request->search}%")
                  );
            })
            ->when($request->date, fn($q) =>
                $q->whereDate('check_in', $request->date)
                  ->orWhereDate('check_out', $request->date)
            )
            ->orderByDesc('created_at')
            ->paginate(10);
 
        return response()->json($reservations);
    }
 
    public function confirm(Reservation $reservation)
    {
        $this->checkPermission('confirm_reservation');
        $reservation->update(['status' => 'confirmed', 'confirmed_at' => now()]);
        
        // Trigger loyalty check
        $this->reservationService->checkLoyaltyBonus($reservation->user);
        
        return response()->json($reservation->load(['user', 'room.roomType']));
    }
 
    public function checkIn(Reservation $reservation)
    {
        $this->checkPermission('checkin_reservation');
        $reservation->update(['status' => 'checked_in', 'checked_in_at' => now()]);
        
        // Trigger loyalty check in case it wasn't confirmed normally before
        if ($reservation->user) {
            $this->reservationService->checkLoyaltyBonus($reservation->user);
        }

        return response()->json($reservation);
    }
 
    public function checkOut(Reservation $reservation)
    {
        $this->checkPermission('checkout_reservation');
        $reservation->update(['status' => 'checked_out', 'checked_out_at' => now()]);
        return response()->json($reservation);
    }
 
    public function noShow(Reservation $reservation)
    {
        $this->checkPermission('manage_reservations');
        $reservation->update(['status' => 'no_show']);
        return response()->json($reservation);
    }
 
    private function checkPermission(string $permission): void
    {
        $user = auth()->user();
        if (!$user->can($permission) && !$user->can('manage_reservations') && !$user->hasRole('admin')) {
            abort(403, 'Permission refusée');
        }
    }
}