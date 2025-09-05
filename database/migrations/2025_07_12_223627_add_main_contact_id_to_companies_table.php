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
        Schema::table('companies', function (Blueprint $table) {
            // Aggiungi la colonna main_contact_id
            $table->unsignedBigInteger('main_contact_id')->nullable()->after('assigned_to_id'); // Puoi scegliere la colonna 'after' appropriata

            // Aggiungi la foreign key. Se elimini un contatto, il campo main_contact_id nell'azienda diventa NULL.
            $table->foreign('main_contact_id')->references('id')->on('contacts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Rimuovi la foreign key prima di rimuovere la colonna
            $table->dropForeign(['main_contact_id']);
            // Rimuovi la colonna
            $table->dropColumn('main_contact_id');
        });
    }
};