<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use Carbon\Carbon;

class AutoCancelPendingReservations extends Command
{
    protected $signature   = 'reservations:auto-cancel {--hours=24}';
    protected $description = 'Annuler automatiquement les réservations en attente non payées';

    public function handle(): void
    {
        $hours   = $this->option('hours');
        $cutoff  = Carbon::now()->subHours($hours);

        $reservations = Reservation::where('status', 'pending')
            ->where('created_at', '<=', $cutoff)
            ->whereDoesntHave('payment', fn($q) => $q->where('status', 'completed'))
            ->get();

        $this->info("Annulation de {$reservations->count()} réservation(s) en attente...");

        foreach ($reservations as $reservation) {
            $reservation->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => 'Annulation automatique - paiement non reçu',
            ]);
            $this->line("  ✓ Annulée : {$reservation->reference}");
        }

        $this->info('Annulations automatiques terminées.');
    }
}