<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    use HasFactory;

    // CORREZIONE: Sostituito $guarded con $fillable per coerenza
    protected $fillable = [
        'name',
        'description',
        'fields_schema', // Assumendo che questi siano i campi della tua tabella
    ];

    protected $casts = [
        'fields_schema' => 'array',
    ];

    public function customerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class);
    }

    public function opportunities(): BelongsToMany
    {
        return $this->belongsToMany(Opportunity::class, 'opportunity_service_type')
                    ->withPivot('quantity', 'price', 'discount');
    }
}