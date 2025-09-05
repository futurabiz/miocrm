<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Aggiungi questo

class City extends Model
{
    public $timestamps = false;
    protected $fillable = ['id', 'name', 'province_id', 'fiscal_code'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    // NUOVA RELAZIONE: Un comune ha molti CAP
    public function postalCodes(): HasMany
    {
        return $this->hasMany(PostalCode::class);
    }
}