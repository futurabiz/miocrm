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
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Es. "Polizza Assicurativa", "Servizio SumUp"
            $table->text('description')->nullable(); // Descrizione opzionale

            // Qui salviamo la struttura dei campi custom per questo tipo di servizio.
            // Esempio: [{"name": "scadenza", "label": "Data Scadenza", "type": "date"}, {"name": "broker", "label": "Broker", "type": "text"}]
            $table->json('fields_schema')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
