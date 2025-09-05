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
        Schema::create('customer_services', function (Blueprint $table) {
            $table->id();

            // A quale "tipo" di servizio fa riferimento?
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');

            // --- Relazione Polimorfica con il Cliente ---
            // A chi è stato venduto il servizio? (Azienda o Contatto)
            $table->morphs('customerable'); // Crea customerable_id e customerable_type

            // Qui salviamo i valori dei campi custom per questo specifico servizio venduto.
            $table->json('custom_fields_data')->nullable();

            $table->string('status')->default('active'); // Es. 'active', 'expired', 'canceled'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_services');
    }
};
