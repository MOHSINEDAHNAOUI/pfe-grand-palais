<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            // Technologie
            ['name' => 'WiFi Gratuit',        'icon' => 'wifi',          'category' => 'technology'],
            ['name' => 'Télévision 4K',        'icon' => 'tv',            'category' => 'technology'],
            ['name' => 'Coffre-fort',          'icon' => 'lock-closed',   'category' => 'technology'],
            ['name' => 'Téléphone',            'icon' => 'phone',         'category' => 'technology'],

            // Confort
            ['name' => 'Climatisation',        'icon' => 'cloud',         'category' => 'comfort'],
            ['name' => 'Mini-Bar',             'icon' => 'cube',          'category' => 'comfort'],
            ['name' => 'Bureau de travail',    'icon' => 'briefcase',     'category' => 'comfort'],
            ['name' => 'Salon privé',          'icon' => 'home',          'category' => 'comfort'],
            ['name' => 'Terrasse',             'icon' => 'sun',           'category' => 'comfort'],

            // Salle de bain
            ['name' => 'Baignoire',            'icon' => 'beaker',        'category' => 'bathroom'],
            ['name' => 'Douche à l\'italienne','icon' => 'beaker',        'category' => 'bathroom'],
            ['name' => 'Produits de toilette', 'icon' => 'sparkles',      'category' => 'bathroom'],
            ['name' => 'Peignoir & Chaussons', 'icon' => 'star',          'category' => 'bathroom'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::firstOrCreate(['name' => $amenity['name']], $amenity);
        }
    }
}