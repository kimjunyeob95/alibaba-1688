<?php

use App\Http\Controllers\Api\W\WCategoryController;
use App\Http\Controllers\Api\W\WProductController;
use App\Http\Controllers\Api\GenuioController;
use App\Http\Controllers\Api\MallController;
use Illuminate\Support\Facades\Route;

/**
 * W API List
 */
Route::name('w.')->prefix('w')->group(function () {
    Route::middleware(["oepnApi.jwt.verify"])->group(function () {
        // 1688
        Route::name('1688.')->prefix('1688')->group(function () {
            // 1688에 상품ID 조회 endPoint를 호출 후 결과 반환
            Route::get('/product/{offerId}', [WProductController::class, 'getProductData'])->name('getCategory');
            // 1688에 카테고리 조회 endPoint를 호출 후 결과 반환
            Route::get('/category/{categoryId?}', [WCategoryController::class, 'getMallCategory'])->name('getCategory');
        });

        // 카테고리
        Route::name('category.')->prefix('category')->group(function () {
            // 1688에서 수집 한 카테고리를 단계별로 정리한 데이터 목록
            Route::get('/', [WCategoryController::class, 'getAllCategory'])->name('getAllCategory');
            // 1688에서 수집 한 최상위 카테고리 계층별 목록
            Route::get('/tree/{categoryId?}', [WCategoryController::class, 'getTreeCategory'])->name('getTreeCategory');
            // 1688<->채널 카테고리 맵핑 조회
            Route::get('/mapping/{channel?}', [WCategoryController::class, 'getMappingCategory'])->name('getTreeCategory');
        });

        // 상품 조회
        Route::get('/products', [WProductController::class, "apiPrdList"])->name("products");
    });

    Route::name('product.')->prefix('product')->group(function () {
        // 1688 상품ID 별 수집
        Route::post('/collect', [WProductController::class, 'collectProduct'])->name('collectProduct');
        // 1688 keywordQuery 수집
        Route::post('/collectKeywordQuery', [WProductController::class, 'collectKeywordQuery'])->name('collectKeywordQuery');
        // 1688 이미지ID 생성
        Route::post('/create/imgId', [WProductController::class, 'createImgId'])->name('createImgId');
        // 1688 이미지->상품ID 별 수집
        Route::post('/collect/img', [WProductController::class, 'collectProductImage'])->name('collectProductImage');
        // 1688 imageQuery 수집
        Route::post('/collectImageQuery', [WProductController::class, 'collectImageQuery'])->name('collectImageQuery');
        // 1688 상품상세 URL->상품ID 별 수집
        Route::post('/collect/url', [WProductController::class, 'collectProductUrl'])->name('collectProductUrl');
        // 1688 상품상세 URL 수집 데이터 삭제
        Route::post('/urlQuery/delete', [WProductController::class, 'urlQueryDel'])->name('urlQueryDel');
        // 1688 상품 조회 요청
        Route::post('/searchData', [WProductController::class, 'productSearchData'])->name('productSearchData');
    });

    Route::name('category.')->prefix('category')->group(function () {
        // W 카테고리 조회
        Route::post('/', [WCategoryController::class, 'getW'])->name('getW');
        // W 카테고리 맵핑
        Route::post('/mapping', [WCategoryController::class, 'wMapping'])->name('wMapping');
        // 1688 하위 카테고리 조회
        Route::get('/depth/{categoryId}', [WCategoryController::class, 'getDepth'])->name('getDepth');
        // 카테고리 정보 조회
        Route::post('/infos', [WCategoryController::class, 'getInfos'])->name('getInfos');
        // W 하위 카테고리 조회
        Route::post('/wDepth', [WCategoryController::class, 'getWDepth'])->name('getWDepth');
    });
});

/**
 * Channel API List
 */
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

/**
 * Genuio API List
 */
Route::name('genuio.')->prefix('genuio')->group(function () {
    Route::post('/token/create', [GenuioController::class, "tokenCreate"])->name("tokenCreate");

    Route::middleware(["oepnApi.jwt.verify"])->group(function () {
        Route::post('/img/trans', [GenuioController::class, "imgTrans"])->name("imgTrans");

        // 상품 조회
        Route::get('/products', [WProductController::class, "apiPrdList"])->name("products");
        // 상품 이미지 수정
        Route::patch('/products/{offerId}/images', [WProductController::class, "productsUpdateImages"])->name("productsUpdateImages");
    });

    // 상품 이미지 번역 요청
    Route::post('/img/trans/request', [GenuioController::class, 'imgTransRequest'])->name('imgTransRequest');
});