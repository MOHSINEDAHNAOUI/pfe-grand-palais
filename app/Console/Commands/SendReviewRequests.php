<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Notifications\ReviewRequestNotification;
use Carbon\Carbon;

class SendReviewRequests extends Command
{
    protected $signature   = 'reservations:review-requests';
    protected $description = 'Envoyer les demandes d\'avis après check-out';

    public function handle(): void
    {
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        $reservations = Reservation::where('check_out', $yesterday)
            ->where('status', 'checked_out')
            ->whereDoesntHave('review')
            ->with('user')
            ->get();

        $this->info("Envoi de {$reservations->count()} demande(s) d'avis...");

        foreach ($reservations as $reservation) {
            $reservation->user->notify(new ReviewRequestNotification($reservation));
            $this->line("  ✓ Demande envoyée à : {$reservation->user->email}");
        }

        $this->info('Demandes envoyées avec succès.');
    }
}