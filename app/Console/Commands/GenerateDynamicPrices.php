<?php
// app/Console/Commands/GenerateDynamicPrices.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RoomType;
use App\Services\DynamicPricingService;
use Carbon\Carbon;

class GenerateDynamicPrices extends Command
{
    protected $signature = 'prices:generate {--days=30}';
    protected $description = 'Générer les prix dynamiques pour les prochains jours';

    public function handle(DynamicPricingService $pricingService): void
    {
        $days = $this->option('days');
        $start = Carbon::today()->format('Y-m-d');
        $end = Carbon::today()->addDays($days)->format('Y-m-d');

        $types = RoomType::where('is_active', true)->get();

        $this->withProgressBar($types, function ($type) use ($pricingService, $start, $end) {
            $pricingService->generatePricesForPeriod($type, $start, $end);
        });

        $this->newLine();
        $this->info("Prix générés pour {$types->count()} types de chambres sur {$days} jours.");
    }
}