<?php
namespace App\Listeners;

use App\Events\ReservationCancelled;
use App\Mail\ReservationCancelledMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCancellationNotification implements ShouldQueue
{
    public function handle(ReservationCancelled $event): void
    {
        Mail::to($event->reservation->user->email)
            ->send(new ReservationCancelledMail($event->reservation));
    }
}