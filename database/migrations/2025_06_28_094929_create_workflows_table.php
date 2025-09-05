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
        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome del workflow per riconoscerlo
            $table->text('description')->nullable(); // Descrizione di cosa fa

            // --- CONDIZIONI DI ATTIVAZIONE (TRIGGER) ---
            $table->string('trigger_model'); // Su quale modello si attiva? Es. App\Models\Opportunity
            $table->string('trigger_condition_field'); // Quale campo controlliamo? Es. closing_date
            $table->string('trigger_condition_operator'); // Quale operatore usiamo? Es. <, >, =, ecc.
            $table->string('trigger_condition_value'); // Con quale valore confrontiamo? Es. '+7 days'

            // --- AZIONE DA ESEGUIRE ---
            $table->string('action_type'); // Che tipo di azione compiere? Es. 'create_activity'
            $table->json('action_parameters'); // Parametri per l'azione, in formato JSON.
                                               // Es. {"title": "Follow-up per opportunità", "type": "task"}

            $table->boolean('is_active')->default(true); // Per attivare/disattivare il workflow
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};
