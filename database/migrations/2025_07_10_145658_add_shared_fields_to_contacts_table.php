<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'salutation')) {
                $table->string('salutation')->nullable()->after('last_name');
            }
            if (!Schema::hasColumn('contacts', 'role')) {
                $table->string('role')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('contacts', 'mobile_phone')) {
                $table->string('mobile_phone')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('contacts', 'source')) {
                $table->string('source')->nullable()->after('role');
            }
            // *** MODIFICA QUI: Da string a foreignId con vincolo ***
            if (!Schema::hasColumn('contacts', 'assigned_to_id')) {
                $table->foreignId('assigned_to_id')
                      ->nullable()
                      ->constrained('users') // Vincola alla tabella 'users'
                      ->onDelete('set null') // Se l'utente viene eliminato, il campo diventa NULL
                      ->after('source');
            }
            if (!Schema::hasColumn('contacts', 'codice_fiscale')) {
                $table->string('codice_fiscale')->nullable()->after('salutation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $columns_to_drop = ['salutation', 'role', 'mobile_phone', 'source', 'codice_fiscale'];
            // Assicurati di rimuovere il vincolo foreign key prima di droppare la colonna
            if (Schema::hasColumn('contacts', 'assigned_to_id')) {
                $table->dropConstrainedForeignId('assigned_to_id');
            }
            foreach ($columns_to_drop as $column) {
                if (Schema::hasColumn('contacts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};