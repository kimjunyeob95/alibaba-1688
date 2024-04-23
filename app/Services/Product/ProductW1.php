<?php

namespace App\Services\Product;

use App\Abstracts\ProductAbstract;
use App\Abstracts\TransApiAbstract;
use App\Abstracts\UploadAbstract;
use App\Constants\Constant1688;
use App\Constants\GenuioConstant;
use App\Constants\ImageConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\LogConstant;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\CategoryMapping;
use App\Models\GenuioImageData;
use App\Models\ProductCollectDetailLog;
use App\Models\ProductCollectLog;
use App\Models\ProductData;
use App\Models\ProductExtendData;
use App\Models\ProductImageData;
use App\Models\ProductImageDetailData;
use App\Models\ProductNoticeData;
use App\Models\ProductOptionData;
use App\Models\ProductSearchData;
use App\Models\ProductSearchDetailData;
use App\Models\WCategory;
use App\Vo\Product\Product1688Dto;
use App\Vo\Product\Product1688ExtendDto;
use App\Vo\Product\Product1688ImageDto;
use App\Vo\Product\Product1688NoticeDto;
use App\Vo\Product\Product1688OptionDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Psr\Log\LogLevel;
use UnexpectedValueException;
use ValueError;

class ProductW1 extends ProductAbstract
{
    private array $returnMsg;
    private string $accessToken;
    private TransApiAbstract $transApiAbstract;
    private UploadAbstract $uploadAbstract;

    public function __construct(TransApiAbstract $transApiAbstract, UploadAbstract $uploadAbstract)
    {
        $this->returnMsg        = helpers_fail_message();
        $this->accessToken      = env("1688_ACCESS_TOKEN");
        $this->transApiAbstract = $transApiAbstract;
        $this->uploadAbstract   = $uploadAbstract;
    }

    public function getPrdList(array $params): array
    {
        $pageSize       = $params["pageSize"];
        $search_cls     = $params["search_cls"];
        $keyword        = $params["keyword"];
        $trans_status   = $params["trans_status"];
        $mapping_status = $params["mapping_status"];
        $sortArr        = explode("|", $params["sort"]);

        $prdBuilder = ProductData::select(["product_datas.*"])->with([
            "main_img",
            "options", 
            "images.ai_all_imgs"
        ]);

        if( !empty($keyword) ){
            if( $search_cls == "offer_id"){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                $prdBuilder->whereIn("product_datas." . $search_cls, $keyword);
            } else if( $search_cls == "prd_name_trans" || $search_cls == "prd_name"){
                $prdBuilder->where("product_datas." . $search_cls, "like", "%" . $keyword . "%");
            } else if( $search_cls == "option_name_trans" || $search_cls == "option_name" ){
                $prdBuilder->whereHas('options', function ($query) use ($keyword, $search_cls) {
                    $query->where($search_cls, 'like', "%" . $keyword . "%");
                });
            }
        }

        if( in_array($sortArr[0], ["option_price", "md_price"])) {
            $maxPriceSubquery = DB::table('product_option_datas');
            if( $sortArr[0] == "option_price" ){
                $maxPriceSubquery->selectRaw('offer_id, MAX(option_price) as max_price');
            } else if( $sortArr[0] == "md_price" ) {
                $maxPriceSubquery->selectRaw('offer_id, MAX(md_price) as max_price');
            }

            $maxPriceSubquery->groupBy('offer_id');
            $prdBuilder->joinSub($maxPriceSubquery, 'max_qry', function ($join) {
                $join->on('product_datas.offer_id', '=', 'max_qry.offer_id');
            });

            $prdBuilder->orderBy('max_qry.max_price', $sortArr[1]);
        } else {
            $prdBuilder->orderBy("product_datas." . $sortArr[0], $sortArr[1]);
        }
        
        if( !empty($trans_status) ){
            $prdBuilder->where("product_datas.trans_status", $trans_status);
        }

        if( !empty($mapping_status) ){
            $prdBuilder->where("product_datas.mapping_status", $mapping_status);
        }

        $totalCnt  = ProductData::count();
        $transYCnt = ProductData::where("trans_status", ProductConstant::TRANS_STATUS_Y)->count();
        $transNCnt = ProductData::where("trans_status", ProductConstant::TRANS_STATUS_N)->count();

        $lists = $prdBuilder->paginate($pageSize)->appends($params);
        return [
            "paginator" => $lists,
            "totalCnt"  => $totalCnt,
            "transYCnt" => $transYCnt,
            "transNCnt" => $transNCnt,
        ];
    }

    public function apiPrdDetail(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductData::with([
                "images.ai_all_imgs",
                "extends",
                "options",
                "notices",
                "category",
                "w_mapping.w_cate_name",
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getPrdCollectLogList(array $params): LengthAwarePaginator
    {
        $pageSize  = $params["pageSize"];

        $prdBuilder = ProductCollectLog::orderBy("created_at", "desc");
        $lists = $prdBuilder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function getPrdDetail(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductData::with([
                "images",
                "extends",
                "options",
                "notices",
                "category",
                "w_mapping.w_cate_name",
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function prdCollectLogDetail(int $logId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductCollectLog::with([
                "details"
            ])->where("id", $logId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getProductData(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
            $payload = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'country' => Constant1688::LANGUAGE_KO,
                    'offerId' => $offerId,
                ]
            ];
            $returnMsg = curl_1688("post", $endPoint, $payload);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
    }

    public function saveMallProductByCategotyId(int $categoryId): void
    {
        $msg = "======================== 실행 시작 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");

        $page     = 1;
        $pageSize = 50;
        try {
            $this->saveMallProductRecursively($categoryId, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (categoryId: {$categoryId}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");
    }

    public function saveMallProductRecursively(int $categoryId, int $page, int $pageSize, int $totalPage = 0): void
    {
        $msg = "start saveMallProductRecursively | page: {$page} | categoryId: {$categoryId}";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");

        $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | categoryId: {$categoryId} | page: {$page}";
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'keyword'    => '',
                    'beginPage'  => $page,
                    'pageSize'   => $pageSize,
                    'country'    => Constant1688::LANGUAGE_KO,
                    'categoryId' => $categoryId,
                ]
            ];
            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != true ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                $successCnt   = 0;
                foreach ($productDatas as $productData) {
                    try {
                        $offerId        = $productData["offerId"];
                        $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                        $payload        = [
                            'access_token'     => $this->accessToken,
                            'offerDetailParam' => [
                                'offerId' => $offerId,
                                'country' => Constant1688::LANGUAGE_KO,
                            ]
                        ];
                        $detailResult = curl_1688("POST", $endPoint, $payload);
                        if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                        }

                        $detailProduct = $detailResult["data"]["result"]["result"];
                        $prdCategoryId = $detailProduct["categoryId"];

                        $prdDto                   = $this->get1688ProductDto($detailResult);
                        $product1688Dto           = $prdDto["product1688Dto"];
                        $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                        $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                        $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                        $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                        $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                        if( $saveResult["isSuccess"] == true ){
                            $successCnt++;
                        }else{
                            throw new Exception($saveResult["msg"]);
                        }
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId} | categoryId: {$categoryId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
                    } catch (UnexpectedValueException $ue) {
                        $msg = $ue->getMessage() . " | page: {$page} | offerId: {$offerId} | categoryId: {$categoryId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
                    }
                }
            } else {
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | page: {$page} | categoryId: {$categoryId}");
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function saveMallProductByImageId(string $imageId): void
    {
        $msg = "======================== 실행 시작 (imageId: {$imageId}) ========================";
        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId");

        $page     = 1;
        $pageSize = 50;
        try {
            $this->saveMallProductByImageIdRecursively($imageId, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (imageId: {$imageId}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 (imageId: {$imageId}) ========================";
        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId");
    }

    public function saveMallProductByImageIdRecursively(string $imageId, int $page, int $pageSize, int $totalPage = 0): void
    {
        $msg = "start saveMallProductByImageIdRecursively | page: {$page} | imageId: {$imageId}";
        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId");

        $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_IMAGEQUERY") . " | imageId: {$imageId} | page: {$page}";
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.imageQuery/";
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'beginPage' => $page,
                    'pageSize'  => $pageSize,
                    'country'   => Constant1688::LANGUAGE_KO,
                    'imageId'   => $imageId,
                ]
            ];

            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != "true" ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                $successCnt   = 0;
                foreach ($productDatas as $productData) {
                    try {
                        $offerId        = $productData["offerId"];
                        $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                        $payload        = [
                            'access_token'     => $this->accessToken,
                            'offerDetailParam' => [
                                'offerId' => $offerId,
                                'country' => Constant1688::LANGUAGE_KO,
                            ]
                        ];
                        $detailResult = curl_1688("POST", $endPoint, $payload);
                        if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                        }

                        $detailProduct = $detailResult["data"]["result"]["result"];
                        $prdCategoryId = $detailProduct["categoryId"];

                        $prdDto                   = $this->get1688ProductDto($detailResult);
                        $product1688Dto           = $prdDto["product1688Dto"];
                        $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                        $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                        $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                        $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                        $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                        if( $saveResult["isSuccess"] == true ){
                            $successCnt++;
                        }else{
                            throw new Exception($saveResult["msg"]);
                        }
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId} | imageId: {$imageId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);
                    } catch (UnexpectedValueException $ue) {
                        $msg = $ue->getMessage() . " | page: {$page} | offerId: {$offerId} | imageId: {$imageId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);
                    }
                }
            } else {
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_IMAGEQUERY") . " | page: {$page} | imageId: {$imageId}");
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductByImageIdRecursively($imageId, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductByImageIdRecursively($imageId, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function collectProduct(array $offerIds, string $type = LogConstant::COLLECT_API_KEYWORDQUERY): void
    {
        $logId = ProductCollectLog::insertGetId([
            "type"       => $type,
            "status"     => LogConstant::COLLECT_RUNNING,
            "payload"    => implode(", ", $offerIds),
            "log_count"  => 0,
            "created_at" => Carbon::now()
        ]);
        $successCnt = 0;
        $failCnt    = 0;
        foreach ($offerIds as $offerId) {
            $offerId = trim($offerId);
            try {
                $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                $payload  = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_KO,
                    ]
                ];
                $detailResult = curl_1688("POST", $endPoint, $payload);
                if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                }

                $prdDto                   = $this->get1688ProductDto($detailResult);
                $product1688Dto           = $prdDto["product1688Dto"];
                $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);
                if( $saveResult["isSuccess"] != true ){
                    throw new Exception($saveResult["msg"]);
                }

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                    "msg"        => ""
                ]);

                $successCnt++;
            } catch (Exception $e) {
                $msg = "offerId: {$offerId} | error: " . $e->getMessage();
                debug_log($msg, "collectProduct/".$type, $type, LogLevel::ERROR);

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_N,
                    "msg"        => $e->getMessage()
                ]);

                $failCnt++;
            }
        }

        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $successCnt + $failCnt,
            "completed_at" => Carbon::now()
        ]);
    }

    public function collectProductImage(array $offerIds): void
    {
        $logId = ProductCollectLog::insertGetId([
            "type"       => LogConstant::COLLECT_API_KEYWORDQUERY,
            "status"     => LogConstant::COLLECT_RUNNING,
            "payload"    => implode(", ", $offerIds),
            "log_count"  => 0,
            "created_at" => Carbon::now()
        ]);
        $successCnt = 0;
        $failCnt    = 0;
        foreach ($offerIds as $offerId) {
            try {
                $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                $payload  = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_KO,
                    ]
                ];
                $detailResult = curl_1688("POST", $endPoint, $payload);
                if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                }

                $prdDto                   = $this->get1688ProductDto($detailResult);
                $product1688Dto           = $prdDto["product1688Dto"];
                $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);
                if( $saveResult["isSuccess"] != true ){
                    throw new Exception($saveResult["msg"]);
                }

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                    "msg"        => ""
                ]);

                $successCnt++;
            } catch (Exception $e) {
                $msg = "offerId: {$offerId} | error: " . $e->getMessage();
                debug_log($msg, "collectProduct", "collectProduct", LogLevel::ERROR);

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_N,
                    "msg"        => $e->getMessage()
                ]);

                $failCnt++;
            }
        }

        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $successCnt + $failCnt,
            "completed_at" => Carbon::now()
        ]);
    }

    public function get1688ProductDto(array $detailResult): array
    {
        $detailProduct = $detailResult["data"]["result"]["result"];
        $offerId       = $detailProduct["offerId"];
        $prdCategoryId = $detailProduct["categoryId"];
        $status        = $detailProduct["status"];

        // 1. 상품 이미지
        $product1688ImageDtoList = [];
        foreach ($detailProduct["productImage"]["images"] as $imgKey => $prdImage) {
            if( $imgKey == 0 ) {
                $imgType = ImageConstant::IMAGE_TYPE_MAIN;
            } else {
                $imgType = ImageConstant::IMAGE_TYPE_SUB;
            }
            $is_except = ImageConstant::IS_EXCEPT_N;
            $imgObj    = ProductImageData::where([
                "offer_id"       => $offerId,
                "img_type"       => $imgType,
                "img_url_origin" => $prdImage,
            ])->first();
            if( $imgObj != null ){
                $is_except = $imgObj->is_except;
            }
            if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                continue;
            }

            $isChangeImg = $this->isChangeImage($offerId, $prdImage, $imgType);
            $imgWidth    = 0;
            $imgHeight   = 0;
            $imgByte     = 0;
            $imgMime     = "";
            if( $isChangeImg == true ){
                $imageInfo = $this->checkImageSize($prdImage);
                $imgWidth  = $imageInfo["width"];
                $imgHeight = $imageInfo["height"];
                $imgByte   = $imageInfo["byte"];
                $imgMime   = $imageInfo["mime"];
            }
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgType,
                "is_except"      => $is_except,
                "img_url_origin" => $prdImage,
                "img_url_trans"  => "",
                "isChangeImg"    => $isChangeImg,
                "width"          => $imgWidth,
                "height"         => $imgHeight,
                "byte"           => $imgByte,
                "mime"           => $imgMime
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }

        // 1-1. 상품 상세 이미지
        $prdDescription = $detailProduct["description"];
        preg_match_all('/<img[^>]+src="([^">]+)"/', $prdDescription, $matches);
        $imageSrcs = $matches[1];
        foreach ($imageSrcs as $imageSrc) {
            $imgType   = ImageConstant::IMAGE_TYPE_DESC;
            $is_except = ImageConstant::IS_EXCEPT_N;
            $imgObj    = ProductImageData::where([
                "offer_id"       => $offerId,
                "img_type"       => $imgType,
                "img_url_origin" => $imageSrc,
            ])->first();
            if( $imgObj != null ){
                $is_except = $imgObj->is_except;
            }
            if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                continue;
            }

            $isChangeImg = $this->isChangeImage($offerId, $imageSrc, $imgType);
            $imgWidth    = 0;
            $imgHeight   = 0;
            $imgByte     = 0;
            $imgMime     = "";
            if( $isChangeImg == true ){
                $imageInfo = $this->checkImageSize($imageSrc);
                $imgWidth  = $imageInfo["width"];
                $imgHeight = $imageInfo["height"];
                $imgByte   = $imageInfo["byte"];
                $imgMime   = $imageInfo["mime"];
            }
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgType,
                "is_except"      => $is_except,
                "img_url_origin" => $imageSrc,
                "img_url_trans"  => "",
                "isChangeImg"    => $isChangeImg,
                "width"          => $imgWidth,
                "height"         => $imgHeight,
                "byte"           => $imgByte,
                "mime"           => $imgMime
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }

        // 2. 상품 기본정보
        $mapping_status = ProductConstant::MAPPING_STATUS_N;
        $getCategoryMappingObj = CategoryMapping::select(["mapping_code"])
        ->where([
            "mapping_channel" => ProductConstant::MAPPING_WAPP,
            "category_id"     => $prdCategoryId,
        ])->first();
        if( $getCategoryMappingObj != null ){
            $mapping_status = ProductConstant::MAPPING_STATUS_Y;
        }

        $startQuantity = $detailProduct["productSaleInfo"]["priceRangeList"][0]["startQuantity"];
        $product1688Dto = new Product1688Dto();
        $product1688Dto->bind([
            "offerId"        => $offerId,
            "categoryId"     => $prdCategoryId,
            "status"         => $status,
            "subject"        => $detailProduct["subject"],
            "subjectTrans"   => $detailProduct["subjectTrans"],
            "startQuantity"  => $startQuantity,
            "description"    => $detailProduct["description"],
            "mapping_status" => $mapping_status,
        ]);

        // 2. 상품 확장정보
        $product1688ExtendDto = new Product1688ExtendDto();
        $product1688ExtendDto->bind([
            "offerId" => $offerId,
        ]);

        // 4. 상품 고시정보
        $product1688NoticeDtoList = [];
        foreach ($detailProduct["productAttribute"] as $prdNotice) {
            $product1688NoticeDto = new Product1688NoticeDto();
            $product1688NoticeDto->bind([
                "offerId"            => $offerId,
                "attributeId"        => $prdNotice["attributeId"],
                "attributeName"      => $prdNotice["attributeName"],
                "value"              => $prdNotice["value"],
                "attributeNameTrans" => $prdNotice["attributeNameTrans"],
                "valueTrans"         => $prdNotice["valueTrans"]
            ]);
            $product1688NoticeDtoList[] = $product1688NoticeDto;
        }

        // 5. 상품 옵션정보
        $product1688OptionDtoList = [];

        $price_1688 = 0;
        // 5-1. price 컬럼이 있을 경우
        if( isset($detailProduct["productSkuInfos"][0]["price"]) ){
            foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                if( $prdOptions["price"] > $price_1688 ){
                    $price_1688 = $prdOptions["price"];
                }
            }
        } else if( !isset($detailProduct["productSkuInfos"][0]["price"]) &&
            isset($detailProduct["productSaleInfo"]["priceRangeList"])
        ) {
            $price_1688 = $detailProduct["productSaleInfo"]["priceRangeList"][0]["price"];
        }

        if( $price_1688 == 0 ){
            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRICE_1688"));
        }

        foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
            $opt_status = ProductConstant::OPTION_SEC_ON_SALE_NUMBER;
            if( $status != ProductConstant::PRD_STATUS ){
                $opt_status = ProductConstant::OPTION_SEC_OUT_OF_STOCK_NUMBER;
            }

            $optionName      = "";
            $optionNameTrans = "";
            foreach ($prdOptions["skuAttributes"] as $prdOption) {
                $optionName      .= $prdOption["value"] .  "_";
                $optionNameTrans .= $prdOption["valueTrans"] .  "_";
            }
            $product1688OptionDto = new Product1688OptionDto();
            $product1688OptionDto->bind([
                "offerId"         => $offerId,
                "skuId"           => $prdOptions["skuId"],
                "specId"          => $prdOptions["specId"],
                "status"          => $opt_status,
                "price_1688"      => $price_1688,
                "optionName"      => rtrim($optionName, "_"),
                "optionNameTrans" => rtrim($optionNameTrans, "_"),
                "amountOnSale"    => $prdOptions["amountOnSale"],
                "cargoNumber"     => $prdOptions["cargoNumber"] ?? "",
            ]);
            $product1688OptionDtoList[] = $product1688OptionDto;
        }

        return [
            "product1688Dto"           => $product1688Dto,
            "product1688ImageDtoList"  => $product1688ImageDtoList,
            "product1688ExtendDto"     => $product1688ExtendDto,
            "product1688NoticeDtoList" => $product1688NoticeDtoList,
            "product1688OptionDtoList" => $product1688OptionDtoList,
        ];
    }

    public function save1688ProductData(
        Product1688Dto $product1688Dto, Product1688ExtendDto $product1688ExtendDto, array $product1688ImageDtoList,
        array $product1688NoticeDtoList, array $product1688OptionDtoList): array
    {
        $returnMsg = helpers_fail_message();
        try {
            $offerId = (int)$product1688Dto->offer_id;

            // 1. product_datas upsert
            $upsertWhere = $product1688Dto->getAllProperties();
            unset($upsertWhere["offer_id"]);
            ProductData::updateOrCreate(
                ["offer_id" => $offerId],
                $upsertWhere
            );

            // 2. product_extend_datas upsert
            $upsertWhere = $product1688ExtendDto->getAllProperties();
            unset($upsertWhere["offer_id"]);
            ProductExtendData::updateOrCreate(
                ["offer_id" => $offerId],
                $upsertWhere
            );

            // 3. product_image_datas, product_image_detail_datas upsert
            foreach ($product1688ImageDtoList as $product1688ImageDto) {
                // 메인 이미지
                if( $product1688ImageDto->is_change_img == true && $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                    ProductImageData::updateOrCreate(
                        [
                            "offer_id" => $offerId,
                            "img_type" => ImageConstant::IMAGE_TYPE_MAIN,
                        ],
                        [
                            "img_url_origin" => $product1688ImageDto->img_url_origin,
                            "img_url_trans"  => "",
                            "trans_dated_at" => null
                        ]
                    );
                }
                // 서브 이미지 or 상세 이미지
                if( $product1688ImageDto->is_change_img == true && $product1688ImageDto->img_type != ImageConstant::IMAGE_TYPE_MAIN ){
                    ProductImageData::updateOrCreate(
                        [
                            "offer_id"       => $offerId,
                            "img_type"       => $product1688ImageDto->img_type,
                            "img_url_origin" => $product1688ImageDto->img_url_origin,
                        ],
                        [
                            "img_url_trans" => "",
                            "trans_dated_at" => null
                        ]
                    );
                }

                $upsertDetailWhere = [];
                if( $product1688ImageDto->is_change_img == true ){
                    $upsertDetailWhere = [
                        "width"  => $product1688ImageDto->width,
                        "height" => $product1688ImageDto->height,
                        "byte"   => $product1688ImageDto->byte,
                        "mime"   => $product1688ImageDto->mime,
                    ];
                }
                if( !empty($upsertDetailWhere) ){
                    ProductImageDetailData::updateOrCreate(
                        [
                            "offer_id"       => $offerId,
                            "img_type"       => $product1688ImageDto->img_type,
                            "img_url_origin" => $product1688ImageDto->img_url_origin,
                        ],
                        $upsertDetailWhere
                    );
                }
            }

            // 4. product_notice_datas upsert
            foreach ($product1688NoticeDtoList as $product1688NoticeDto) {
                $upsertWhere = $product1688NoticeDto->getAllProperties();
                unset($upsertWhere["offer_id"]);
                unset($upsertWhere["attribute_id"]);
                ProductNoticeData::updateOrCreate(
                    [
                        "offer_id"     => $offerId,
                        "attribute_id" => $product1688NoticeDto->attribute_id,
                    ],
                    $upsertWhere
                );
            }

            // 5. product_option_datas upsert
            // 5-1. 우선 전체 품절처리
            ProductOptionData::where("offer_id", $offerId)->update(["status" => ProductConstant::OPTION_SEC_OUT_OF_STOCK_NUMBER]);
            // 5-2. Upsert
            foreach ($product1688OptionDtoList as $product1688OptionDto) {
                $upsertWhere = $product1688OptionDto->getAllProperties();
                unset($upsertWhere["offer_id"]);
                unset($upsertWhere["sku_id"]);
                unset($upsertWhere["spec_id"]);
                ProductOptionData::updateOrCreate(
                    [
                        "offer_id" => $offerId,
                        "sku_id"   => $product1688OptionDto->sku_id,
                        "spec_id"  => $product1688OptionDto->spec_id,
                    ],
                    $upsertWhere
                );
            }

            // 6. 기존 이미지 삭제
            $this->delProductImage($product1688ImageDtoList);

            // 7. 이미지 번역 요청 통신
            if( env("APP_ENV", "local") == "production" ) {
                $transResult = $this->transApiAbstract->createTransProductImg($product1688ImageDtoList, $offerId);
                if( $transResult["isSuccess"] == false ){
                    throw new Exception($transResult["msg"]);
                }
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }
        return $returnMsg;
    }

    public function delProductImage(array $product1688ImageDtoList): void
    {
        $mainImgs = [];
        $subImgs  = [];
        $descImgs = [];
        foreach ($product1688ImageDtoList as $product1688ImageDto) {
            if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                $mainImgs[$product1688ImageDto->offer_id][] = $product1688ImageDto->img_url_origin;
            }
            if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_SUB ){
                $subImgs[$product1688ImageDto->offer_id][] = $product1688ImageDto->img_url_origin;
            }
            if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_DESC ){
                $descImgs[$product1688ImageDto->offer_id][] = $product1688ImageDto->img_url_origin;
            }
        }

        // 1. 메인 이미지 삭제
        foreach ($mainImgs as $offer_id => $mainImg) {
            ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_MAIN)
            ->where('offer_id', $offer_id)
            ->where('is_except', ImageConstant::IS_EXCEPT_N)
            ->where('img_url_origin', '!=', $mainImg)
            ->delete();
        }

        // 2. 서브 이미지 삭제
        foreach ($subImgs as $offer_id => $subImg) {
            ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
            ->where('offer_id', $offer_id)
            ->where('is_except', ImageConstant::IS_EXCEPT_N)
            ->whereNotIn('img_url_origin', $subImg)
            ->delete();
        }

        // 3. 상세 이미지 삭제
        foreach ($descImgs as $offer_id => $descImg) {
            ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
            ->where('offer_id', $offer_id)
            ->where('is_except', ImageConstant::IS_EXCEPT_N)
            ->whereNotIn('img_url_origin', $descImg)
            ->delete();
        }
    }

    public function isChangeImage(int $offerId, string $imagePath, string $imgType): bool
    {
        return true;
        $isChange = false;

        $getOriginImgObj = ProductImageDetailData::where("offer_id", $offerId)
        ->where("img_type", $imgType)
        ->where("img_url_origin", $imagePath)->first();

        if( $getOriginImgObj == null ){
            $isChange = true;
        } else {
            $imageInfo = $this->checkImageSize($imagePath);
            $imgWidth  = $imageInfo["width"];
            $imgHeight = $imageInfo["height"];
            $imgByte   = $imageInfo["byte"];
            $imgMime   = $imageInfo["mime"];

            if( $getOriginImgObj->width != $imgWidth || $getOriginImgObj->height != $imgHeight
            && $getOriginImgObj->byte != $imgByte && $getOriginImgObj->mime != $imgMime){
                $isChange = true;
            }
        }

        return $isChange;
    }

    public function checkImageSize(string $imagePath): array
    {
        // try {
        //     $imageInfo = getimagesize($imagePath);
        // } catch (Exception $e) {
        //     $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_CHECK_IMG_SIZE") . " {$imagePath}" . " | error: " . $e->getMessage();
        //     throw new UnexpectedValueException($errorMsg);
        // }

        // return [
        //     "width"  => $imageInfo[0],
        //     "height" => $imageInfo[1],
        //     "byte"   => $imageInfo["bits"],
        //     "mime"   => $imageInfo["mime"],
        // ];

        return [
            "width"  => 800,
            "height" => 800,
            "byte"   => 8,
            "mime"   => "image/jpeg",
        ];
    }

    public function getQueryProductDetail(array $offerIds): array
    {
        $datas = [];
        foreach ($offerIds as $offerId) {
            try {
                $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                $payload        = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_KO,
                    ]
                ];
                $apiDatas = curl_1688("POST", $endPoint, $payload);
                if( $apiDatas["isSuccess"] == true && isset($apiDatas["data"]["result"]["result"]) ){
                    $detailData               = $apiDatas["data"]["result"]["result"];
                    $price_1688               = getPrice1688($detailData);
                    $detailData["price_1688"] = $price_1688;
                    $datas[]                  = $detailData;
                }
            } catch (Exception $e) {
            }
        }

        foreach ($datas as &$data) {
            $ocPrice                 = ocPrice($data["price_1688"]);
            $data["onch_price"]      = $ocPrice["onch_price"];
            $data["option_price"]    = $ocPrice["option_price"];
            $data["cus_price"]       = $ocPrice["cus_price"];
            $data["recom_cus_price"] = $ocPrice["recom_cus_price"];
            $data["hasPrd"]          = ProductConstant::HAS_PRD_N;
            $prdCnt                  = ProductData::where("offer_id", $data["offerId"])->count();
            if( $prdCnt > 0 ){
                $data["hasPrd"] = ProductConstant::HAS_PRD_Y;
            }
        }

        return $datas;
    }

    public function getKeywordQuery(array $params): array
    {
        $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";
        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $payload = [
            'access_token'    => $this->accessToken,
            'offerQueryParam' => [
                'sort'       => json_encode($sort),
                'beginPage'  => $params["page"],
                'pageSize'   => $params["pageSize"],
                'country'    => Constant1688::LANGUAGE_KO,
            ]
        ];
        if( !empty($params["search_cls"]) && !empty($params["keyword"]) ){
            $payload["offerQueryParam"][$params["search_cls"]] = $params["keyword"];
        }

        $apiDatas = curl_1688("POST", $endPoint, $payload);

        $resultData   = $apiDatas["data"]["result"]["result"];
        $datas        = $resultData["data"] ?? [];
        $totalRecords = $resultData["totalRecords"];
        $totalPage    = $resultData["totalPage"];
        foreach ($datas as &$data) {
            $ocPrice                 = ocPrice((float)$data["priceInfo"]["price"]);
            $data["price_1688"]      = (float)$data["priceInfo"]["price"];
            $data["onch_price"]      = $ocPrice["onch_price"];
            $data["option_price"]    = $ocPrice["option_price"];
            $data["cus_price"]       = $ocPrice["cus_price"];
            $data["recom_cus_price"] = $ocPrice["recom_cus_price"];
            $data["hasPrd"]          = ProductConstant::HAS_PRD_N;
            $prdCnt                  = ProductData::where("offer_id", $data["offerId"])->count();
            if( $prdCnt > 0 ){
                $data["hasPrd"] = ProductConstant::HAS_PRD_Y;
            }
        }

        return [
            "datas"        => $datas,
            "totalRecords" => $totalRecords,
            "totalPage"    => $totalPage,
            "payload"      => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ];
    }

    public function saveKeywordQuery(array $params): array
    {
        $returnMsg = $this->returnMsg;

        $params_json = json_encode($params, JSON_UNESCAPED_UNICODE);
        $msg = "======================== 실행 시작 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll");

        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $page     = $params["page"];
        $pageSize = $params["pageSize"];
        $payload  = [
            'access_token'    => $this->accessToken,
            'offerQueryParam' => [
                'sort'      => json_encode($sort),
                'country'   => Constant1688::LANGUAGE_KO,
            ]
        ];
        if( !empty($params["search_cls"]) && !empty($params["keyword"]) ){
            $payload["offerQueryParam"][$params["search_cls"]] = $params["keyword"];
        }

        try {
            $payload_json = json_encode($payload["offerQueryParam"], JSON_UNESCAPED_UNICODE);
            $logId = ProductCollectLog::insertGetId([
                "type"       => LogConstant::COLLECT_API_KEYWORDQUERY_ALL,
                "status"     => LogConstant::COLLECT_RUNNING,
                "payload"    => $payload_json,
                "log_count"  => 0,
                "created_at" => Carbon::now()
            ]);

            $this->saveKeywordQueryRecursively($logId, $payload, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (params_json: {$params_json}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll", LogLevel::ERROR);
        }

        $log_count = ProductCollectDetailLog::where([
            "log_id" => $logId
        ])->count();
        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $log_count,
            "completed_at" => Carbon::now()
        ]);

        $msg = "======================== 실행 종료 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll");

        return $returnMsg;
    }

    public function saveKeywordQueryRecursively(int $logId, array $payload, int $page, int $pageSize, int $totalPage = 0): void
    {
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";

            $payload["offerQueryParam"]["beginPage"] = $page;
            $payload["offerQueryParam"]["pageSize"]  = $pageSize;

            $payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $errorMsg     = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | payload: {$payload_json}";

            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != true ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                $successCnt   = 0;
                foreach ($productDatas as $productData) {
                    try {
                        $offerId = $productData["offerId"];
                        $prdCnt  = ProductData::where("offer_id", $offerId)->count();
                        if( $prdCnt > 0 ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("ALREADY_PRODUCT"));
                        }

                        $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                        $payload_detail = [
                            'access_token'     => $this->accessToken,
                            'offerDetailParam' => [
                                'offerId' => $offerId,
                                'country' => Constant1688::LANGUAGE_KO,
                            ]
                        ];
                        $detailResult = curl_1688("POST", $endPoint, $payload_detail);
                        if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                        }

                        $prdDto                   = $this->get1688ProductDto($detailResult);
                        $product1688Dto           = $prdDto["product1688Dto"];
                        $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                        $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                        $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                        $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                        $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                        if( $saveResult["isSuccess"] == true ){
                            $successCnt++;
                        }else{
                            throw new Exception($saveResult["msg"]);
                        }

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                            "msg"        => ""
                        ]);
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId}";
                        debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll", LogLevel::ERROR);

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_N,
                            "msg"        => $de->getMessage()
                        ]);
                    }
                }
            } else {
                throw new Exception($errorMsg);
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveKeywordQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveKeywordQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function createImgId(UploadedFile $file): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $filePath      = $file->getRealPath();
            $fileContent   = fileContents($filePath);
            $base64Encoded = base64_encode($fileContent);

            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.image.upload/";
            $payload = [
                'access_token' => $this->accessToken,
                'uploadImageParam' => [
                    "imageBase64" => $base64Encoded
                ]
            ];
            $apiDatas = curl_1688("POST", $endPoint, $payload);

            if( $apiDatas["isSuccess"] != true || $apiDatas["data"]["result"]["success"] != "true" || !isset($apiDatas["data"]["result"]["result"]) ){
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMG_ID"));
            }

            $returnMsg = helpers_success_message($apiDatas["data"]["result"]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getImageQuery(array $params): array
    {
        $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.imageQuery/";
        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $payload = [
            'access_token'    => $this->accessToken,
            'offerQueryParam' => [
                'imageId'    => $params["imageId"],
                'sort'       => json_encode($sort),
                'beginPage'  => $params["page"],
                'pageSize'   => $params["pageSize"],
                'country'    => Constant1688::LANGUAGE_KO,
            ]
        ];
        if( !empty($params["search_cls"]) && !empty($params["keyword"]) ){
            $payload["offerQueryParam"][$params["search_cls"]] = $params["keyword"];
        }

        $apiDatas = curl_1688("POST", $endPoint, $payload);

        $resultData   = $apiDatas["data"]["result"]["result"];
        $datas        = $resultData["data"] ?? [];
        $totalRecords = $resultData["totalRecords"];
        $totalPage    = $resultData["totalPage"];
        foreach ($datas as &$data) {
            $ocPrice                 = ocPrice((float)$data["priceInfo"]["price"]);
            $data["price_1688"]      = (float)$data["priceInfo"]["price"];
            $data["onch_price"]      = $ocPrice["onch_price"];
            $data["option_price"]    = $ocPrice["option_price"];
            $data["cus_price"]       = $ocPrice["cus_price"];
            $data["recom_cus_price"] = $ocPrice["recom_cus_price"];
            $data["hasPrd"]          = ProductConstant::HAS_PRD_N;
            $prdCnt                  = ProductData::where("offer_id", $data["offerId"])->count();
            if( $prdCnt > 0 ){
                $data["hasPrd"] = ProductConstant::HAS_PRD_Y;
            }
        }

        return [
            "datas"        => $datas,
            "totalRecords" => $totalRecords,
            "totalPage"    => $totalPage,
            "payload"      => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ];
    }

    public function saveImageQuery(array $params): array
    {
        $returnMsg = $this->returnMsg;

        $params_json = json_encode($params, JSON_UNESCAPED_UNICODE);
        $msg = "======================== 실행 시작 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll");

        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $page     = $params["page"];
        $pageSize = $params["pageSize"];
        $imageIds = $params["imageIds"];

        try {
            $logId = ProductCollectLog::insertGetId([
                "type"       => LogConstant::COLLECT_API_IMAGEQUERY_ALL,
                "status"     => LogConstant::COLLECT_RUNNING,
                "payload"    => implode(",", $imageIds),
                "log_count"  => 0,
                "created_at" => Carbon::now()
            ]);

            foreach ($imageIds as $imageId) {
                $payload  = [
                    'access_token'    => $this->accessToken,
                    'offerQueryParam' => [
                        'sort'      => json_encode($sort),
                        'country'   => Constant1688::LANGUAGE_KO,
                        'imageId'   => $imageId
                    ]
                ];

                $this->saveImageQueryRecursively($logId, $payload, $page, $pageSize);
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (params_json: {$params_json}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll", LogLevel::ERROR);
        }

        $log_count = ProductCollectDetailLog::where([
            "log_id" => $logId
        ])->count();
        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $log_count,
            "completed_at" => Carbon::now()
        ]);

        $msg = "======================== 실행 종료 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll");

        return $returnMsg;
    }

    public function saveImageQueryRecursively(int $logId, array $payload, int $page, int $pageSize, int $totalPage = 0): void
    {
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.imageQuery/";

            $payload["offerQueryParam"]["beginPage"] = $page;
            $payload["offerQueryParam"]["pageSize"]  = $pageSize;

            $payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $errorMsg     = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_IMAGEQUERY") . " | payload: {$payload_json}";

            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != true ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                $successCnt   = 0;
                foreach ($productDatas as $productData) {
                    try {
                        $offerId = $productData["offerId"];
                        $prdCnt  = ProductData::where("offer_id", $offerId)->count();
                        if( $prdCnt > 0 ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("ALREADY_PRODUCT"));
                        }

                        $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                        $payload_detail = [
                            'access_token'     => $this->accessToken,
                            'offerDetailParam' => [
                                'offerId' => $offerId,
                                'country' => Constant1688::LANGUAGE_KO,
                            ]
                        ];
                        $detailResult = curl_1688("POST", $endPoint, $payload_detail);
                        if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                        }

                        $prdDto                   = $this->get1688ProductDto($detailResult);
                        $product1688Dto           = $prdDto["product1688Dto"];
                        $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                        $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                        $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                        $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                        $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                        if( $saveResult["isSuccess"] == true ){
                            $successCnt++;
                        }else{
                            throw new Exception($saveResult["msg"]);
                        }

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                            "msg"        => ""
                        ]);
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId}";
                        debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll", LogLevel::ERROR);

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_N,
                            "msg"        => $de->getMessage()
                        ]);
                    }
                }
            } else {
                throw new Exception($errorMsg);
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveImageQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveImageQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function getUrlQuery(array $params): LengthAwarePaginator
    {
        $pageSize  = $params["pageSize"];

        $prdBuilder = ProductSearchData::orderBy("created_at", "desc");
        $lists = $prdBuilder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function urlQueryDetail(int $searchId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $searchObjs = ProductSearchData::with(["details"])->where("id", $searchId)->orderBy("created_at", "desc")->first();
            if( $searchObjs != null ){
                foreach ($searchObjs->details as &$detail) {
                    $prdCnt = ProductData::where("offer_id", $detail->offer_id)->count();
                    if( $prdCnt > 0 ){
                        $detail->hasPrd = ProductConstant::HAS_PRD_Y;
                    } else {
                        $detail->hasPrd = ProductConstant::HAS_PRD_N;
                    }
                }
            }
            $returnMsg  = helpers_success_message($searchObjs);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function productsUpdateImages(int $offerId, array $images): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $resultImgs = [];
            $prdObj     = ProductData::where("offer_id", $offerId)->first();

            if( $prdObj == null ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
            }

            $prd_desc = $prdObj->prd_desc;
            $dateName = $prdObj->created_at->format('Y/m/d');

            $descTransImgs = [];
            foreach ($images as $image) {
                $imgId     = $image["id"];
                try {
                    $imgObj = ProductImageData::where([
                        "id"       => $imgId,
                        "offer_id" => $offerId
                    ])->first();
                    if( $imgObj == null ){
                        throw new ValueError(ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGE"));
                    }

                    $img_url_origin = $imgObj->img_url_origin;
                    $mime           = pathinfo($img_url_origin, PATHINFO_EXTENSION);
                    if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                        $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->img_type . "." . $mime;
                    } else {
                        $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->id . "_" . $imgObj->img_type . "." . $mime;
                    }

                    $uploadResult = $this->uploadAbstract->uploadFile($imgName, base64_decode($image["base64"]));

                    if( $uploadResult == true ) {
                        $img_url_trans = env("AWS_URL") . $imgName;
                        ProductImageData::where("id", $imgId)->update([
                            "img_url_trans"  => $img_url_trans,
                            "trans_dated_at" => Carbon::now(),
                        ]);

                        if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_DESC ){
                            $descTransImgs[] = [
                                "img_url_origin" => $img_url_origin,
                                "img_url_trans"  => $img_url_trans
                            ];
                        }
                    } else {
                        throw new ValueError(ImageErrorMessageConstant::getFitErrorMessage("S3_IMG_UPLOAD"));
                    }

                    $resultImgs[] = [
                        "id"        => $imgId,
                        "isSuccess" => true,
                    ];
                } catch (ValueError $ve) {
                    $resultImgs[] = [
                        "id"        => $imgId,
                        "isSuccess" => false,
                        "msg"       => $ve->getMessage()
                    ];
                }
            }

            foreach ($descTransImgs as $descTransImg) {
                $prd_desc_trans = str_replace($descTransImg["img_url_origin"], $descTransImg["img_url_trans"], $prd_desc);
                $prd_desc       = $prd_desc_trans;
                ProductData::where("offer_id", $offerId)->update([
                    "prd_desc_trans" => $prd_desc_trans
                ]);
            }

            $returnMsg = helpers_success_message($resultImgs);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function saveProductSearchData(array $params): void
    {
        $offerIds     = $params["offerIds"];
        $search_title = $params["search_title"];
        $search_type  = $params["search_type"];

        $searchId = ProductSearchData::insertGetId([
            "search_title" => $search_title,
            "search_type"  => $search_type,
            "status"       => ProductConstant::SEARCH_STATUS_R,
            "search_count" => count($offerIds),
            "created_at"   => Carbon::now(),
        ]);

        foreach ($offerIds as $offerId) {
            $offerId        = trim($offerId);
            $prdCategoryId  = 0;
            $prd_name_trans = "";
            $price_1688     = 0;
            $prd_image      = "";
            $sold_out       = 0;

            try {
                $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                $payload  = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_KO,
                    ]
                ];
                $detailResult = curl_1688("POST", $endPoint, $payload);
                if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                }

                $detailProduct = $detailResult["data"]["result"]["result"];
                $prdCategoryId = $detailProduct["categoryId"];
                $prd_name_trans = $detailProduct["subjectTrans"];
                if( isset($detailProduct["productSkuInfos"][0]["price"]) ){
                    foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                        if( $prdOptions["price"] > $price_1688 ){
                            $price_1688 = $prdOptions["price"];
                        }
                    }
                } else if( !isset($detailProduct["productSkuInfos"][0]["price"]) &&
                    isset($detailProduct["productSaleInfo"]["priceRangeList"])
                ) {
                    $price_1688 = $detailProduct["productSaleInfo"]["priceRangeList"][0]["price"];
                }
                if( $price_1688 == 0 ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRICE_1688"));
                }

                if( count($detailProduct["productImage"]["images"]) < 5 ) {
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_MAIN_IMG"));
                }
                $prd_image = $detailProduct["productImage"]["images"][4];

                if( isset($detailProduct["soldOut"]) ){
                    $sold_out = (int)$detailProduct["soldOut"];
                }

                ProductSearchDetailData::create([
                    "search_id"      => $searchId,
                    "offer_id"       => $offerId,
                    "category_id"    => $prdCategoryId,
                    "prd_name_trans" => $prd_name_trans,
                    "price_1688"     => $price_1688,
                    "prd_image"      => $prd_image,
                    "sold_out"       => $sold_out,
                    "is_search"      => ProductConstant::IS_SEARCH_Y,
                    "msg"            => ""
                ]);

            } catch (Exception $e) {
                ProductSearchDetailData::create([
                    "search_id"      => $searchId,
                    "offer_id"       => $offerId,
                    "category_id"    => $prdCategoryId,
                    "prd_name_trans" => $prd_name_trans,
                    "price_1688"     => $price_1688,
                    "prd_image"      => $prd_image,
                    "sold_out"       => $sold_out,
                    "is_search"      => ProductConstant::IS_SEARCH_N,
                    "msg"            => $e->getMessage()
                ]);
            }
        }

        ProductSearchData::where("id", $searchId)->update([
            "status"       => ProductConstant::SEARCH_STATUS_C,
            "completed_at" => Carbon::now()
        ]);
    }

    public function urlQueryDel(array $ids): array
    {
        $returnMsg = $this->returnMsg;

        try {
            foreach ($ids as $id) {
                ProductSearchData::where("id", $id)->forceDelete();
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function getPrdImageEdit(int $offerId):array
    {
        $returnMsg = $this->returnMsg;

        try {
            $imgObjs = ProductImageData::where("offer_id", $offerId)
            ->where("img_url_trans", "!=", "")->get();
            foreach ($imgObjs as $imgObj) {
                $gObj = GenuioImageData::where([
                    "offer_id" => $offerId,
                    "img_id"   => $imgObj->id,
                    "ai_type"  => GenuioConstant::IMG_Ai_TRANS,
                ])->count();
                if( $gObj < 1 ){
                    GenuioImageData::insert([
                        [
                            "offer_id"   => $offerId,
                            "img_id"     => $imgObj->id,
                            "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                            "is_origin"  => GenuioConstant::IS_ORIGIN_Y,
                            "img_url_ai" => $imgObj->img_url_origin,
                            "created_at" => $imgObj->created_at,
                        ],
                        [
                            "offer_id"   => $offerId,
                            "img_id"     => $imgObj->id,
                            "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                            "is_origin"  => GenuioConstant::IS_ORIGIN_N,
                            "img_url_ai" => $imgObj->img_url_trans,
                            "created_at" => $imgObj->trans_dated_at,
                        ],
                    ]);
                }
            }

            $prdObj = ProductData::with([
                "main_img.ai_origin_img",
                "main_img.ai_imgs",
                "sub_imgs.ai_origin_img",
                "sub_imgs.ai_imgs",
                "desc_imgs.ai_origin_img",
                "desc_imgs.ai_imgs",
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }
            // dd($prdObj->toArray());
            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function wAppProductMapping(): void
    {
        $msg = "======================== 실행 시작 ========================";
        debug_log($msg, "wAppProductMapping", "wAppProductMapping");

        try {
            //1. category_mappings upsert
            $wObjs = WCategory::where("category_id", "!=", 0)->get();
            foreach ($wObjs as $wObj) {
                $category_id = $wObj->category_id;
                $upsertWhere = [
                    "mapping_chaneel" => ProductConstant::MAPPING_WAPP,
                    "mapping_code"    => $wObj->mapping_code,
                ];
                CategoryMapping::updateOrCreate(
                    ["category_id" => $category_id],
                    $upsertWhere
                );
            }

            $prdObjs = ProductData::where()->get();
            foreach ($prdObjs as $prdObj) {
                $mapping_status = ProductConstant::MAPPING_STATUS_N;
                $cateObj = CategoryMapping::where([
                    "mapping_channel" => ProductConstant::MAPPING_WAPP,
                    "category_id"     => $prdObj->category_id,
                ])->first();

                if( $cateObj != null ){
                    $mapping_status = ProductConstant::MAPPING_STATUS_Y;
                }

                ProductData::where("id", $prdObj->id)->update([
                    "mapping_status" => $mapping_status
                ]);
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "wAppProductMapping", "wAppProductMapping", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 ========================";
        debug_log($msg, "wAppProductMapping", "wAppProductMapping");
    }

    public function imageExcept(array $imgIds, string $is_except): array
    {
        $returnMsg = $this->returnMsg;
        try {
            ProductImageData::whereIn("id", $imgIds)->update([
                "is_except" => $is_except
            ]);

            foreach ($imgIds as $imgId) {
                $imgObj = ProductImageData::where("id", $imgId)->first();
                if( $imgObj != null ){
                    // 상세이미지 업데이트
                    upPrdDescTrans($imgObj->offer_id);
                }
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function imageAccept(array $aiImgIds): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($aiImgIds as $aiImgId) {
                $aiImgObj = GenuioImageData::with(["image"])->where("id", $aiImgId)->first();
                if( $aiImgObj != null && $aiImgObj->image ){
                    $imgObj = $aiImgObj->image;
                    if( $imgObj->img_url_trans ){
                        ProductImageData::where("id", $imgObj->id)->update([
                            "img_url_trans" => $aiImgObj->img_url_ai
                        ]);

                        // 상세이미지 업데이트
                        upPrdDescTrans($imgObj->offer_id);
                    }
                }
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }

    public function mdPriceUpdate(array $offerIds, int $mdPrice): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($offerIds as $offerId) {
                ProductOptionData::where("offer_id", $offerId)->update([
                    "md_price" => $mdPrice
                ]);
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message(false, $e->getMessage());
        }

        return $returnMsg;
    }
}