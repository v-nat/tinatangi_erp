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




require __DIR__ . '/auth.php';
require __DIR__ . '/hr.php';
require __DIR__ . '/finance.php';