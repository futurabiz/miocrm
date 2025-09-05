<?php
// File: routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importiamo tutti i controller API che useremo
use App\Http\Controllers\Api\CompanySearchController;
use App\Http\Controllers\Api\ContactSearchController;
use App\Http\Controllers\Api\UserSearchController;
use App\Http\Controllers\Api\StreetController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\FiscalCodeValidationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// --- ROTTE PER LA RICERCA CON SELECT2 ---
// Nota: la rotta per cities/search è stata rimossa perché la sua logica
// è ora gestita dal nuovo LocationController in modo più strutturato.
Route::get('/companies/search', [CompanySearchController::class, 'search'])->name('api.companies.search');
Route::get('/contacts/search', [ContactSearchController::class, 'search'])->name('api.contacts.search');
Route::get('/users/search', [UserSearchController::class, 'search'])->name('api.users.search');
Route::get('/streets/search', [StreetController::class, 'search'])->name('api.streets.search');


// --- NUOVE ROTTE PER LA LOGICA A CASCATA DEGLI INDIRIZZI ---
// Organizzate sotto un unico prefisso per pulizia
Route::prefix('locations')->name('api.locations.')->group(function () {
    Route::get('/regions', [LocationController::class, 'regions'])->name('regions');
    Route::get('/provinces/{regionId}', [LocationController::class, 'provinces'])->name('provinces');
    Route::get('/cities/{provinceId}', [LocationController::class, 'cities'])->name('cities');
    Route::get('/city/{cityId}', [LocationController::class, 'cityDetails'])->name('city.details');
});


// --- ALTRE ROTTE DI UTILITÀ ---
Route::post('/fiscal-code/validate', [FiscalCodeValidationController::class, 'validateFiscalCode'])->name('api.fiscal_code.validate');