<?php
// database/seeders/RoomTypeSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Chambre Simple', 'slug' => 'simple', 'base_capacity' => 1, 'max_capacity' => 1, 'base_price' => 80, 'description' => 'Chambre confortable pour 1 personne'],
            ['name' => 'Chambre Double', 'slug' => 'double', 'base_capacity' => 2, 'max_capacity' => 2, 'base_price' => 120, 'description' => 'Chambre spacieuse pour 2 personnes'],
            ['name' => 'Suite Junior', 'slug' => 'suite-junior', 'base_capacity' => 2, 'max_capacity' => 3, 'base_price' => 200, 'description' => 'Suite luxueuse avec salon séparé'],
            ['name' => 'Suite Présidentielle', 'slug' => 'suite-presidentielle', 'base_capacity' => 2, 'max_capacity' => 4, 'base_price' => 450, 'description' => 'La suite la plus luxueuse de l\'hôtel'],
            ['name' => 'Chambre Familiale', 'slug' => 'familiale', 'base_capacity' => 4, 'max_capacity' => 6, 'base_price' => 180, 'description' => 'Idéale pour les familles'],
        ];

        foreach ($types as $type) {
            RoomType::firstOrCreate(['slug' => $type['slug']], $type);
        }
    }
}