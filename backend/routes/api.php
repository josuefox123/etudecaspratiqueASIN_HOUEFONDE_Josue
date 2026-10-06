<?php

declare(strict_types=1);

use App\Http\Controllers\Api\DemandeController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/demandes', [DemandeController::class, 'store']);
    Route::get('/demandes', [DemandeController::class, 'index']);
    Route::get('/usagers/{npi}/demandes', [DemandeController::class, 'indexByUsager']);
    Route::patch('/demandes/{reference}/statut', [DemandeController::class, 'updateStatut']);
    Route::get('/demandes/stats', [DemandeController::class, 'stats']);
});
