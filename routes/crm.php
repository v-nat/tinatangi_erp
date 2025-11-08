<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CRM\CrmController;
use App\Http\Controllers\Admin\CRM\FaqController;
use App\Http\Controllers\Admin\CRM\BookingController;
use App\Http\Controllers\Admin\CRM\FeedbackController;
use App\Http\Controllers\Admin\CRM\TableForReservationController;

Route::get('/customer-service', [CrmController::class, 'index'])->middleware('auth', 'isEmployee')->name('crm');

Route::prefix('/customer-service')->group(function () {
    Route::post('/submit-feedback', [FeedbackController::class, 'submitFeedback']);
});

Route::prefix('/customer-service')->middleware(['auth', 'isEmployee'])->group(function () {
    Route::get('/dashboard-analytics', [CrmController::class, 'getDashboardAnalytics']);

    Route::get('/service-feedbacks', [CrmController::class, 'serviceFeedback'])->name('crm.feedback');
    Route::get('/feedbacks', [FeedbackController::class, 'fetchFeedbacks']);
    Route::post('/feedbacks/update-status/{feedback}', [FeedbackController::class, 'updateStatus']);
    Route::delete('/feedbacks/destroy/{feedback}', [FeedbackController::class, 'destroy']);
    Route::delete('/feedbacks/batch-destroy', [FeedbackController::class, 'batchDestroy']);

    Route::get('/faqs', [FaqController::class, 'index'])->name('crm.faqs');
    Route::get('/faqs/list', [FaqController::class, 'list']);
    Route::post('/faqs/store', [FaqController::class, 'store']);
    Route::get('/faqs/edit/{id}', [FaqController::class, 'edit']);
    Route::post('/faqs/update/{id}', [FaqController::class, 'update']);
    Route::delete('/faqs/destroy/{id}', [FaqController::class, 'destroy']);
    Route::delete('/faqs/batch-destroy', [FaqController::class, 'batchDestroy']);

    Route::get('/bookings', [BookingController::class, 'bookingPage'])->name('crm.bookings');
    Route::get('/bookings/all', [BookingController::class, 'getBookings']);
    Route::post('/bookings/update-status', [BookingController::class, 'updateBookingStatus']);
    Route::delete('/bookings/delete/{booking}', [BookingController::class, 'destroyBooking']);

    Route::get('/table-management', [CrmController::class, 'tableManagement'])->name('crm.tables');
    Route::get('/tables/list', [TableForReservationController::class, 'list']);
    Route::post('/tables/store', [TableForReservationController::class, 'store']);
    Route::get('/tables/edit/{table}', [TableForReservationController::class, 'edit']);
    Route::post('/tables/update/{table}', [TableForReservationController::class, 'update']);
    Route::delete('/tables/destroy/{table}', [TableForReservationController::class, 'destroy']);
    Route::delete('/tables/batch-destroy', [TableForReservationController::class, 'batchDestroy']);
});
