<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            [
                'code'        => 'WELCOME10',
                'name'        => 'Bienvenue -10%',
                'description' => '10% de réduction pour votre première réservation',
                'type'        => 'percentage',
                'value'       => 10,
                'max_uses'    => 100,
                'is_active'   => true,
            ],
            [
                'code'        => 'SUMMER25',
                'name'        => 'Été -25%',
                'description' => '25% de réduction sur les séjours estivaux',
                'type'        => 'percentage',
                'value'       => 25,
                'starts_at'   => Carbon::create(2025, 6, 1),
                'expires_at'  => Carbon::create(2025, 9, 30),
                'min_amount'  => 200,
                'is_active'   => true,
            ],
            [
                'code'        => 'FLAT50',
                'name'        => 'Remise fixe 50€',
                'description' => '50€ de réduction dès 300€ de réservation',
                'type'        => 'fixed',
                'value'       => 50,
                'min_amount'  => 300,
                'max_uses'    => 50,
                'is_active'   => true,
            ],
            [
                'code'        => 'WEEKEND15',
                'name'        => 'Week-end -15%',
                'description' => '15% de réduction sur les séjours du week-end',
                'type'        => 'percentage',
                'value'       => 15,
                'is_active'   => true,
            ],
        ];

        foreach ($promotions as $promo) {
            Promotion::firstOrCreate(['code' => $promo['code']], $promo);
        }
    }
}