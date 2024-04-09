<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\GenuioController;
use App\Http\Controllers\MallController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\W\WProductController;
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
    // 1688 이미지ID 생성
    Route::post('/create/imgId', [ProductController::class, 'createImgId'])->name('createImgId');
    // 1688 이미지->상품ID 별 수집
    Route::post('/collect/img', [ProductController::class, 'collectProductImage'])->name('collectProductImage');
    // 1688 imageQuery 수집
    Route::post('/collectImageQuery', [ProductController::class, 'collectImageQuery'])->name('collectImageQuery');
    // 1688 상품상세 URL->상품ID 별 수집
    Route::post('/collect/url', [ProductController::class, 'collectProductUrl'])->name('collectProductUrl');
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

        // 상품 조회
        Route::get('/products', [WProductController::class, "apiPrdList"])->name("products");
    });

    // 상품 이미지 번역 요청
    Route::post('/img/trans/request', [GenuioController::class, 'imgTransRequest'])->name('imgTransRequest');
});

Route::name('mall.')->prefix('mall')->group(function () {
    Route::post('/{channel}/token/create', [MallController::class, "tokenCreate"])->name("tokenCreate");

    Route::name('easySell.')->prefix('easySell')->group(function () {
        Route::post('/product/regist', [MallController::class, "productRegist"])->name('productRegist');

        Route::middleware(["oepnApi.jwt.verify"])->group(function () {
            // 주문 조회
            Route::get('/order/{orderId}', [MallController::class, "orderInfo"])->name("orderInfo");
            // 주문 생성
            Route::post('/order/create', [MallController::class, "orderCreate"])->name("orderCreate");
        });
    });
});

Route::name('w.')->prefix('w')->group(function () {
    
    Route::middleware(["oepnApi.jwt.verify"])->group(function () {
        // 상품 조회
        Route::get('/products', [WProductController::class, "apiPrdList"])->name("products");
    });
});