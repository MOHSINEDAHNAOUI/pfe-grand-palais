<?php
namespace App\Services;
 
use App\Models\Reservation;
use App\Models\Room;
use App\Models\Promotion;
use App\Models\User;
use App\Models\Payment;
use App\Mail\ReservationConfirmationMail;
use App\Mail\ReservationPendingConfirmationMail;
use App\Mail\LoyaltyGiftMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
 
class ReservationService
{
    public function create(User $user, array $data): Reservation
    {
        $room   = Room::findOrFail($data['room_id']);
        $nights = Carbon::parse($data['check_in'])->diffInDays($data['check_out']);
 
        // Check availability
        $conflict = Reservation::where('room_id', $room->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<', $data['check_out'])
            ->where('check_out', '>', $data['check_in'])
            ->exists();
 
        if ($conflict) {
            abort(422, 'Cette chambre n\'est pas disponible pour ces dates');
        }
 
        $roomPrice      = ($room->price_override ?? $room->roomType->base_price) * $nights;
        $discountAmount = 0;
        $paymentMethod  = $data['payment_method'] ?? 'on_arrival';
 
        // Apply promo
        if (!empty($data['promo_code'])) {
            $promo = Promotion::where('code', $data['promo_code'])
                ->where('is_active', true)->first();
            if ($promo) {
                $discountAmount = $promo->type === 'percentage'
                    ? $roomPrice * ($promo->value / 100)
                    : $promo->value;
                $promo->increment('used_count');
            }
        }
 
        $totalPrice = max(0, $roomPrice - $discountAmount);
 
        // Generate QR code SVG
        $reference = 'HB-' . strtoupper(Str::random(8));
        $qrPath    = null;
 
        try {
            $qrPath = 'qrcodes/' . $reference . '.svg';
            Storage::disk('public')->makeDirectory('qrcodes');
            QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate(
                    json_encode([
                        'ref'      => $reference,
                        'room'     => $room->number,
                        'check_in' => $data['check_in'],
                        'guest'    => $user->name,
                    ]),
                    storage_path('app/public/' . $qrPath)
                );
        } catch (\Exception $e) {
            $qrPath = null;
        }
 
        // Generate confirmation token for on_arrival payment
        $confirmationToken = Str::random(64);
 
        // Status depends on payment method
        // Both start as pending until confirmed (either by email link or Stripe success)
        $status = 'pending';

        $reservation = Reservation::create([
            'reference'          => $reference,
            'user_id'            => $user->id,
            'room_id'            => $room->id,
            'promotion_id'       => isset($promo) ? $promo->id : null,
            'check_in'           => $data['check_in'],
            'check_out'          => $data['check_out'],
            'adults'             => $data['adults'],
            'children'           => $data['children'] ?? 0,
            'status'             => $status,
            'room_price'         => $roomPrice,
            'services_price'     => 0,
            'discount_amount'    => $discountAmount,
            'total_price'        => $totalPrice,
            'special_requests'   => $data['special_requests'] ?? null,
            'qr_code'            => $qrPath,
            'confirmation_token' => $confirmationToken,
            'payment_method'     => $paymentMethod,
            'confirmed_at'       => null,
        ]);
 
        // Attach services
        if (!empty($data['services'])) {
            foreach ($data['services'] as $serviceData) {
                $reservation->services()->attach($serviceData['id'], [
                    'quantity' => $serviceData['quantity'] ?? 1,
                    'price'    => $serviceData['price']    ?? 0,
                ]);
            }
        }
 
        // Send email based on payment method
        try {
            $loaded = $reservation->load(['room.roomType', 'user', 'services']);
 
            if ($paymentMethod === 'card') {
                // Paiement en ligne → On attend la confirmation du paiement via Stripe pour envoyer l'email
                // (Géré dans PaymentController@confirm)
            } else {
                // Paiement à l'arrivée → email pour confirmer la réservation
                Mail::to($user->email)->queue(new ReservationPendingConfirmationMail($loaded));
            }
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }

        // Create Payment Record (Pending until confirmed)
        Payment::create([
            'reservation_id' => $reservation->id,
            'user_id'        => $user->id,
            'amount'         => $totalPrice,
            'method'         => $paymentMethod,
            'status'         => 'pending',
            'transaction_id' => null,
            'paid_at'        => null,
        ]);

        return $reservation;
    }
 
    public function confirmArrival(Reservation $reservation): Reservation
    {
        $reservation->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);
 
        // Send confirmation email now
        try {
            Mail::to($reservation->user->email)
                ->queue(new ReservationConfirmationMail(
                    $reservation->load(['room.roomType', 'user', 'services'])
                ));
        } catch (\Exception $e) {
            \Log::error('Email sending failed: ' . $e->getMessage());
        }
 
        $this->checkLoyaltyBonus($reservation->user);
 
        return $reservation;
    }

    public function checkLoyaltyBonus(User $user)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();

        $count = Reservation::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'checked_in', 'completed', 'checked_out'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        if ($count === 3) {
            // Check if user already got a gift this month to avoid duplicates
            $alreadyGaveGift = Promotion::where('user_id', $user->id)
                ->where('name', 'LIKE', 'Cadeau de Fidélité %')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->exists();

            if (!$alreadyGaveGift) {
                $code = 'GIFT50-' . strtoupper(Str::random(6));
                
                Promotion::create([
                    'code'         => $code,
                    'name'         => 'Cadeau de Fidélité -50%',
                    'description'  => 'Félicitations pour vos 3 réservations ce mois-ci !',
                    'type'         => 'percentage',
                    'value'        => 50,
                    'max_uses'     => 1,
                    'is_active'    => true,
                    'user_id'      => $user->id,
                    'starts_at'    => now(),
                    'expires_at'   => now()->addMonths(3), // Valid for 3 months
                ]);

                try {
                    Mail::to($user->email)->queue(new LoyaltyGiftMail($user, $code));
                } catch (\Exception $e) {
                    \Log::error('Loyalty email failed: ' . $e->getMessage());
                }
            }
        }
    }
 
    public function cancel(Reservation $reservation, ?string $reason = null): Reservation
    {
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            abort(422, 'Cette réservation ne peut pas être annulée');
        }
 
        $reservation->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $reason,
        ]);
 
        return $reservation;
    }
}