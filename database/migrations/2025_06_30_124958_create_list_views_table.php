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
        Schema::create('list_views', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Es. "Vista Commerciale", "Vista Amministrativa"
            $table->string('module'); // A quale modulo si applica? Es. App\Models\Company

            // La colonna JSON che conterrà l'array delle colonne da visualizzare
            $table->json('columns');

            // Per il futuro: potremmo legare una vista a un utente specifico
            // $table->foreignId('user_id')->constrained('users');
            
            $table->boolean('is_default')->default(false); // C'è una sola vista di default per modulo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_views');
    }
};
