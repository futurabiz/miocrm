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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome della trattativa
            $table->string('stage')->default('Qualificazione'); // Fase (es. Qualificazione, Proposta, Negoziazione)
            $table->decimal('amount', 10, 2)->nullable(); // Valore economico
            $table->date('closing_date')->nullable(); // Data di chiusura prevista

            // Relazione con i contatti e le aziende
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            
            // Per il futuro: a quale utente del CRM è assegnata l'opportunità
            // $table->foreignId('user_id')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
