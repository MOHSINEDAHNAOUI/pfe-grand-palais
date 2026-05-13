<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Rappels check-in (chaque jour à 9h)
        $schedule->command('reservations:remind')->dailyAt('09:00');

        // Demandes d'avis après départ (chaque jour à 11h)
        $schedule->command('reservations:review-requests')->dailyAt('11:00');

        // Annulation des réservations non payées (toutes les 6h)
        $schedule->command('reservations:auto-cancel --hours=24')->everySixHours();

        // Génération des prix dynamiques (chaque semaine)
        $schedule->command('prices:generate --days=60')->weekly()->sundays()->at('02:00');

        // Rapport mensuel (1er de chaque mois à minuit)
        $schedule->command('report:generate')->monthlyOn(1, '00:00');

        // Remerciements post-séjour (chaque jour à 10h)
        $schedule->command('emails:send-post-stay')->dailyAt('10:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}