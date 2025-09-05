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
        Schema::table('leads', function (Blueprint $table) {
            // Aggiungiamo i campi dopo 'last_name' per raggruppare i dati personali
            $table->string('salutation')->nullable()->after('last_name')->comment('Es. Sig., Sig.ra');
            $table->string('codice_fiscale')->nullable()->after('salutation');
            $table->string('mobile_phone')->nullable()->after('phone');

            // Aggiungiamo i campi dopo 'status' per i dettagli di qualificazione e indirizzo
            $table->string('website')->nullable()->after('status');
            $table->string('industry')->nullable()->after('website');
            $table->string('number_of_employees')->nullable()->after('industry');
            $table->string('rating')->nullable()->after('number_of_employees');
            $table->text('lead_status_reason')->nullable()->after('rating');
            $table->text('description')->nullable()->after('lead_status_reason');

            // Indirizzo strutturato
            $table->string('address_street')->nullable()->after('description');
            $table->string('address_city')->nullable()->after('address_street');
            $table->string('address_state')->nullable()->after('address_city');
            $table->string('address_postalcode')->nullable()->after('address_state');
            $table->string('address_country')->nullable()->after('address_postalcode');

            // Relazione con l'utente assegnatario
            $table->foreignId('assigned_to_id')->nullable()->after('status')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Rimuoviamo il vincolo prima della colonna
            $table->dropForeign(['assigned_to_id']);

            $table->dropColumn([
                'salutation',
                'codice_fiscale',
                'mobile_phone',
                'website',
                'industry',
                'number_of_employees',
                'rating',
                'lead_status_reason',
                'description',
                'address_street',
                'address_city',
                'address_state',
                'address_postalcode',
                'address_country',
                'assigned_to_id'
            ]);
        });
    }
};