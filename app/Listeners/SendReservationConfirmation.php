<?php
namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Mail\ReservationConfirmationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendReservationConfirmation implements ShouldQueue
{
	public function handle(ReservationCreated $event): void
	{
		Mail::to($event->reservation->user->email)
			->send(new ReservationConfirmationMail($event->reservation));
	}
}
