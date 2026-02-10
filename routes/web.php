<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

// Product Routes
Route::resource('products', ProductController::class);

// Additional route for the iSeed demonstration
Route::get('/iseed-demo', function () {
    return view('iseed-demo');
});