<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Inventory\RecipeController;
use App\Http\Controllers\Admin\Inventory\ProductController;
use App\Http\Controllers\Admin\Inventory\InventoryController;
use App\Http\Controllers\Admin\Inventory\StockManagementController;

Route::get('/inventory', [InventoryController::class, 'index'])->middleware('auth', 'isEmployee')->name('inventory');

Route::prefix('/inventory')->middleware(['auth', 'isEmployee'])->group(function () {
    ////////////////////////////////// DASHBOARD ////////////////////////////////////////////////
    Route::get('/get-to-receive', [InventoryController::class, 'getToReceive']);
    Route::get('/get-to-restock', [InventoryController::class, 'getToRestock']);
    Route::get('/items-to-receive/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);
    Route::post('/items-to-receive/receive-inventory/{id}', [StockManagementController::class, 'receiveInventory']);
    Route::get('/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/recent-items', [InventoryController::class, 'getRecentItems']);
    Route::get('/get-all-items', [InventoryController::class, 'getAllItems']);
    Route::post('/send-restock-request', [StockManagementController::class, 'restockRequest']);

    ///////////////////////////////////// ALL ITEMS ///////////////////////////////////
    Route::get('/all-items', [InventoryController::class, 'all'])->name('inventory.all-items');
    Route::get('/data-to-display', [InventoryController::class, 'fetchDataToDisplay']);

    //////////////////////////////////////// TRANSACTIONS ////////////////////////////////////////////
    Route::get('/stock-transactions', [InventoryController::class, 'transactionsView'])->name('inventory.transactions');
    Route::get('/stock-transactions/list', [StockManagementController::class, 'stockTransactions']);

    /////////////////////////////////////////// RECIPE & PRODUCTS ///////////////////////////////////////////////////////
    Route::get('/products', [ProductController::class, 'index'])->name('inventory.products');

    Route::get('/products/get', [ProductController::class, 'getProductData']);
    Route::get('/products/get-categories', [ProductController::class, 'getCategories']);
    Route::post('/products/store', [ProductController::class, 'store']);

    Route::get('/products/{product}/servings', [ProductController::class, 'getServings']);
    Route::patch('/products/{product}/update-price', [RecipeController::class, 'updatePrice']);

    Route::post('/recipes/{product}', [RecipeController::class, 'update']);
    Route::get('/recipes/{product}/data', [RecipeController::class, 'getRecipeData']);
    Route::get('/recipes/{product}/calculate-price', [RecipeController::class, 'calculatePrice']);

    Route::get('/products/{product}', [ProductController::class, 'show']);
});
