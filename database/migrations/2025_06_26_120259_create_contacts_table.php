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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name'); // Nome
            $table->string('last_name');  // Cognome
            $table->string('email')->nullable()->unique(); // Email, può essere vuota ma se c'è dev'essere unica
            $table->string('phone')->nullable(); // Telefono
            $table->string('role')->nullable();  // Ruolo in azienda
            
            // --- La relazione con le aziende ---
            $table->foreignId('company_id') // Crea la colonna company_id
                  ->nullable()              // Può essere vuota (un contatto può non avere un'azienda)
                  ->constrained('companies') // Deve essere un id valido nella tabella 'companies'
                  ->onDelete('set null');  // Se l'azienda viene cancellata, il campo diventa NULL
            
            $table->timestamps(); // Colonne created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
