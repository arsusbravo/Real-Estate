<?php

use App\Http\Controllers\Buyer\DashboardController;
use App\Http\Controllers\Buyer\DocumentController;
use App\Http\Controllers\Buyer\FavoriteController;
use App\Http\Controllers\Buyer\RequirementController;
use App\Http\Controllers\Buyer\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Buyer Routes
|--------------------------------------------------------------------------
|
| Routes for authenticated buyers to manage their property searches,
| requirements, favorites, and transaction tracking.
|
*/

Route::middleware(['auth', 'verified', 'role:buyer'])->prefix('buyer')->name('buyer.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Favorites
    Route::get('/favorit', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/properti/{property}/favorit', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/properti/{property}/favorit', [FavoriteController::class, 'destroy'])->name('favorites.destroy');

    // Requirements (Formulir Kebutuhan)
    Route::get('/kebutuhan', [RequirementController::class, 'index'])->name('requirements.index');
    Route::get('/kebutuhan/buat', [RequirementController::class, 'create'])->name('requirements.create');
    Route::post('/kebutuhan', [RequirementController::class, 'store'])->name('requirements.store');
    Route::get('/kebutuhan/{requirement}/edit', [RequirementController::class, 'edit'])->name('requirements.edit');
    Route::put('/kebutuhan/{requirement}', [RequirementController::class, 'update'])->name('requirements.update');
    Route::delete('/kebutuhan/{requirement}', [RequirementController::class, 'destroy'])->name('requirements.destroy');

    // Transactions
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Documents
    Route::post('/transaksi/{transaction}/dokumen', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/dokumen/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
});
