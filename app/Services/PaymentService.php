<?php
namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentService
{
	public function __construct()
	{
		Stripe::setApiKey(config('services.stripe.secret'));
	}

	public function createPaymentIntent(Reservation $reservation): array
	{
		$intent = PaymentIntent::create([
			'amount' => (int) ($reservation->total_price * 100), // en centimes
			'currency' => 'eur',
			'metadata' => [
				'reservation_id' => $reservation->id,
				'reference' => $reservation->reference,
			],
		]);

		return [
			'client_secret' => $intent->client_secret,
			'payment_intent_id' => $intent->id,
		];
	}

	public function confirmPayment(Reservation $reservation, string $transactionId): Payment
	{
		$payment = Payment::create([
			'reservation_id' => $reservation->id,
			'user_id' => $reservation->user_id,
			'transaction_id' => $transactionId,
			'amount' => $reservation->total_price,
			'currency' => 'EUR',
			'method' => 'card',
			'status' => 'completed',
			'paid_at' => now(),
		]);

		$reservation->update(['status' => 'confirmed', 'confirmed_at' => now()]);

		return $payment;
	}

	public function refund(Payment $payment): Payment
	{
		\Stripe\Refund::create([
			'payment_intent' => $payment->transaction_id,
		]);

		$payment->update(['status' => 'refunded']);
		return $payment;
	}
}
