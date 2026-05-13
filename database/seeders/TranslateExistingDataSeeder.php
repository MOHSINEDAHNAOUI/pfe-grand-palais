<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Service;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;

class TranslateExistingDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Reverts JSON translations to plain text (French version).
     */
    public function run(): void
    {
        // 1. Types de Chambres
        foreach (RoomType::all() as $item) {
            $this->revertItem($item, ['name', 'description']);
        }

        // 2. Services
        foreach (Service::all() as $item) {
            $this->revertItem($item, ['name', 'description']);
        }

        // 3. Promotions
        foreach (Promotion::all() as $item) {
            $this->revertItem($item, ['name', 'description']);
        }

        $this->command->info('Base de données nettoyée : toutes les traductions JSON ont été converties en texte simple.');
    }

    /**
     * Reverts specified fields from JSON to simple strings.
     */
    private function revertItem($item, array $fields)
    {
        $updates = [];
        foreach ($fields as $field) {
            $value = $item->getRawOriginal($field) ?? $item->$field;
            
            if ($this->isJson($value)) {
                $decoded = json_decode($value, true);
                // On privilégie 'fr', sinon on prend la première clé dispo, sinon la valeur brute
                $updates[$field] = $decoded['fr'] ?? (reset($decoded) ?: $value);
            }
        }

        if (!empty($updates)) {
            // On utilise DB::table pour éviter que le trait HasTranslations (s'il est encore là) ne bloque l'update
            DB::table($item->getTable())->where('id', $item->id)->update($updates);
        }
    }

    private function isJson($string) {
        if (!is_string($string) || empty($string)) return false;
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
