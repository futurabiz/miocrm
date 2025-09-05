<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esegue le migrazioni.
     */
    public function up(): void
    {
        // Aggiunge le colonne alla tabella 'leads'
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('province_id')->nullable()->after('address_country')->constrained('provinces')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('province_id')->constrained('cities')->onDelete('set null');
            $table->foreignId('postal_code_id')->nullable()->after('city_id')->constrained('postal_codes')->onDelete('set null');
        });

        // Aggiunge le colonne alla tabella 'contacts'
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreignId('province_id')->nullable()->after('address_country')->constrained('provinces')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('province_id')->constrained('cities')->onDelete('set null');
            $table->foreignId('postal_code_id')->nullable()->after('city_id')->constrained('postal_codes')->onDelete('set null');
        });

        // Aggiunge le colonne alla tabella 'companies'
        Schema::table('companies', function (Blueprint $table) {
            // Posizioniamo le colonne in un punto logico, ad esempio dopo i dati fiscali
            $table->foreignId('province_id')->nullable()->after('sdi_code')->constrained('provinces')->onDelete('set null');
            $table->foreignId('city_id')->nullable()->after('province_id')->constrained('cities')->onDelete('set null');
            $table->foreignId('postal_code_id')->nullable()->after('city_id')->constrained('postal_codes')->onDelete('set null');
        });
    }

    /**
     * Annulla le migrazioni.
     */
    public function down(): void
    {
        $tables = ['leads', 'contacts', 'companies'];
        $columns = ['postal_code_id', 'city_id', 'province_id']; // Ordine inverso per la rimozione

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($columns) {
                // Rimuoviamo prima i vincoli di chiave esterna, poi le colonne
                foreach ($columns as $column) {
                    $table->dropConstrainedForeignId($column);
                }
            });
        }
    }
};