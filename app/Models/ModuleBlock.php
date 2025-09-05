<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_class',
        'name',
        'order',
    ];

    /**
     * Un blocco ha molti campi.
     * CORREZIONE: La relazione punta ora al modello corretto 'ModuleField'.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(ModuleField::class);
    }
}