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
        Schema::table('list_views', function (Blueprint $table) {
            // Aggiunge la colonna per l'ID dell'utente dopo la colonna 'id'.
            // constrained() crea la chiave esterna verso la tabella 'users'.
            // onDelete('cascade') elimina le viste di un utente se l'utente viene eliminato.
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_views', function (Blueprint $table) {
            // Rimuove prima la chiave esterna, poi la colonna.
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};