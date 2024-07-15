<?php

use App\Http\Controllers\Api\W\WCategoryController;
use App\Http\Controllers\Api\W\WProductController;
use App\Http\Controllers\Api\GenuioController;
use App\Http\Controllers\Api\MallCategoryController;
use App\Http\Controllers\Api\MallController;
use App\Http\Controllers\Api\W\ExchangeRateController;
use App\Http\Controllers\Api\W\W2ProductController;
use App\Http\Controllers\Api\W\WAppOrderController;
use App\Http\Controllers\Api\W\WCollectController;
use App\Http\Controllers\Api\W\WExceptController;
use App\Http\Controllers\Api\W\WForbiddenWordController;
use Illuminate\Support\Facades\Route;

/**
 * W API List
 */
Route::name('w.')->prefix('w')->group(function () {
    /** 1688 */
    Route::name('1688.')->prefix('1688')->group(function () {
        /** 1688에 상품ID 조회 endPoint를 호출 후 결과 반환 */
        Route::get('/product/{offerId}', [WProductController::class, 'getProductData'])->name('getProductData');
        /** 1688에 카테고리 조회 endPoint를 호출 후 결과 반환 */
        Route::get('/category/{categoryId?}', [WCategoryController::class, 'getMallCategory'])->name('getMallCategory');

        /** test */
        Route::get('/test', [WCategoryController::class, 'testEndPoint'])->name('testEndPoint');
    });

    Route::middleware(["oepnApi.jwt.verify"])->group(function () {
        /** 카테고리 */
        Route::name('category.')->prefix('category')->group(function () {
            /** 1688에서 수집 한 카테고리를 단계별로 정리한 데이터 목록 */
            Route::get('/', [WCategoryController::class, 'getAllCategory'])->name('getAllCategory');
            /** 1688에서 수집 한 최상위 카테고리 계층별 목록 */
            Route::get('/tree/{categoryId?}', [WCategoryController::class, 'getTreeCategory'])->name('getTreeCategory');
            /** 1688<->채널 카테고리 맵핑 조회 */
            Route::get('/mapping/{channel?}', [WCategoryController::class, 'getMappingCategory'])->name('getMappingCategory');
            /** W 카테고리별 인기상품 조회 */
            Route::get('/topList/{categoryId}', [WCategoryController::class, 'topList'])->name('topList');
            /** W 카테고리별 인기검색어 조회 */
            Route::get('/topKeyword/{categoryId}', [WCategoryController::class, 'topKeyword'])->name('topKeyword');
        });

        /** 수집 */
        Route::name('collect.')->prefix('collect')->group(function () {
            /** WApp 팔레트 수집 조회 */
            Route::get('/pallet/{palletId}', [WCollectController::class, "palletPrdList"])->name("palletPrdList");
            /** WApp 팔레트 수집 여부 조회 */
            Route::get('/pallet/validation/{palletId}', [WCollectController::class, "palletValidation"])->name("palletValidation");
        });

        /** 상품 */
        Route::name('products.')->prefix('products')->group(function () {
            /** 상품 조회 */
            Route::get('/', [WProductController::class, "apiPrdList"])->name("");
            /** 상품 상세 조회 */
            Route::get('/{offerId}', [WProductController::class, "apiPrdDetail"])->name("detail");
            /** W 상품 키워드 조회 */
            Route::get('/search/keywordQuery', [WProductController::class, "searchKeywordQuery"])->name("searchKeywordQuery");
            /** W 상품 상세 조회 */
            Route::get('/search/detail/{offerId}', [WProductController::class, "searchDetail"])->name("searchDetail");
            /** W 상품 이미지 ID 생성 */
            Route::post('/search/create/imageId', [WProductController::class, "searchCreateImageId"])->name("searchCreateImageId");
            /** W 상품 이미지URL로 이미지 ID 생성 */
            Route::post('/search/create/imageIdByUrl', [WProductController::class, "searchCreateImageIdByUrl"])->name("searchCreateImageIdByUrl");
            /** W 상품 이미지 조회 */
            Route::get('/search/imageQuery', [WProductController::class, "searchImageQuery"])->name("searchImageQuery");
            /** W 인기상품 조회 */
            Route::get('/search/recommend', [WProductController::class, "searchRecommend"])->name("searchRecommend");
            /** W 연관 상품 조회 */
            Route::get('/search/related/recommend/{offerId}', [WProductController::class, "searchRelatedRecommend"])->name("searchRelatedRecommend");
        });

        /** 환율 조회 */
        Route::name('exchangeRate.')->prefix('exchangeRate')->group(function() {
            Route::get('/{date}', [ExchangeRateController::class, "getExchangeRate"])->name("/");
        });
    });

    /** 상품 */
    Route::name('product.')->prefix('product')->group(function () {
        /** 1688 상품ID 별 수집 */
        Route::post('/collect', [WProductController::class, 'collectProduct'])->name('collectProduct');
        /** 1688 상품ID 별 재수집 */
        Route::post('/reCollect', [WProductController::class, 'reCollectProduct'])->name('reCollectProduct');
        /** 1688 keywordQuery 수집 */
        Route::post('/collectKeywordQuery', [WProductController::class, 'collectKeywordQuery'])->name('collectKeywordQuery');
        /** 1688 이미지ID 생성 */
        Route::post('/create/imgId', [WProductController::class, 'createImgId'])->name('createImgId');
        /** 1688 이미지->상품ID 별 수집 */
        Route::post('/collect/img', [WProductController::class, 'collectProductImage'])->name('collectProductImage');
        /** 1688 imageQuery 수집 */
        Route::post('/collectImageQuery', [WProductController::class, 'collectImageQuery'])->name('collectImageQuery');
        /** 1688 상품상세 URL->상품ID 별 수집 */
        Route::post('/collect/url', [WProductController::class, 'collectProductUrl'])->name('collectProductUrl');
        /** 1688 상품상세 URL 수집 데이터 삭제 */
        Route::post('/urlQuery/delete', [WProductController::class, 'urlQueryDel'])->name('urlQueryDel');
        /** 1688 상품 조회 요청 */
        Route::post('/searchData', [WProductController::class, 'productSearchData'])->name('productSearchData');
        /** 이미지 수집 제외 처리 */
        Route::post('/image/except', [WProductController::class, 'imageExcept'])->name('imageExcept');
        /** AI 이미지 적용 */
        Route::post('/image/accept', [WProductController::class, 'imageAccept'])->name('imageAccept');
        /** MD 판매자가 설정 */
        Route::post('/mdPrice/update', [WProductController::class, 'mdPriceUpdate'])->name('mdPriceUpdate');
        /** 판매상태 변경 */
        Route::post('/status/update', [WProductController::class, 'statusUpdate'])->name('statusUpdate');
        /** 대표 이미지 적용 */
        Route::post('/image/mainApply', [WProductController::class, 'imageMainApply'])->name('imageMainApply');
        /** 고시정보 제외 처리 */
        Route::post('/gosi/except', [WProductController::class, 'gosiExcept'])->name('gosiExcept');
        /** W1 상품 update */
        Route::post('/update', [WProductController::class, 'update'])->name('update');
        /** 검수상태 update */
        Route::post('/inspect/update', [WProductController::class, 'inspectStatusUpdate'])->name('inspectStatusUpdate');
        /** 상품 중량 저장 */
        Route::post('/weight/save', [WProductController::class, 'weightSave'])->name('weightSave');
        /** 정보고시 적용 항목명 update */
        Route::post('/notice/name/update', [WProductController::class, 'noticeNameUpdate'])->name('noticeNameUpdate');
    });

    /** 주문 */
    Route::prefix("order")->name("order.")->group(function(){
        /** WApp 주문 업데이트 */
        Route::post("/update", [WAppOrderController::class, "orderUpdate"])->name("update");
        /** WApp 주문 결제 링크 생성 */
        Route::post("/payLink/create", [WAppOrderController::class, "orderPayLinkCreate"])->name("orderPayLinkCreate");
    });

    /** 카테고리 */
    Route::name('category.')->prefix('category')->group(function () {
        /** W 카테고리 조회 */
        Route::post('/', [WCategoryController::class, 'getW'])->name('getW');
        /** W 카테고리 맵핑 */
        Route::post('/mapping', [WCategoryController::class, 'wMapping'])->name('wMapping');
        /** 1688 하위 카테고리 조회 */
        Route::get('/depth/{categoryId}', [WCategoryController::class, 'getDepth'])->name('getDepth');
        /** 카테고리 정보 조회 */
        Route::post('/infos', [WCategoryController::class, 'getInfos'])->name('getInfos');
        /** W 하위 카테고리 조회 */
        Route::post('/wDepth', [WCategoryController::class, 'getWDepth'])->name('getWDepth');
        /** 카테고리 중량 저장 */
        Route::post('/weight/save', [WCategoryController::class, 'weightSave'])->name('weightSave');
        /** 카테고리 중량 삭제 */
        Route::post('/weight/remove', [WCategoryController::class, 'weightRemove'])->name('weightRemove');
        /** 채널별 전송 카테고리 수정 */
        Route::post('/send/mall/update', [WCategoryController::class, 'sendMallUpdate'])->name('sendMallUpdate');
    });

    Route::name('forbiddenWord.')->prefix('forbiddenWord')->group(function () {
        /** 상품정보 키워드 조회 */
        Route::get('/{id}', [WForbiddenWordController::class, 'get'])->name('get');
        /** 상품정보 키워드 등록 */
        Route::post('/create', [WForbiddenWordController::class, 'create'])->name('create');
        /** 상품정보 키워드 수정 */
        Route::post('/update', [WForbiddenWordController::class, 'update'])->name('update');
        /** 상품정보 키워드 삭제 */
        Route::post('/delete', [WForbiddenWordController::class, 'delete'])->name('delete');
        /** 정보고시 키워드 조회 */
        Route::get('/notice/{id}', [WForbiddenWordController::class, 'getNotice'])->name('getNotice');
        /** 정보고시 키워드 등록 */
        Route::post('/notice/create', [WForbiddenWordController::class, 'createNotice'])->name('createNotice');
        /** 정보고시 키워드 수정 */
        Route::post('/notice/update', [WForbiddenWordController::class, 'updateNotice'])->name('updateNotice');
        /** 정보고시 키워드 삭제 */
        Route::post('/notice/delete', [WForbiddenWordController::class, 'deleteNotice'])->name('deleteNotice');
    });

    Route::name('except.')->prefix('except')->group(function () {
        /** 정보고시 제외 적용 update */
        Route::post('/notice/update', [WExceptController::class, 'noticeUpdate'])->name('noticeUpdate');
    });
});

/**
 * W2 API List
 */
Route::name('w2.')->prefix('w2')->group(function () {
    Route::name('product.')->prefix('product')->group(function () {
        /** 1688 상품ID 별 수집 */
        Route::post('/collect', [W2ProductController::class, 'collectProduct'])->name('collectProduct');
        /** 1688 keywordQuery 수집 */
        Route::post('/collectKeywordQuery', [WProductController::class, 'collectKeywordQuery'])->name('collectKeywordQuery');
        /** 1688 이미지ID 생성 */
        Route::post('/create/imgId', [WProductController::class, 'createImgId'])->name('createImgId');
        /** 1688 이미지->상품ID 별 수집 */
        Route::post('/collect/img', [WProductController::class, 'collectProductImage'])->name('collectProductImage');
        /** 1688 imageQuery 수집 */
        Route::post('/collectImageQuery', [WProductController::class, 'collectImageQuery'])->name('collectImageQuery');
        /** 1688 상품상세 URL->상품ID 별 수집 */
        Route::post('/collect/url', [WProductController::class, 'collectProductUrl'])->name('collectProductUrl');
        /** 1688 상품상세 URL 수집 데이터 삭제 */
        Route::post('/urlQuery/delete', [WProductController::class, 'urlQueryDel'])->name('urlQueryDel');
        /** 1688 상품 조회 요청 */
        Route::post('/searchData', [WProductController::class, 'productSearchData'])->name('productSearchData');
        /** 이미지 수집 제외 처리 */
        Route::post('/image/except', [WProductController::class, 'imageExcept'])->name('imageExcept');
        /** AI 이미지 적용 */
        Route::post('/image/accept', [WProductController::class, 'imageAccept'])->name('imageAccept');
        /** MD 판매자가 설정 */
        Route::post('/mdPrice/update', [WProductController::class, 'mdPriceUpdate'])->name('mdPriceUpdate');
        /** 판매상태 변경 */
        Route::post('/status/update', [W2ProductController::class, 'statusUpdate'])->name('statusUpdate');
        /** W2 상품 update */
        Route::post('/update', [W2ProductController::class, 'update'])->name('update');
    });
});

/**
 * Channel API List
 */
Route::name('mall.')->prefix('mall')->group(function () {
    Route::post('/{channel}/token/create', [MallController::class, "tokenCreate"])->name("tokenCreate");

    Route::post('/all/product/regist', [MallController::class, "allProductRegist"])->name('allProductRegist');
    Route::get('/product/regist/log/{offerId}', [MallController::class, "productRegistLog"])->name('productRegistLog');

    Route::name('{channel}.')->prefix('{channel}')->group(function () {
        /** WApp 상품 생성 후 채널 전송 */
        Route::post('/product/wapp/regist/{offerId}', [MallController::class, "productWappRegist"])->name('productWappRegist');
        Route::post('/product/regist', [MallController::class, "productRegist"])->name('productRegist');
        Route::get('/product/log/{logId}', [MallController::class, "productLog"])->name('productLog');

        /** 카테고리 */
        Route::prefix("category")->name("category.")->group(function(){
            Route::post("/depth", [MallCategoryController::class, "depth"])->name("depth");
            Route::post("/list", [MallCategoryController::class, "list"])->name("list");
            Route::post("/mapping", [MallCategoryController::class, "mapping"])->name("mapping");
        });

        Route::middleware(["oepnApi.jwt.verify"])->group(function () {
            /** 주문 조회 */
            Route::get('/order/{orderId}', [MallController::class, "orderInfo"])->name("orderInfo");
            /** 주문 생성 */
            Route::post('/order/create', [MallController::class, "orderCreate"])->name("orderCreate");

            /** 이미지 S3 upload */
            Route::post('/img/upload', [MallController::class, "imgUpload"])->name("imgUpload");

            /** Genuio */
            Route::name('genuio.')->prefix('genuio')->group(function () {
                /** 이미지 번역 요청 */
                Route::post('/img/trans/request', [MallController::class, "imgTransRequest"])->name("imgTransRequest");
                /** 번역 된 이미지 처리 */
                Route::post('/img/trans', [MallController::class, "imgTrans"])->name("imgTrans");
            });
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

        /** 상품 조회 */
        Route::get('/products', [WProductController::class, "apiPrdList"])->name("products");
        /** 상품 상세 조회 */
        Route::get('/products/{offerId}', [WProductController::class, "apiPrdDetail"])->name("productsDetail");
        /** 상품 이미지 제외 처리 */
        Route::patch('/products/{offerId}/images/except', [WProductController::class, 'imageExcept'])->name('imageExcept');
        /** AI 이미지 저장 */
        Route::patch('/products/{offerId}/images', [GenuioController::class, "imgAiRegist"])->name("imgAiRegist");
    });

    /** 상품 이미지 번역 요청 */
    Route::post('/img/trans/request', [GenuioController::class, 'imgTransRequest'])->name('imgTransRequest');
    /** 상품 썸네일 이미지 번역 요청 */
    Route::post('/img/thumnail/trans/request/{offerId}', [GenuioController::class, 'imgThumnailTransRequest'])->name('imgThumnailTransRequest');
    /** 상품 상세 이미지 번역 요청 */
    Route::post('/img/desc/trans/request/{offerId}', [GenuioController::class, 'imgDescTransRequest'])->name('imgDescTransRequest');
    /** 이미지 별 AI 알고리즘 요청 */
    Route::post('/img/ai/trans/request', [GenuioController::class, 'imgAiTransRequest'])->name('imgAiTransRequest');
    /** 큐 삭제 */
    Route::post('/queue/remove', [GenuioController::class, 'queueRemove'])->name('queueRemove');
});