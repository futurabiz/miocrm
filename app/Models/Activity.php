<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importante aggiungere questo

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'start_time',
        'end_time',
        'status',
        'user_id', // CORREZIONE: Aggiunto per permettere l'assegnazione dell'utente
        'activityable_id',
        'activityable_type',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    /**
     * Get the parent activityable model (company, contact, or opportunity).
     */
    public function activityable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * CORREZIONE: Definisce la relazione con l'utente che ha creato/a cui è assegnata l'attività.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}