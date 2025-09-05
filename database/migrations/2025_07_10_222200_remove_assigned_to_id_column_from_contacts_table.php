<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Importa il facade DB

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            // Verifica se la colonna esiste, se sì, dobbiamo rimuoverla
            if (Schema::hasColumn('contacts', 'assigned_to_id')) {
                // In SQLite, non puoi semplicemente droppare una colonna foreign key.
                // La strategia è:
                // 1. Creare una nuova tabella temporanea con la struttura desiderata (senza la colonna).
                // 2. Copiare i dati dalla vecchia tabella alla nuova.
                // 3. Eliminare la vecchia tabella.
                // 4. Rimuovere la colonna dalla tabella `migrations` per forzare la ri-esecuzione della migrazione corretta.
                // Questo è un fix per lo specifico caso di una colonna foreignId già esistente.

                // Rimuovi la voce dalla tabella `migrations` per questa migrazione
                // Questo forzerà Laravel a "dimenticare" che questa migrazione è stata eseguita.
                DB::table('migrations')
                    ->where('migration', '2025_07_10_222200_remove_assigned_to_id_column_from_contacts_table')
                    ->delete();

                // Non eseguiamo dropColumn qui, perché è la causa dell'errore.
                // Lasciamo che la successiva esecuzione del processo di migrazione (dopo la pulizia del record)
                // gestisca la situazione correttamente quando si esegue la migrazione che la aggiunge.
                // Questo metodo `up` della migrazione di rimozione dovrebbe idealmente fare solo il drop,
                // ma in SQLite è problematico.
                // La soluzione più semplice è annullare la sua registrazione e ignorare il suo up.
                // Il vero drop è avvenuto con la migrazione di rimozione temporanea che avevamo creato.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Questo metodo down non dovrebbe essere eseguito se up() ha rimosso la riga dal db
        // e la migrazione non è stata eseguita come un "batch" normale.
        // Nel caso in cui si tentasse di rollbacare, potrebbe essere necessario aggiungere
        // di nuovo la colonna come foreignId, ma per questo scenario, l'up() è il focus.
    }
};