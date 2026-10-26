<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeederController;

Route::get('/', function () {
    return view('welcome');
});

// Product Routes
Route::resource('products', ProductController::class);

// Educational Demo
Route::get('/iseed-demo', function () {
    return view('iseed-demo');
});

Route::get('/products/export', [ProductController::class, 'export'])
    ->name('products.export');

// Seeder Studio & Management Routes
Route::get('/seeder-dashboard', [SeederController::class, 'index'])
    ->name('seeder.dashboard');

Route::post('/seeder-generate', [SeederController::class, 'generate'])
    ->name('seeder.generate');

Route::post('/seeder-run', [SeederController::class, 'run'])
    ->name('seeder.run');

Route::get('/seeder-code/{name}', [SeederController::class, 'viewCode'])
    ->name('seeder.code');

Route::get('/seeder-diff/{id}', [SeederController::class, 'diff'])
    ->name('seeder.diff');

Route::post('/seeder-update-code', [SeederController::class, 'updateCode'])
    ->name('seeder.update.code');

Route::get('/seeder-download/{name}', [SeederController::class, 'download'])
    ->name('seeder.download');

Route::delete('/seeder/{id}', [SeederController::class, 'destroy'])
    ->name('seeder.destroy');
