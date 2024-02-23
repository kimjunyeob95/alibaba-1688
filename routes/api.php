<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/all/category/{categoryId?}', [ApiController::class, 'getAllCategory'])->name('getAllCategory');
Route::name('1688.')->prefix('1688')->group(function () {
    Route::get('/category/{categoryId?}', [ApiController::class, 'get1688Category'])->name('getCategory');
});
