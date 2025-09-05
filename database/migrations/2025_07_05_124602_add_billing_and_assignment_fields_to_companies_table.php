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
            $table->string('legal_form')->nullable()->after('name')->comment('Forma Giuridica');
            $table->string('company_tax_code')->nullable()->after('vat_number')->comment('Codice Fiscale Azienda');
            $table->string('pec_address')->nullable()->after('email')->comment('Indirizzo PEC');
            $table->string('sdi_code')->nullable()->after('pec_address')->comment('Codice Destinatario SDI');
            $table->foreignId('assigned_to_id')->nullable()->after('description')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['assigned_to_id']);
            $table->dropColumn([
                'legal_form',
                'company_tax_code',
                'pec_address',
                'sdi_code',
                'assigned_to_id'
            ]);
        });
    }
};