<?php

use App\Http\Controllers\BestSellerController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('welcome');
})->name('home');

Route::get('/4861707079204269727468646179', function () {
    return view('test');
})->name('testing');
Route::get('/hmmm', function () {
    return view('congrats');
});
Route::get('/garden', function () {
    return view('flowers');
});


Route::get('/admintest', function () {
    return view('dashboard-test');
})->name('test');

Route::get('/best-sellers/highlights', [BestSellerController::class, 'publicHighlights']);

Route::get('/menu/products', [App\Http\Controllers\Admin\CRM\CrmController::class, 'getPublicProducts']);
Route::get('/testimonials', [App\Http\Controllers\Admin\CRM\CrmController::class, 'fetchPublicTestimonials']);
Route::get('/faqs-public', [App\Http\Controllers\Admin\CRM\CrmController::class, 'getPublicFaqs']);

Route::post('/book-a-table/check-availability', [App\Http\Controllers\Admin\CRM\BookingController::class, 'checkAvailability'])->name('bookings.check');
Route::get('/book-a-table/table-slots', [App\Http\Controllers\Admin\CRM\BookingController::class, 'getTableSlots'])->name('bookings.slots');

Route::post('/book-a-table', [App\Http\Controllers\Admin\CRM\BookingController::class, 'store'])->name('bookings.store');


require __DIR__ . '/auth.php';
require __DIR__ . '/executives.php';
require __DIR__ . '/hr.php';
require __DIR__ . '/finance.php';
require __DIR__ . '/procurement.php';
require __DIR__ . '/inventory.php';
require __DIR__ . '/operations.php';
require __DIR__ . '/crm.php';
require __DIR__ . '/supplier.php';
