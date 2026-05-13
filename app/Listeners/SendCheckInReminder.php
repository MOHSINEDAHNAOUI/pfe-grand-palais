<?php
namespace App\Listeners;

use App\Events\ReservationCreated;
use App\Notifications\CheckInReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCheckInReminder implements ShouldQueue
{
    public $delay; // sera défini dynamiquement

    public function handle(ReservationCreated $event): void
    {
        $reservation = $event->reservation;
        $reminderDate = $reservation->check_in->subDay(); // J-1

        $reservation->user->notify(
            (new CheckInReminderNotification($reservation))
                ->delay($reminderDate)
        );
    }
}