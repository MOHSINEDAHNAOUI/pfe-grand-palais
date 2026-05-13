<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // Basiques
            ['name' => 'Petit-déjeuner Continental', 'description' => 'Café, jus, viennoiseries, fruits', 'price' => 15,  'price_type' => 'per_person', 'category' => 'basic',   'icon' => '☕'],
            ['name' => 'Petit-déjeuner Buffet',      'description' => 'Buffet complet varié',              'price' => 25,  'price_type' => 'per_person', 'category' => 'basic',   'icon' => '🍳'],
            ['name' => 'Parking',                    'description' => 'Place de parking sécurisée',        'price' => 12,  'price_type' => 'per_night',  'category' => 'basic',   'icon' => '🚗'],
            ['name' => 'Transfert Aéroport',         'description' => 'Navette aller/retour',              'price' => 45,  'price_type' => 'one_time',   'category' => 'basic',   'icon' => '✈️'],
            ['name' => 'Service en Chambre',         'description' => 'Repas livré en chambre 24h/24',     'price' => 10,  'price_type' => 'one_time',   'category' => 'basic',   'icon' => '🛎️'],

            // Premium
            ['name' => 'Spa & Wellness',             'description' => 'Accès spa, hammam, jacuzzi',        'price' => 50,  'price_type' => 'per_person', 'category' => 'premium', 'icon' => '💆'],
            ['name' => 'Salle de Sport',             'description' => 'Accès illimité salle de fitness',   'price' => 20,  'price_type' => 'per_night',  'category' => 'premium', 'icon' => '💪'],
            ['name' => 'Blanchisserie Express',      'description' => 'Retour sous 24h',                   'price' => 30,  'price_type' => 'one_time',   'category' => 'premium', 'icon' => '👔'],
            ['name' => 'Location Véhicule',          'description' => 'Voiture de luxe avec chauffeur',    'price' => 150, 'price_type' => 'per_night',  'category' => 'premium', 'icon' => '🚙'],
            ['name' => 'Dîner Gastronomique',        'description' => 'Menu 3 plats au restaurant',        'price' => 80,  'price_type' => 'per_person', 'category' => 'premium', 'icon' => '🍽️'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], $service);
        }
    }
}