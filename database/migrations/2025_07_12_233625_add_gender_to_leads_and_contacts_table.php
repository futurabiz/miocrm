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
            // Aggiungi la colonna 'gender' dopo 'salutation'
            $table->string('gender', 1)->nullable()->after('salutation'); // 'M' o 'F'
        });

        Schema::table('contacts', function (Blueprint $table) {
            // Aggiungi la colonna 'gender' dopo 'salutation'
            $table->string('gender', 1)->nullable()->after('salutation'); // 'M' o 'F'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('gender');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};