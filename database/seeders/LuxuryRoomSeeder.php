<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\DB;

class LuxuryRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Nettoyage des anciennes données pour repartir sur une structure propre
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Room::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $floors = [
            'A' => range(1, 20),
            'B' => range(21, 40),
            'C' => range(41, 60),
            'D' => range(61, 80),
        ];

        $typeIds = RoomType::pluck('id')->toArray();

        foreach ($floors as $floorName => $roomNumbers) {
            foreach ($roomNumbers as $number) {
                // Attribution semi-aléatoire mais structurée des types
                // 1-10: Simple/Double, 11-15: Familiale, 16-20: Suites
                $localIndex = ($number - 1) % 20 + 1;
                
                if ($localIndex <= 8) {
                    $typeId = $typeIds[array_rand([$typeIds[0], $typeIds[1]])]; // Simple ou Double
                } elseif ($localIndex <= 14) {
                    $typeId = $typeIds[4] ?? $typeIds[0]; // Familiale
                } elseif ($localIndex <= 18) {
                    $typeId = $typeIds[2] ?? $typeIds[0]; // Junior Suite
                } else {
                    $typeId = $typeIds[3] ?? $typeIds[0]; // Presidential
                }

                Room::create([
                    'room_type_id' => $typeId,
                    'number'       => (string)$number,
                    'floor'        => $floorName,
                    'surface'      => rand(25, 85),
                    'view'         => collect(['Mer', 'Jardin', 'Ville', 'Piscine'])->random(),
                    'smoking'      => false,
                    'status'       => 'available',
                    'notes'        => 'Chambre de prestige, Étage ' . $floorName,
                ]);
            }
        }
    }
}
