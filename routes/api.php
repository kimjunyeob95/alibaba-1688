<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\GenuioController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::name('category.')->prefix('category')->group(function () {
    // 1688에서 수집 한 카테고리를 단계별로 정리한 데이터 목록
    Route::get('/all', [ApiController::class, 'getAllCategory'])->name('getAllCategory');
    // 1688에서 수집 한 최상위 카테고리 계층별 목록
    Route::get('/tree/{categoryId?}', [ApiController::class, 'getTreeCategory'])->name('getTreeCategory');
    // 1688<->채널 카테고리 맵핑 조회
    Route::get('/mapping/{channel?}', [ApiController::class, 'getMappingCategory'])->name('getTreeCategory');
});

Route::name('product.')->prefix('product')->group(function () {
    // 1688 상품ID 별 수집
    Route::post('/collect', [ProductController::class, 'collectProduct'])->name('collectProduct');
    // 1688 keywordQuery 수집
    Route::post('/collectKeywordQuery', [ProductController::class, 'collectKeywordQuery'])->name('collectKeywordQuery');
});

Route::name('1688.')->prefix('1688')->group(function () {
    // 1688에 상품ID 조회 endPoint를 호출 후 결과 반환
    Route::get('/product/{offerId}', [ApiController::class, 'getProductData'])->name('getCategory');
    // 1688에 카테고리 조회 endPoint를 호출 후 결과 반환
    Route::get('/category/{categoryId?}', [ApiController::class, 'getMallCategory'])->name('getCategory');
});

Route::name('genuio.')->prefix('genuio')->group(function () {
    Route::post('/token/create', [GenuioController::class, "tokenCreate"])->name("tokenCreate");

    Route::middleware(["oepnApi.jwt.verify"])->group(function () {
        Route::post('/img/trans', [GenuioController::class, "imgTrans"])->name("imgTrans");
    });
});
