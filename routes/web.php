<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OpportunityController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\EmailListController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ListViewController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Api\CompanySearchController;
use App\Http\Controllers\Api\ContactSearchController;
use App\Http\Controllers\Api\UserSearchController;
use App\Http\Controllers\Api\CitySearchController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\StreetController;
use App\Http\Controllers\Api\FiscalCodeValidationController;
use App\Http\Controllers\ProfileController;

// Rotta per la radice
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Middleware di autenticazione E WEB
Route::middleware(['web', 'auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CORREZIONE: Aggiunte rotte di esportazione PRIMA delle resource
    Route::get('companies/export', [CompanyController::class, 'export'])->name('companies.export');
    Route::get('contacts/export', [ContactController::class, 'export'])->name('contacts.export');
    Route::get('leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::get('opportunities/export', [OpportunityController::class, 'export'])->name('opportunities.export');

    // Aziende, Contatti, Leads, Opportunità, Attività e Note
    Route::resource('companies', CompanyController::class);
    Route::resource('contacts', ContactController::class);
    Route::resource('leads', LeadController::class);
    Route::resource('opportunities', OpportunityController::class);

    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Servizi e Customer Services
    Route::resource('service_types', ServiceTypeController::class);
    Route::resource('customer_services', CustomerServiceController::class);

    // Workflows - tutte le rotte speciali e la resource
    Route::get('workflows/get-fields', [WorkflowController::class, 'getFields'])->name('workflows.getFields');
    Route::get('workflows/get-field-options', [WorkflowController::class, 'getFieldOptions'])->name('workflows.getFieldOptions');
    Route::get('workflows/get-initial-entity', [WorkflowController::class, 'getInitialEntity'])->name('workflows.getInitialEntity');
    Route::get('workflows/search-values', [WorkflowController::class, 'searchValues'])->name('workflows.searchValues');
    Route::get('workflows/get-action-parameters', [WorkflowController::class, 'getActionParameters'])->name('workflows.getActionParameters');
    Route::resource('workflows', WorkflowController::class);

    // Email marketing e opzionali
    Route::resource('email_lists', EmailListController::class);
    Route::resource('tags', TagController::class);
    Route::resource('email_templates', EmailTemplateController::class);
    Route::resource('list_views', ListViewController::class)->only(['store', 'update', 'destroy']);

    // Importazione dati
    Route::get('import', [ImportController::class, 'create'])->name('import.create');
    Route::post('import/upload', [ImportController::class, 'upload'])->name('import.upload');
    Route::post('import/map', [ImportController::class, 'map'])->name('import.map');
    Route::post('import/process', [ImportController::class, 'process'])->name('import.process');

    // Calendario
    Route::get('calendar', [CalendarController::class, 'index'])->name('calendario.index');

    // API interne con prefisso
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/companies/search', [CompanySearchController::class, 'search'])->name('companies.search');
        Route::get('/contacts/search', [ContactSearchController::class, 'search'])->name('contacts.search');
        Route::get('/users/search', [UserSearchController::class, 'search'])->name('users.search');
        Route::get('/cities/search', [CitySearchController::class, 'search'])->name('cities.search');
        Route::get('/address/municipalities', [AddressController::class, 'search'])->name('address.municipalities.search');
        Route::get('/address/streets', [StreetController::class, 'search'])->name('address.streets.search');
        Route::post('/validate-fiscal-code', [FiscalCodeValidationController::class, 'validateFiscalCode'])->name('fiscal-code.validate');
    });

    // Profilo utente
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rotte di autenticazione incluse dal file di default Laravel
require __DIR__.'/auth.php';