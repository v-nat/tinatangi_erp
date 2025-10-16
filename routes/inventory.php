<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Finance\FinanceController;
use App\Http\Controllers\Admin\Finance\InvoiceController;
use App\Http\Controllers\Admin\Inventory\RecipeController;
use App\Http\Controllers\Admin\Inventory\ProductController;
use App\Http\Controllers\Admin\Inventory\InventoryController;
use App\Http\Controllers\Admin\Inventory\StockManagementController;

Route::middleware(['auth', 'isEmployee'])->group(function () {
    ////////////////////////////////// DASHBOARD ////////////////////////////////////////////////
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('/inventory/get-to-receive', [InventoryController::class, 'getToReceive']);
    Route::get('/inventory/get-to-restock', [InventoryController::class, 'getToRestock']);
    Route::get('/inventory/items-to-receive/get-invoice/{id}', [InvoiceController::class, 'getInvoiceForViewing']);
    Route::post('/inventory/items-to-receive/receive-inventory/{id}', [StockManagementController::class, 'receiveInventory']);
    Route::get('/inventory/purchases/get-details/{id}', [FinanceController::class, 'getDetailsForViewing']);
    Route::get('/inventory/recent-items', [InventoryController::class, 'getRecentItems']);
    Route::get('/inventory/get-all-items', [InventoryController::class, 'getAllItems']);
    Route::post('/inventory/send-restock-request', [StockManagementController::class, 'restockRequest']);

    ///////////////////////////////////// ALL ITEMS ///////////////////////////////////
    Route::get('/inventory/all-items', [InventoryController::class, 'all'])->name('inventory.all-items');
    Route::get('/inventory/data-to-display', [InventoryController::class, 'fetchDataToDisplay']);

    //////////////////////////////////////// TRANSACTIONS ////////////////////////////////////////////
    Route::get('/inventory/stock-transactions', [InventoryController::class, 'transactionsView'])->name('inventory.transactions');
    Route::get('/inventory/stock-transactions/list', [StockManagementController::class, 'stockTransactions']);

    /////////////////////////////////////////// RECIPE ///////////////////////////////////////////////////////
    Route::get('/inventory/products', [ProductController::class, 'index'])->name('inventory.products');
    Route::get('/inventory/products/get', [ProductController::class, 'getProductData']);
    Route::get('/inventory/products/get-categories', [ProductController::class, 'getCategories']);
    Route::post('/inventory/products/store', [ProductController::class, 'store']);
    Route::post('/inventory/recipes/{product}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::get('/inventory/products/{product}/servings', [ProductController::class, 'getServings']);
    Route::get('/inventory/recipes/{product}/data', [RecipeController::class, 'getRecipeData'])->name('api.recipes.data');
    Route::get('/inventory/recipes/{product}/calculate-price', [RecipeController::class, 'calculatePrice'])->name('api.recipes.calculate_price');
    Route::patch('/inventory/products/{product}/update-price', [RecipeController::class, 'updatePrice'])->name('api.products.update_price');
});
