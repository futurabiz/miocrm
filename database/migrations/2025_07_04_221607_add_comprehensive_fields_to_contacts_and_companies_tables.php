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
        // Aggiungiamo i campi alla tabella Contatti
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('salutation')->nullable()->after('last_name');
            $table->string('codice_fiscale')->nullable()->after('salutation');
            $table->string('mobile_phone')->nullable()->after('phone');
            $table->text('description')->nullable()->after('role');
            $table->string('address_street')->nullable()->after('description');
            $table->string('address_city')->nullable()->after('address_street');
            $table->string('address_state')->nullable()->after('address_city');
            $table->string('address_postalcode')->nullable()->after('address_state');
            $table->string('address_country')->nullable()->after('address_postalcode');
        });

        // Aggiungiamo i campi alla tabella Aziende
        Schema::table('companies', function (Blueprint $table) {
            $table->string('industry')->nullable()->after('website');
            $table->string('number_of_employees')->nullable()->after('industry');
            $table->text('description')->nullable()->after('number_of_employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['salutation', 'codice_fiscale', 'mobile_phone', 'description', 'address_street', 'address_city', 'address_state', 'address_postalcode', 'address_country']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['industry', 'number_of_employees', 'description']);
        });
    }
};