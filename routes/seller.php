<?php

use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\DocumentController;
use App\Http\Controllers\Seller\InquiryController;
use App\Http\Controllers\Seller\PropertyController;
use App\Http\Controllers\Seller\PropertyImageController;
use App\Http\Controllers\Seller\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
|
| Routes for authenticated sellers to manage their property listings,
| inquiries, and transaction tracking.
|
*/

Route::middleware(['auth', 'verified', 'role:seller'])->prefix('seller')->name('seller.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Properties
    Route::get('/properti', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properti/buat', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properti', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properti/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properti/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properti/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::post('/properti/{property}/submit', [PropertyController::class, 'submit'])->name('properties.submit');

    // Property Images
    Route::post('/properti/{property}/gambar', [PropertyImageController::class, 'store'])->name('properties.images.store');
    Route::put('/properti/{property}/gambar/reorder', [PropertyImageController::class, 'reorder'])->name('properties.images.reorder');
    Route::delete('/properti/{property}/gambar/{media}', [PropertyImageController::class, 'destroy'])->name('properties.images.destroy');

    // Inquiries
    Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiry/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');

    // Transactions
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Documents
    Route::post('/transaksi/{transaction}/dokumen', [DocumentController::class, 'store'])->name('documents.store');
});
