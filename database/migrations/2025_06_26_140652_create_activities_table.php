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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Titolo dell'attività (es. "Chiamata di follow-up")
            $table->text('description')->nullable(); // Descrizione dettagliata
            
            // Tipo di attività, per poterle filtrare o colorare diversamente
            $table->enum('type', ['task', 'meeting', 'call'])->default('task');
            
            // Date e ore per il calendario
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();

            // Stato dell'attività
            $table->enum('status', ['pending', 'completed', 'canceled'])->default('pending');

            // --- Relazione Polimorfica ---
            // A cosa è collegata questa attività?
            $table->unsignedBigInteger('activityable_id')->nullable();
            $table->string('activityable_type')->nullable();
            
            // A quale utente è assegnata l'attività (per il futuro)
            // $table->foreignId('user_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
