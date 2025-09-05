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
        Schema::table('list_views', function (Blueprint $table) {
            // Rinomina la vecchia colonna 'module' nella nuova 'module_class'
            $table->renameColumn('module', 'module_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('list_views', function (Blueprint $table) {
            // Se annulliamo la migrazione, fa l'operazione inversa
            $table->renameColumn('module_class', 'module');
        });
    }
};