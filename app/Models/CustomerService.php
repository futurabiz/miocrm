<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustomerService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_type_id',
        'customerable_id',
        'customerable_type',
        'custom_fields_data',
        'status',
    ];

    /**
     * Laravel tratterà automaticamente la colonna 'custom_fields_data' come un array/oggetto JSON.
     */
    protected $casts = [
        'custom_fields_data' => 'array',
    ];

    /**
     * Un servizio cliente appartiene a un tipo di servizio.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Un servizio cliente appartiene a un "cliente" (che può essere un'Azienda o un Contatto).
     */
    public function customerable(): MorphTo
    {
        return $this->morphTo();
    }
}
