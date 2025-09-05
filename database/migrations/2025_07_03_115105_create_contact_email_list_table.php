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
        // Tabella pivot per la relazione Molti-a-Molti tra Contatti e Liste Email
        Schema::create('contact_email_list', function (Blueprint $table) {
            // Chiave esterna per il contatto
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            // Chiave esterna per la lista email
            $table->foreignId('email_list_id')->constrained()->onDelete('cascade');

            // Imposta la coppia di ID come chiave primaria per evitare duplicati
            $table->primary(['contact_id', 'email_list_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_email_list');
    }
};
