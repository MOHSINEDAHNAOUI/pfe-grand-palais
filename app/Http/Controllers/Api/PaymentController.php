<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
 
use App\Services\ReservationService;
 
class PaymentController extends Controller
{
    public function __construct(private ReservationService $reservationService)
    {
    }

    public function createIntent(Reservation $reservation)
    {
        // $this->authorize('view', $reservation); // Optional: if policy is set

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => (int)($reservation->total_price * 100),
            'currency' => 'mad',
            'metadata' => [
                'reservation_id' => $reservation->id,
                'user_email' => auth()->user()->email
            ],
        ]);
 
        return response()->json([
            'client_secret'     => $intent->client_secret,
            'payment_intent_id' => $intent->id,
        ]);
    }
 
    public function confirm(Reservation $reservation, Request $request)
    {
        // $this->authorize('view', $reservation);

        $payment = $reservation->payment;
        if (!$payment) {
             return response()->json(['message' => 'Payment record not found'], 404);
        }

        $payment->update([
            'transaction_id' => $request->transaction_id,
            'status'         => 'completed',
            'paid_at'        => now(),
        ]);

        $reservation->update([
            'status' => 'confirmed', 
            'confirmed_at' => now()
        ]);

        // Send confirmation email
        try {
            \Illuminate\Support\Facades\Mail::to($reservation->user->email)
                ->queue(new \App\Mail\ReservationConfirmationMail(
                    $reservation->load(['room.roomType', 'user', 'services'])
                ));
        } catch (\Exception $e) {
            \Log::error('Email confirmation after Stripe failed: ' . $e->getMessage());
        }

        $this->reservationService->checkLoyaltyBonus($reservation->user);

        return response()->json(['payment' => $payment, 'reservation' => $reservation]);
    }
}