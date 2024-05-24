<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EasySellController;
use App\Http\Controllers\ExceptController;
use App\Http\Controllers\ForbiddenWordController;
use App\Http\Controllers\OnchannelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductW2Controller;
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

/**
 * 상품
 */
Route::prefix("product")->name("product.")->group(function(){
    /** 상품 수집 관리 */
    Route::get("/queryProductDetail", [ProductController::class, "queryProductDetail"])->name("queryProductDetail");
    Route::get("/keywordQuery", [ProductController::class, "keywordQuery"])->name("keywordQuery");
    Route::get("/urlQuery", [ProductController::class, "urlQuery"])->name("urlQuery");
    Route::get("/urlQuery/log/{searchId}", [ProductController::class, "urlQueryDetail"])->name("urlQueryDetail");
    Route::get("/imageQuery", [ProductController::class, "imageQuery"])->name("imageQuery");
    Route::get("/imageMultiQuery", [ProductController::class, "imageMultiQuery"])->name("imageMultiQuery");
    Route::get("/collectLogs", [ProductController::class, "prdCollectLogs"])->name("prdCollectLogs");
    Route::get("/collect/log/{logId}", [ProductController::class, "prdCollectLogDetail"])->name("prdCollectLogDetail");

    /** 검수 중 */
    Route::prefix("noInspect")->name("noInspect.")->group(function(){
        /** 전체상품(KOR) */
        Route::get("/list", [ProductController::class, "noInspectList"])->name("list");
        /** Drop.Hub 상품(ENG) */
        Route::get("/w2/list", [ProductW2Controller::class, "noInspectList"])->name("w2.list");
        /** 판매제외 상품 리스트 */
        Route::get("/except/list", [ProductController::class, "noInspectExceptList"])->name("except.list");
    });

    /** 판매 중 */
    Route::prefix("")->name("")->group(function(){
        /** 전체상품(KOR) */
        Route::get("/list", [ProductController::class, "getPrdList"])->name("list");
        /** 판매제외 상품 리스트 */
        Route::get("/except/list", [ProductController::class, "getPrdExceptList"])->name("getPrdExceptList");
        /** 상품 상세 */
        Route::get("/{offerId}", [ProductController::class, "getPrdDetail"])->name("detail");
        /** 상품 수정 */
        Route::get("/update/{offerId}", [ProductController::class, "update"])->name("update");
        /** 이미지 수정 */
        Route::get("/img/edit/{offerId}", [ProductController::class, "getPrdImageEdit"])->name("imgEdit");
    });


    Route::prefix("w2")->name("w2.")->group(function(){
        /** 상품 수집 관리 */
        Route::get("/queryProductDetail", [ProductW2Controller::class, "queryProductDetail"])->name("queryProductDetail");
        Route::get("/collectLogs", [ProductW2Controller::class, "prdCollectLogs"])->name("prdCollectLogs");

        /** Drop.Hub 상품(ENG) */
        Route::get("/list", [ProductW2Controller::class, "getPrdList"])->name("list");
        /** W2 상품 상세 */
        Route::get("/{offerId}", [ProductW2Controller::class, "getPrdDetail"])->name("detail");
    });

});

/**
 * 카테고리
 */
Route::prefix("category")->name("category.")->group(function(){
    /** 맵핑 관리 */
    Route::get("/", [CategoryController::class, "manage"])->name("list");
    /** 표준 중량(배송비) 관리 */
    Route::get("/weight/list", [CategoryController::class, "weightList"])->name("weightList");
});

/**
 * 금칙어
 */
Route::prefix("forbiddenWord")->name("forbiddenWord.")->group(function(){
    /** 상품정보 관리 */
    Route::get("/list", [ForbiddenWordController::class, "list"])->name("list");
    /** 정보고시 관리 */
    Route::get("/notice/list", [ForbiddenWordController::class, "noticeList"])->name("noticeList");
});

/**
 * 제외 관리
 */
Route::prefix("except")->name("except.")->group(function(){
    /** 정보고시 관리 */
    Route::get("/notice/list", [ExceptController::class, "noticeList"])->name("noticeList");
});

/**
 * 이지셀
 */
Route::prefix("easySell")->name("easySell.")->group(function(){
    /** 상품 현황 */
    Route::get("product/list", [EasySellController::class, "getPrdList"])->name("product/list");

    /** 카테고리 */
    Route::prefix("category")->name("category.")->group(function(){
        /** 카테고리 관리 */
        Route::get("/", [EasySellController::class, "categoryManage"])->name("");
        /** 카테고리 목록 */
        Route::post("/depth", [EasySellController::class, "categoryDepth"])->name("depth");
        Route::post("/info", [EasySellController::class, "categoryInfo"])->name("info");
        Route::post("/mapping", [EasySellController::class, "categoryMapping"])->name("mapping");
    });

});

/**
 * 온채널
 */
Route::prefix("onchannel")->name("onchannel.")->group(function(){
    /** 상품 현황 */
    Route::get("product/list", [OnchannelController::class, "getPrdList"])->name("productList");
});