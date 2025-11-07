<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
})->name('home');


Route::get('/admintest', function () {
    return view('dashboard-test');
})->name('test');

Route::get('/menu/products', [App\Http\Controllers\Admin\CRM\CrmController::class, 'getPublicProducts']);
Route::get('/testimonials', [App\Http\Controllers\Admin\CRM\CrmController::class, 'fetchPublicTestimonials']);
Route::get('/faqs-public', [App\Http\Controllers\Admin\CRM\CrmController::class, 'getPublicFaqs']);
Route::post('/book-a-table', [App\Http\Controllers\Admin\CRM\BookingController::class, 'store'])->name('bookings.store');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
require __DIR__ . '/hr.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/procurement.php';
require __DIR__ . '/inventory.php';
require __DIR__ . '/operations.php';
require __DIR__ . '/crm.php';
require __DIR__ . '/supplier.php';
