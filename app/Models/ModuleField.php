<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModuleField extends Model
{
    use HasFactory;

    // La tabella associata al modello
    protected $table = 'module_fields'; 

    // Campi che possono essere assegnati massivamente
    protected $fillable = [
        'module_block_id',
        'name',
        'label',
        'type',
        'is_standard',
        'is_required',
        'is_visible',
        'order',
        'options', // Assicurati che 'options' sia fillable se lo aggiorni dal codice
    ];

    // Cast per gli attributi, specialmente 'options' se è JSON
    protected $casts = [
        'options' => 'array',
        'is_standard' => 'boolean',
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
    ];

    /**
     * Get the module block that owns the field.
     */
    public function moduleBlock(): BelongsTo
    {
        return $this->belongsTo(ModuleBlock::class);
    }
}