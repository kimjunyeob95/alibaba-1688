<?php

use App\Http\Controllers\EasySellController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", [ProductController::class, "getPrdList"]);

Route::prefix("product")->name("product.")->group(function(){
    // 상품 수집 관리
    Route::get("/queryProductDetail", [ProductController::class, "queryProductDetail"])->name("queryProductDetail");
    Route::get("/keywordQuery", [ProductController::class, "keywordQuery"])->name("keywordQuery");
    Route::get("/urlQuery", [ProductController::class, "urlQuery"])->name("urlQuery");
    Route::get("/imageQuery", [ProductController::class, "imageQuery"])->name("imageQuery");
    Route::get("/imageMultiQuery", [ProductController::class, "imageMultiQuery"])->name("imageMultiQuery");
    Route::get("/collectLogs", [ProductController::class, "prdCollectLogs"])->name("prdCollectLogs");
    Route::get("/collect/log/{logId}", [ProductController::class, "prdCollectLogDetail"])->name("prdCollectLogDetail");

    // 상품 리스트
    Route::get("/list", [ProductController::class, "getPrdList"])->name("list");
    Route::get("/{offerId}", [ProductController::class, "getPrdDetail"])->name("detail");
});

Route::prefix("easySell")->name("easySell.")->group(function(){
    // 상품 현황
    Route::get("product/list", [EasySellController::class, "getPrdList"])->name("product/list");
});