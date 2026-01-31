<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\NotaryController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes for platform administrators to manage all aspects of the
| real estate platform including users, properties, transactions,
| and reports.
|
*/

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Properties
    Route::get('/properti', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properti/pending', [PropertyController::class, 'pending'])->name('properties.pending');
    Route::get('/properti/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::put('/properti/{property}', [PropertyController::class, 'update'])->name('properties.update');
    Route::put('/properti/{property}/approve', [PropertyController::class, 'approve'])->name('properties.approve');
    Route::put('/properti/{property}/reject', [PropertyController::class, 'reject'])->name('properties.reject');
    Route::put('/properti/{property}/feature', [PropertyController::class, 'feature'])->name('properties.feature');
    Route::delete('/properti/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');

    // Users
    Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
    Route::get('/pengguna/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
    Route::put('/pengguna/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Transactions
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/buat', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::put('/transaksi/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::put('/transaksi/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::put('/transaksi/{transaction}/assign-notary', [TransactionController::class, 'assignNotary'])->name('transactions.assign-notary');
    Route::post('/transaksi/{transaction}/activity', [TransactionController::class, 'addActivity'])->name('transactions.add-activity');

    // Notary Partners
    Route::get('/notaris', [NotaryController::class, 'index'])->name('notaries.index');
    Route::get('/notaris/buat', [NotaryController::class, 'create'])->name('notaries.create');
    Route::post('/notaris', [NotaryController::class, 'store'])->name('notaries.store');
    Route::get('/notaris/{notary}/edit', [NotaryController::class, 'edit'])->name('notaries.edit');
    Route::put('/notaris/{notary}', [NotaryController::class, 'update'])->name('notaries.update');
    Route::delete('/notaris/{notary}', [NotaryController::class, 'destroy'])->name('notaries.destroy');

    // Inquiries
    Route::get('/inquiry', [InquiryController::class, 'index'])->name('inquiries.index');
    Route::get('/inquiry/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::put('/inquiry/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
    Route::post('/inquiry/{inquiry}/convert', [InquiryController::class, 'convertToTransaction'])->name('inquiries.convert');

    // Documents
    Route::get('/dokumen/pending', [DocumentController::class, 'pending'])->name('documents.pending');
    Route::put('/dokumen/{document}/verify', [DocumentController::class, 'verify'])->name('documents.verify');
    Route::put('/dokumen/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');

    // Reports
    Route::get('/laporan/transaksi', [ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('/laporan/properti', [ReportController::class, 'properties'])->name('reports.properties');
    Route::get('/laporan/pendapatan', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/laporan/export/{type}', [ReportController::class, 'export'])->name('reports.export');

    // Settings
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
});
