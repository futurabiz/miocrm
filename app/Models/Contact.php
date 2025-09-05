<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'salutation', 'codice_fiscale', 'email', 'phone', 'mobile_phone',
        'role', 'company_id', 'description',
        'address_street', 'address_city', 'address_state', 'address_postalcode', 'address_country',
        'custom_fields_data', 'assigned_to_id', 'gender', 'birthdate', 'city_code',
        
        // --- NUOVI CAMPI LOCALITÀ ---
        'province_id', 
        'city_id', 
        'postal_code_id',
    ];

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    protected $casts = [
        'custom_fields_data' => 'array',
        'birthdate' => 'date',
    ];

    // --- RELAZIONI ESISTENTI ---
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to_id'); }
    public function opportunities(): HasMany { return $this->hasMany(Opportunity::class); }
    public function notes(): MorphMany { return $this->morphMany(Note::class, 'notable'); }
    public function activities(): MorphMany { return $this->morphMany(Activity::class, 'activityable'); }
    public function customerServices(): MorphMany { return $this->morphMany(CustomerService::class, 'customerable'); }
    public function emailLists(): BelongsToMany { return $this->belongsToMany(EmailList::class, 'contact_email_list'); }
    public function tags(): BelongsToMany { return $this->belongsToMany(Tag::class, 'contact_tag'); }

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