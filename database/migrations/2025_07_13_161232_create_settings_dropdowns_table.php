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
        Schema::create('settings_dropdowns', function (Blueprint $table) {
            $table->id();
            $table->string('module'); // Es. 'leads', 'contacts', etc.
            $table->string('name');   // Es. 'lead_stage', 'role', etc.
            
            // Usiamo JSON per memorizzare l'array di opzioni.
            // È più flessibile di un semplice campo di testo.
            $table->json('value');    
            
            $table->timestamps();

            // Aggiungiamo un indice per velocizzare le ricerche
            $table->index(['module', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_dropdowns');
    }
};