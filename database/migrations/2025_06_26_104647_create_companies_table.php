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
        // Questo metodo crea la tabella 'companies' con le colonne specificate
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // Chiave primaria auto-incrementante (ID)
            $table->string('name'); // Il nome dell'azienda, obbligatorio
            $table->string('vat_number')->nullable(); // Partita IVA, può essere vuota
            $table->string('address')->nullable(); // Indirizzo
            $table->string('city')->nullable(); // Città
            $table->string('zip_code')->nullable(); // CAP
            $table->string('country')->nullable(); // Nazione
            $table->string('phone')->nullable(); // Telefono
            $table->string('email')->nullable(); // Email
            $table->string('website')->nullable(); // Sito web
            $table->timestamps(); // Aggiunge automaticamente le colonne created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};