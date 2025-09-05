<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Region;
use App\Models\Province;
use App\Models\City;
use App\Models\PostalCode;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $this->command->info('--- Avvio seeder DEFINITIVO (con CAP multipli) ---');
        $jsonPath = storage_path('app/comuni.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('ERRORE: File non trovato in storage/app/comuni.json!');
            return;
        }

        $this->command->info('1. Svuotamento tabelle...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        City::truncate();
        Province::truncate();
        Region::truncate();
        PostalCode::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('==> Tabelle pulite.');

        $comuni = json_decode(file_get_contents($jsonPath), true);
        $regionsCache = [];
        $provincesCache = [];

        $this->command->info('2. Inserimento dati...');
        DB::beginTransaction();
        try {
            foreach ($comuni as $comune) {
                $regionName = $comune['regione']['nome'];
                $provinceName = $comune['provincia']['nome'];
                $cityName = $comune['nome'];
                $caps = $comune['cap'];
                $fiscalCode = $comune['codiceCatastale'];

                if (!isset($regionsCache[$regionName])) {
                    $region = Region::firstOrCreate(['name' => $regionName]);
                    $regionsCache[$regionName] = $region->id;
                }
                $regionId = $regionsCache[$regionName];

                if (!isset($provincesCache[$provinceName])) {
                    $province = Province::firstOrCreate(['name' => $provinceName, 'region_id' => $regionId]);
                    $provincesCache[$provinceName] = $province->id;
                }
                $provinceId = $provincesCache[$provinceName];

                $city = City::create(['name' => $cityName, 'province_id' => $provinceId, 'fiscal_code' => $fiscalCode]);

                foreach ($caps as $cap) {
                    PostalCode::create(['city_id' => $city->id, 'code' => $cap]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('ERRORE CRITICO: ' . $e->getMessage());
            return;
        }
        $this->command->info('--- Seeding completato con successo! ---');
    }
}