<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_block_id',
        'name',
        'label',
        'type',
        'options', // <-- CORREZIONE: Aggiunto il campo 'options' qui
        'is_standard',
        'is_required',
        'is_visible',
        'order',
    ];

    /**
     * Converte i valori booleani quando letti/scritti.
     */
    protected $casts = [
        'is_standard' => 'boolean',
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
    ];

    /**
     * Un campo appartiene a un blocco.
     */
    public function block(): BelongsTo
    {
        return $this->belongsTo(ModuleBlock::class, 'module_block_id');
    }
}