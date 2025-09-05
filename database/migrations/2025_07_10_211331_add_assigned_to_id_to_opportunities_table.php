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
            // Aggiunge la colonna 'assigned_to_id', la rende opzionale (nullable)
            // e crea una chiave esterna che punta all'ID degli utenti.
            // onDelete('set null') significa che se un utente viene eliminato,
            // le opportunità assegnate a quell'utente avranno il campo 'assigned_to_id' impostato a NULL.
            $table->foreignId('assigned_to_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null') // Puoi scegliere 'cascade' o 'restrict' se preferisci
                  ->after('company_id'); // Posizione logica
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('opportunities', function (Blueprint $table) {
            // Rimuove prima il vincolo di chiave esterna, poi la colonna stessa.
            $table->dropConstrainedForeignId('assigned_to_id');
        });
    }
};