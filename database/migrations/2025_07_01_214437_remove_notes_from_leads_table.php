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
            // Controlliamo se la colonna esiste prima di tentare di rimuoverla.
            // Questo rende la migrazione sicura anche se eseguita più volte.
            if (Schema::hasColumn('leads', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Se per qualsiasi motivo dovessi annullare questa migrazione,
            // questa funzione ricreerà la colonna.
            $table->text('notes')->nullable();
        });
    }
};
