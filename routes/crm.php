<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CRM\CrmController;
use App\Http\Controllers\Admin\CRM\FaqController;
use App\Http\Controllers\Admin\CRM\FeedbackController;

Route::get('/customer-service', [CrmController::class, 'index'])->middleware('auth', 'isEmployee')->name('crm');

Route::prefix('/customer-service')->group(function () {
    Route::post('/submit-feedback', [CrmController::class, 'submitFeedback']);
});

Route::prefix('/customer-service')->middleware(['auth', 'isEmployee'])->group(function () {
    Route::get('/service-feedbacks', [FeedbackController::class, 'serviceFeedback'])->name('crm.feedback');
    Route::get('/feedbacks', [FeedbackController::class, 'fetchFeedbacks']);
    Route::post('/feedbacks/update-status/{feedback}', [FeedbackController::class, 'updateStatus']);

    Route::get('/faqs', [FaqController::class, 'index'])->name('crm.faqs');
    Route::get('/faqs/list', [FaqController::class, 'list']);
    Route::post('/faqs/store', [FaqController::class, 'store']);
    Route::get('/faqs/edit/{id}', [FaqController::class, 'edit']);
    Route::post('/faqs/update/{id}', [FaqController::class, 'update']);
    Route::delete('/faqs/destroy/{id}', [FaqController::class, 'destroy']);
});
