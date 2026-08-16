<?php

use App\Http\Controllers\InquiryController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/ar'));
Route::get('/test', fn () => 'Laravel is working!');
Route::get('/sitemap.xml', [SiteController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SiteController::class, 'robots'])->name('robots');

Route::prefix('{locale}')
    ->where(['locale' => 'ar|en|zh'])
    ->middleware('locale')
    ->group(function (): void {
        Route::get('/', [SiteController::class, 'home'])->name('home');
        Route::get('/warehouses', [SiteController::class, 'properties'])->name('properties.index');
        Route::get('/warehouses/{slug}', [SiteController::class, 'property'])->name('properties.show');
        Route::get('/media', [SiteController::class, 'media'])->name('media');
        Route::get('/about', [SiteController::class, 'about'])->name('about');
        Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
        Route::post('/inquiries', [InquiryController::class, 'store'])->middleware('throttle:10,1')->name('inquiries.store');
    });
