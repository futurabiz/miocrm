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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->text('content'); // Il contenuto della nota

            // --- Le colonne polimorfiche ---
            $table->unsignedBigInteger('notable_id'); // L'ID del record a cui la nota è collegata (es. ID azienda)
            $table->string('notable_type'); // Il tipo di record (es. 'App\Models\Company')
            // Insieme, queste due colonne creano la relazione "attaccabile"

            // Per il futuro, l'utente che ha creato la nota
            // $table->foreignId('user_id')->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
