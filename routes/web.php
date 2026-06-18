<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/campaign-report');
Route::get('/client-consumption', [DashboardController::class, 'clientConsumption'])->name('client-consumption');
Route::get('/campaign-report', [DashboardController::class, 'campaignReport'])->name('campaign-report');

Route::get('/sync', [DashboardController::class, 'showSync'])->name('sync.show');
Route::post('/import-excel', [DashboardController::class, 'import'])->name('import.excel');
Route::post('/sync-google-sheet', [DashboardController::class, 'syncGoogleSheet'])->name('sync.google-sheet');
Route::get('/export/consumption', [DashboardController::class, 'exportConsumption'])->name('export.consumption');
Route::get('/export/campaigns', [DashboardController::class, 'exportCampaigns'])->name('export.campaigns');
