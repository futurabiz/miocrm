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
            // Aggiungi la colonna 'birthdate' dopo 'gender' o 'codice_fiscale'
            // Ho messo dopo 'gender' per coerenza con i dati anagrafici
            $table->date('birthdate')->nullable()->after('gender'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('birthdate');
        });
    }
};