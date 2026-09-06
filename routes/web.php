<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\LeaseController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/ar'));
Route::get('/test', fn () => 'Laravel is working!');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');

Route::middleware('admin.locale')->prefix('admin')->group(function (): void {
    Route::get('/login', [AdminController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/lang/{locale}', [AdminController::class, 'setLocale'])->name('admin.lang');
    Route::middleware('admin')->group(function (): void {
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');
        Route::get('/', [AdminController::class, 'index'])->name('admin.index');
        Route::get('/properties/{property}', [AdminController::class, 'units'])->name('admin.units');
        Route::post('/properties/{property}/units', [AdminController::class, 'updateUnits'])->name('admin.units.update');
        Route::get('/map', [AdminController::class, 'map'])->name('admin.map');
        Route::post('/units/{unit}/geometry', [AdminController::class, 'saveUnitGeometry'])->name('admin.units.geometry');
        Route::delete('/units/{unit}/geometry', [AdminController::class, 'deleteUnitGeometry'])->name('admin.units.geometry.delete');
        Route::post('/parcels', [AdminController::class, 'saveParcel'])->name('admin.parcels.save');
        Route::delete('/parcels/{parcel}', [AdminController::class, 'deleteParcel'])->name('admin.parcels.delete');

        Route::resource('customers', CustomerController::class)->names('admin.customers');
        Route::resource('leases', LeaseController::class)->except('show')->names('admin.leases');
        Route::get('/leases/{lease}', [LeaseController::class, 'show'])->name('admin.leases.show');
        Route::patch('/leases/{lease}/status', [LeaseController::class, 'changeStatus'])->name('admin.leases.status');
        Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::patch('/payments/{payment}', [PaymentController::class, 'update'])->name('admin.payments.update');
    });
});

Route::prefix('{locale}')
    ->where(['locale' => 'ar|en|zh'])
    ->middleware('locale')
    ->group(function (): void {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        Route::get('/warehouses', [SiteController::class, 'properties'])->name('properties.index');
        Route::get('/warehouses/{slug}', [SiteController::class, 'property'])->name('properties.show');
        Route::get('/media', [SiteController::class, 'media'])->name('media');
        Route::get('/map', [SiteController::class, 'map'])->name('map');
        Route::get('/about', [SiteController::class, 'about'])->name('about');
        Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
        Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:10,1')->name('inquiries.store');
    });
