<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'salutation', 'codice_fiscale', 'email', 'phone', 'mobile_phone',
        'company_id', 'role', 'source', 'status', 'assigned_to_id', 'rating',
        'industry', 'number_of_employees', 'website', 'lead_status_reason', 'description', 
        'address_street', 'address_city', 'address_state', 'address_postalcode', 'address_country',
        'custom_fields_data', 'gender', 'birthdate', 'city_code',
        'municipality_id', 'street_id', // Aggiunti per il salvataggio

        // --- NUOVI CAMPI LOCALITÀ ---
        'province_id', 
        'city_id', 
        'postal_code_id',
    ];

    protected $casts = [
        'custom_fields_data' => 'array',
        'birthdate' => 'date',
    ];

    // --- RELAZIONI ESISTENTI ---
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to_id'); }
    public function notes(): MorphMany { return $this->morphMany(Note::class, 'notable'); }
    public function activities(): MorphMany { return $this->morphMany(Activity::class, 'activityable'); }
    public function municipality(): BelongsTo { return $this->belongsTo(Municipality::class); }
    public function street(): BelongsTo { return $this->belongsTo(Street::class); }
    public function birthCity(): BelongsTo { return $this->belongsTo(City::class, 'city_code', 'fiscal_code'); }

    // --- NUOVE RELAZIONI LOCALITÀ ---
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function postalCode(): BelongsTo
    {
        return $this->belongsTo(PostalCode::class);
    }
}