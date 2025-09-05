<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'trigger_model',
        'trigger_condition_field',
        'trigger_condition_operator',
        'trigger_condition_value',
        'action_type',
        'action_parameters',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'action_parameters' => 'array', // Tratta la colonna JSON come un array PHP
        'is_active' => 'boolean',
    ];
}
