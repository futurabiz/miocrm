<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'stage', 'amount', 'closing_date', 'description',
        'contact_id', 'company_id', 'assigned_to_id',
        'custom_fields_data',
    ];

    protected $casts = [
        'closing_date' => 'date',
        'custom_fields_data' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    
    // CORREZIONE: Aggiunta la relazione mancante con l'utente
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
    
    public function serviceTypes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceType::class, 'opportunity_service_type')
                    ->withPivot('quantity', 'price', 'discount');
    }
    
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    public function getTotalValueAttribute(): float
    {
        $servicesValue = $this->serviceTypes->sum(function ($service) {
            $price = $service->pivot->price ?? 0;
            $quantity = $service->pivot->quantity ?? 1;
            $discount = $service->pivot->discount ?? 0;
            return $price * $quantity * (1 - $discount / 100);
        });
        return ($this->amount ?? 0) + $servicesValue;
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }
}