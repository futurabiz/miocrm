<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'legal_form', 'vat_number', 'company_tax_code',
        'address', 'city', 'zip_code', 'country', 'phone', 'email', 'pec_address',
        'sdi_code', 'website', 'industry', 'number_of_employees', 'description',
        'assigned_to_id', 'custom_fields_data', 'main_contact_id',

        // --- NUOVI CAMPI LOCALITÀ ---
        'province_id', 
        'city_id', 
        'postal_code_id',
    ];
    
    protected $casts = [
        'custom_fields_data' => 'array',
    ];

    // --- RELAZIONI ESISTENTI ---
    public function contacts(): HasMany { return $this->hasMany(Contact::class); }
    public function opportunities(): HasMany { return $this->hasMany(Opportunity::class); }
    public function notes(): MorphMany { return $this->morphMany(Note::class, 'notable'); }
    public function activities(): MorphMany { return $this->morphMany(Activity::class, 'activityable'); }
    public function customerServices(): MorphMany { return $this->morphMany(CustomerService::class, 'customerable'); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to_id'); }
    public function mainContact(): BelongsTo { return $this->belongsTo(Contact::class, 'main_contact_id'); }
    
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