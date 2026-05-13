<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $types = RoomType::all()->keyBy('slug');
        $amenities = Amenity::all();

        $rooms = [
            // Simples — étage 1
            ['number' => '101', 'floor' => 1, 'surface' => 20, 'view' => 'cour',   'slug' => 'simple'],
            ['number' => '102', 'floor' => 1, 'surface' => 22, 'view' => 'jardin', 'slug' => 'simple'],
            ['number' => '103', 'floor' => 1, 'surface' => 20, 'view' => 'cour',   'slug' => 'simple'],

            // Doubles — étage 2
            ['number' => '201', 'floor' => 2, 'surface' => 30, 'view' => 'jardin', 'slug' => 'double'],
            ['number' => '202', 'floor' => 2, 'surface' => 32, 'view' => 'ville',  'slug' => 'double'],
            ['number' => '203', 'floor' => 2, 'surface' => 30, 'view' => 'jardin', 'slug' => 'double'],
            ['number' => '204', 'floor' => 2, 'surface' => 35, 'view' => 'mer',    'slug' => 'double'],

            // Suites Junior — étage 3
            ['number' => '301', 'floor' => 3, 'surface' => 50, 'view' => 'mer',    'slug' => 'suite-junior'],
            ['number' => '302', 'floor' => 3, 'surface' => 55, 'view' => 'ville',  'slug' => 'suite-junior'],

            // Familiale — étage 3
            ['number' => '303', 'floor' => 3, 'surface' => 65, 'view' => 'jardin', 'slug' => 'familiale'],

            // Suite Présidentielle — étage 4
            ['number' => '401', 'floor' => 4, 'surface' => 120, 'view' => 'mer',  'slug' => 'suite-presidentielle'],
        ];

        foreach ($rooms as $roomData) {
            $slug = $roomData['slug'];
            unset($roomData['slug']);

            $roomType = $types[$slug] ?? null;
            if (!$roomType) continue;

            $room = Room::firstOrCreate(
                ['number' => $roomData['number']],
                array_merge($roomData, ['room_type_id' => $roomType->id])
            );

            // Attacher des équipements aléatoires
            $room->amenities()->sync(
                $amenities->random(rand(4, 8))->pluck('id')->toArray()
            );
        }
    }
}