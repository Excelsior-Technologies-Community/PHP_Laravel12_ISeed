<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeederController;

Route::get('/', function () {
    return view('welcome');
});

// Product Routes
Route::resource('products', ProductController::class);

// Additional route for the iSeed demonstration
Route::get('/iseed-demo', function () {
    return view('iseed-demo');
});

Route::get('/products/export', [ProductController::class, 'export'])
    ->name('products.export');

Route::get('/seeder-dashboard', [SeederController::class, 'index'])
    ->name('seeder.dashboard');

Route::post('/seeder-generate', [SeederController::class, 'generate'])
    ->name('seeder.generate');

Route::post('/seeder-run', [SeederController::class, 'run'])
    ->name('seeder.run');

Route::get('/seeder-download/{name}', [SeederController::class, 'download'])
    ->name('seeder.download');
