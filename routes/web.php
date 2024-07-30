<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EasySellController;
use App\Http\Controllers\ExceptController;
use App\Http\Controllers\ForbiddenWordController;
use App\Http\Controllers\GenuioController;
use App\Http\Controllers\OnchannelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductW2Controller;
use App\Http\Controllers\WApp\Admin\AdminController;
use App\Http\Controllers\WApp\Order\OrderController;
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

Route::get("/", [ProductController::class, "queryProductDetail"])->name("/");


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

    /** 상품 관리 */
    Route::prefix("")->name("")->group(function(){
        /** 전체상품 */
        Route::get("/list", [ProductController::class, "getPrdList"])->name("list");
        /** 판매제외 */
        Route::get("/except/list", [ProductController::class, "getPrdExceptList"])->name("getPrdExceptList");
        /** 상품 국문 상세 */
        Route::get("/{offerId}", [ProductController::class, "getPrdDetail"])->name("detail");
        /** 상품 영문 상세 */
        Route::get("/en/{offerId}", [ProductController::class, "getPrdDetailEn"])->name("detailEn");
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
 * WApp
 */
Route::prefix("wapp")->name("wapp.")->group(function(){
    /** 관리자 */
    Route::prefix("admin")->name("admin.")->group(function(){
        /** 로그인 페이지 */
        Route::get("/login", [AdminController::class, "login"])->name("login");
        /** 로그인 처리 */
        Route::post("/signIn", [AdminController::class, "signIn"])->name("signIn");
        /** 로그아웃 처리 */
        Route::get("/logout", [AdminController::class, "logout"])->name("logout");
        /** 회원생성 페이지 */
        Route::get("/regist", [AdminController::class, "registForm"])->name("registForm");
        /** 회원생성 */
        Route::post("/regist", [AdminController::class, "regist"])->name("regist");
    });

    /**
     * 주문
     */
    Route::prefix("order")->name("order.")->group(function(){
        /** WApp 주문 리스트 */
        Route::get("/list", [OrderController::class, "orderList"])->name("list");
        /** W 주문 리스트 */
        Route::get("/w/list", [OrderController::class, "orderWList"])->name("wList");
        /** WApp 주문 수정 */
        Route::get("/edit/{orderId}", [OrderController::class, "orderEdit"])->name("edit");
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
    /** 전송 카테고리 관리 */
    Route::get("/send/mall", [CategoryController::class, "sendMallList"])->name("sendMallList");
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
    Route::get("product/list/{send_type}", [EasySellController::class, "getPrdList"])->name("product/list");

    /** 카테고리 */
    Route::prefix("category")->name("category.")->group(function(){
        /** 카테고리 관리 */
        Route::get("/", [EasySellController::class, "categoryManage"])->name("");
    });

});

/**
 * 온채널
 */
Route::prefix("onchannel")->name("onchannel.")->group(function(){
    /** 상품 현황 */
    Route::get("product/list/{send_type}", [OnchannelController::class, "getPrdList"])->name("productList");

    /** 카테고리 */
    Route::prefix("category")->name("category.")->group(function(){
        /** 카테고리 관리 */
        Route::get("/", [OnchannelController::class, "categoryManage"])->name("");
    });
});

/**
 * SAI
 */
Route::prefix("sai")->name("sai.")->group(function(){
    /** 큐 관리 */
    Route::prefix("queue")->name("queue.")->group(function(){
        /** WApp */
        Route::get("/wapp", [GenuioController::class, "wappQueueList"])->name("wappQueueList");
        /** onchannel */
        Route::get("/onchannel", [GenuioController::class, "onchannelQueueList"])->name("onchannelQueueList");
    });
});