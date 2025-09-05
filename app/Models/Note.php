<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['content'];

    /**
     * Definisce la relazione polimorfica "morphTo".
     * Una nota può appartenere a un'azienda, un contatto, ecc.
     */
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
