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
        // Questa è la tabella "ponte" (pivot) che collega Opportunità e Tipi di Servizio
        Schema::create('opportunity_service_type', function (Blueprint $table) {
            // Chiave esterna per l'opportunità
            $table->foreignId('opportunity_id')->constrained()->onDelete('cascade');
            
            // Chiave esterna per il tipo di servizio
            $table->foreignId('service_type_id')->constrained()->onDelete('cascade');

            // Campi aggiuntivi per la relazione
            $table->integer('quantity')->default(1); // Quantità del servizio per questa specifica opportunità
            $table->decimal('price', 10, 2); // Prezzo del servizio al momento dell'aggiunta (potrebbe essere diverso dal prezzo di listino)
            $table->decimal('discount', 5, 2)->default(0.00); // Sconto percentuale applicato

            // Imposta la coppia di ID come chiave primaria per evitare duplicati
            $table->primary(['opportunity_id', 'service_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunity_service_type');
    }
};
