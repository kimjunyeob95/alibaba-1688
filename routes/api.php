<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::name('v1.')->prefix('v1')->group(function () {
    Route::name('1688.')->prefix('1688')->group(function () {
        Route::get('/all/category/{categoryId?}', [ApiController::class, 'getAllCategory'])->name('getAllCategory');
        Route::get('/category/{categoryId?}', [ApiController::class, 'getCategory'])->name('getCategory');
    });
});
