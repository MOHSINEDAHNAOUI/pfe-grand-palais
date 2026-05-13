<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Notifications\CheckInReminderNotification;
use Carbon\Carbon;

class SendCheckInReminders extends Command
{
    protected $signature   = 'reservations:remind';
    protected $description = 'Envoyer les rappels de check-in pour demain';

    public function handle(): void
    {
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        $reservations = Reservation::where('check_in', $tomorrow)
            ->where('status', 'confirmed')
            ->with('user')
            ->get();

        $this->info("Envoi de {$reservations->count()} rappel(s)...");

        foreach ($reservations as $reservation) {
            $reservation->user->notify(new CheckInReminderNotification($reservation));
            $this->line("  ✓ Rappel envoyé à : {$reservation->user->email}");
        }

        $this->info('Rappels envoyés avec succès.');
    }
}