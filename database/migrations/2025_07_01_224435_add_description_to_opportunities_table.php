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
        Schema::table('opportunities', function (Blueprint $table) {
            // Aggiungiamo la colonna 'description' di tipo testo, che può essere vuota (nullable).
            // La posizioniamo dopo la colonna 'closing_date' per mantenere l'ordine logico.
            $table->text('description')->nullable()->after('closing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            // Se per qualsiasi motivo dovessi annullare questa migrazione,
            // questa funzione rimuoverà la colonna.
            $table->dropColumn('description');
        });
    }
};
