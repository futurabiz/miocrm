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
            $table->json('custom_fields_data')->nullable()->after('website');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->json('custom_fields_data')->nullable()->after('company_id');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->json('custom_fields_data')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('custom_fields_data');
        });
    }
};