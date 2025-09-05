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
        Schema::create('module_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('module_class'); // Il nome completo della classe del modello (es. App\Models\Lead)
            $table->string('name'); // Nome del blocco (es. "Informazioni Principali")
            $table->unsignedInteger('order')->default(0); // Ordine del blocco
            $table->timestamps();

            // Indice unique composito se 'name' del blocco è unico per una data 'module_class'
            $table->unique(['module_class', 'name']); 
        });

        Schema::create('module_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_block_id')->constrained('module_blocks')->onDelete('cascade');
            $table->string('name'); // Nome del campo nel database (es. 'first_name', 'custom_field_1')
            $table->string('label'); // Etichetta visualizzata nel form (es. 'Nome', 'Campo Personalizzato 1')
            $table->string('type'); // Tipo di input del form (es. 'text', 'textarea', 'select', 'date', etc.)
            $table->boolean('is_standard')->default(false); // Indica se è un campo standard del CRM o personalizzato
            $table->boolean('is_required')->default(false); // Indica se il campo è obbligatorio
            $table->boolean('is_visible')->default(true); // Indica se il campo è visibile nel form
            $table->unsignedInteger('order')->default(0); // Ordine del campo all'interno del blocco
            $table->json('options')->nullable(); // Per campi di tipo 'select', 'radio', 'checkbox'
            $table->timestamps();

            // Indice composito per migliorare le performance delle query e garantire l'unicità
            $table->unique(['module_block_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_fields');
        Schema::dropIfExists('module_blocks');
    }
};