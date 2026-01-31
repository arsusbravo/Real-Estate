<?php

use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PropertyController;
use App\Http\Middleware\SetPublicRootView;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/*
|--------------------------------------------------------------------------
| Public Routes (uses public.blade.php layout)
|--------------------------------------------------------------------------
*/

Route::middleware(SetPublicRootView::class)->group(function () {
    Route::get('/', function () {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
        ]);
    })->name('home');

    // Public property browsing
    Route::get('/properti', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properti/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');
    Route::post('/properti/{property:slug}/inquiry', [InquiryController::class, 'store'])->name('inquiries.store');

    // Static pages
    Route::get('/tentang-kami', fn () => Inertia::render('Public/About'))->name('about');
    Route::get('/layanan', fn () => Inertia::render('Public/Services'))->name('services');
    Route::get('/kontak', fn () => Inertia::render('Public/Contact'))->name('contact');
});

/*
|--------------------------------------------------------------------------
| Authenticated Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::get('dashboard', function () {
    $user = auth()->user();

    // Redirect to role-specific dashboard
    return match (true) {
        $user->isAdmin() => redirect()->route('admin.dashboard'),
        $user->isSeller() => redirect()->route('seller.dashboard'),
        default => redirect()->route('buyer.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/settings.php';
