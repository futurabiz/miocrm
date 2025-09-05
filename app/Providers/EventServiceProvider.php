<?php

namespace App\Providers;

// Aggiungiamo tutti i modelli e gli observer che usiamo
use App\Models\Opportunity;
use App\Observers\OpportunityObserver;
use App\Models\Lead;
use App\Observers\LeadObserver;
use App\Models\Activity;
use App\Observers\ActivityObserver;
use App\Models\Company;
use App\Observers\CompanyObserver;
use App\Models\Contact;
use App\Observers\ContactObserver;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * The model observers for your application.
     *
     * @var array
     *
     * --- DISABILITIAMO QUESTO METODO ---
     * Invece di usare l'array, usiamo la registrazione manuale nel metodo boot()
     * per essere sicuri al 100% che Laravel li carichi.
     */
    // protected $observers = [
    //     Opportunity::class => [OpportunityObserver::class],
    //     Lead::class => [LeadObserver::class],
    //     Activity::class => [ActivityObserver::class],
    //     Company::class => [CompanyObserver::class],
    //     Contact::class => [ContactObserver::class],
    // ];

    /**
     * Register any events for your application.
     *
     * --- AGGIUNGIAMO QUI LA REGISTRAZIONE MANUALE ---
     */
    public function boot(): void
    {
        // Questo è il metodo più esplicito e diretto per dire a Laravel
        // di usare un Observer per un determinato Modello.
        Contact::observe(ContactObserver::class);
        Company::observe(CompanyObserver::class);
        Lead::observe(LeadObserver::class);
        Opportunity::observe(OpportunityObserver::class);
        Activity::observe(ActivityObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}