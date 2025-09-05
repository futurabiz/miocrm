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
        // Tabella pivot per la relazione Molti-a-Molti tra Contatti e Tag
        Schema::create('contact_tag', function (Blueprint $table) {
            // Chiave esterna per il contatto
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            // Chiave esterna per il tag
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');

            // Imposta la coppia di ID come chiave primaria per evitare duplicati
            $table->primary(['contact_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_tag');
    }
};
