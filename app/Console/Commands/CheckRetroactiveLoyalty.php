<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckRetroactiveLoyalty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loyalty:retroactive';

    protected $description = 'Vérifie tous les utilisateurs et distribue les cadeaux de fidélité manquants pour ce mois.';

    public function handle(\App\Services\ReservationService $service)
    {
        $this->info('Vérification rétroactive des fidélités en cours...');
        $users = \App\Models\User::all();
        $count = 0;

        foreach ($users as $user) {
            // Count gifts before
            $giftsBefore = \App\Models\Promotion::where('user_id', $user->id)->count();
            
            $service->checkLoyaltyBonus($user);
            
            $giftsAfter = \App\Models\Promotion::where('user_id', $user->id)->count();
            if ($giftsAfter > $giftsBefore) {
                $this->line("Cadeau envoyé à: {$user->email}");
                $count++;
            }
        }

        $this->info("Terminé. {$count} cadeaux ont été distribués rétroactivement.");
    }
}
