<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// AGGIUNGI QUESTI USE
use App\Models\ListView;
use App\Policies\ListViewPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        ListView::class => ListViewPolicy::class, // <-- AGGIUNGI QUESTA RIGA
    ];

    // ... resto del file ...
}