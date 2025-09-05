<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Aggiungi la colonna 'city_code' dopo 'birthdate' per coerenza anagrafica
            $table->string('city_code', 10)->nullable()->after('birthdate'); // Il codice Belfiore è solitamente 4-5 caratteri, 10 è sicuro
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('city_code');
        });
    }
};