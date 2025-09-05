<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocationSeeder::class, // Carica Regioni, Province, Comuni
            ModuleSeeder::class,   // Carica la struttura dei form (blocchi e campi)
        ]);

        // Crea un utente di test
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}