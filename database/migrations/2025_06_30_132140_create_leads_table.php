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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_name')->nullable(); // Nome dell'azienda del lead
            $table->string('role')->nullable(); // Ruolo o posizione
            $table->string('source')->nullable(); // Fonte del lead (es. Fiera, Sito Web, Riferimento)
            
            // Stato del lead per tracciare il processo
            $table->string('status')->default('Nuovo'); // Es. Nuovo, Contattato, Qualificato, Non Qualificato, Convertito

            $table->text('notes')->nullable(); // Un campo per note veloci
            
            // Per il futuro: a quale utente è assegnato il lead
            // $table->foreignId('user_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
