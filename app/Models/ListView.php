<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListView extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'module_class', // MODIFICATO: da 'module' a 'module_class' per coerenza
        'columns',
        'is_default',
        'user_id'       // AGGIUNTO: Permette di salvare l'ID dell'utente
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'columns' => 'array', // Tratta la colonna JSON come un array PHP
        'is_default' => 'boolean',
    ];

    /**
     * Relazione: una vista appartiene a un utente.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}