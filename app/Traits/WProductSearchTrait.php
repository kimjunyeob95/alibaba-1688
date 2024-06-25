<?php

namespace App\Traits;

use App\Constants\CategoryConstant;
use App\Constants\ForbiddenWordConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Models\CategoryWeightData;
use App\Models\ForbiddenWordData;
use Illuminate\Http\UploadedFile;
use Exception;

trait WProductSearchTrait
{
    protected string $accessToken;
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg   = helpers_fail_message();
    }

    public function initWProductSearchTrait(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @func searchKeywordQuery
     * @description 'W 상품 키워드 조회'
     * @param array $params
     * @return array
    */
    public function searchKeywordQuery(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";
            $sortArr  = explode("|", $params["sort"]);
            $sort     = [
                $sortArr[0] => $sortArr[1]
            ];
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'keyword'   => $params["keyword"],
                    'sort'      => json_encode($sort),
                    'beginPage' => $params["begin_page"],
                    'pageSize'  => $params["page_size"],
                    'country'   => $params["country"],
                ]
            ];
            if( !empty($params["search_cls"]) && !empty($params["keyword"]) ){
                $payload["offerQueryParam"][$params["search_cls"]] = $params["keyword"];
            }

            $apiDatas = curl_1688("POST", $endPoint, $payload);

            $datas        = [];
            $totalRecords = 0;
            $totalPage    = 0;

            if( !isset($apiDatas["data"]["result"]["result"]["data"]) || count($apiDatas["data"]["result"]["result"]["data"]) < 1 ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY"));
            }

            $resultData               = $apiDatas["data"]["result"]["result"];
            $datas                    = $resultData["data"];
            $totalRecords             = $resultData["totalRecords"];
            $totalPage                = $resultData["totalPage"];
            $deletePrdForbiddenWords  = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
            $replacePrdForbiddenWords = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();
            foreach ($datas as &$data) {
                $ocPrice          = ocPrice((float)$data["priceInfo"]["price"]);
                $data["oc_orice"] = $ocPrice;

                $subjectTrans = $data["subjectTrans"];
                // 삭제어
                $subjectForbiddenTrans = removeForbiddenText($deletePrdForbiddenWords, $subjectTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
                // 교체어
                $subjectForbiddenTrans = replaceForbiddenText($replacePrdForbiddenWords, $subjectForbiddenTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);

                $subjectForbiddenTrans = trim($subjectForbiddenTrans);
                $subjectForbiddenTrans = removeDuplicateWords($subjectForbiddenTrans);

                $data["subjectTrans"] = $subjectForbiddenTrans;
            }

            $res = [
                "total_records" => $totalRecords,
                "total_page"    => $totalPage,
                "page_size"     => (int)$params["page_size"],
                "records"       => $datas,
            ];

            $returnMsg = helpers_success_message($res);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func searchDetail
     * @description 'W 상품 상세 조회'
     * @param int $offerId
     * @param string $country
     * @return array
    */
    public function searchDetail(int $offerId, string $country): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
            $payload        = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'offerId' => $offerId,
                    'country' => $country,
                ]
            ];

            $apiDatas = curl_1688("POST", $endPoint, $payload);

            if( isset($apiDatas["data"]["result"]["result"]) && isset($apiDatas["data"]["result"]["result"]["offerId"]) ){
                $detailProduct = $apiDatas["data"]["result"]["result"];

                $offerId                  = $detailProduct["offerId"];
                $categoryId               = $detailProduct["categoryId"];
                $weights                  = CategoryConstant::WEIGHTS;
                $deletePrdForbiddenWords  = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
                $replacePrdForbiddenWords = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();

                /** 상품명 적용 */
                $subjectTrans = $detailProduct["subjectTrans"];
                // 삭제어
                $subjectForbiddenTrans = removeForbiddenText($deletePrdForbiddenWords, $subjectTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
                // 교체어
                $subjectForbiddenTrans = replaceForbiddenText($replacePrdForbiddenWords, $subjectForbiddenTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);

                $subjectForbiddenTrans = trim($subjectForbiddenTrans);
                $subjectForbiddenTrans = removeDuplicateWords($subjectForbiddenTrans);

                /** 재구매율 적용 */
                $repeatPurchasePercent = 0;
                if( isset($detailProduct["sellerDataInfo"]["repeatPurchasePercent"]) ){
                    $repeatPurchasePercent = $detailProduct["sellerDataInfo"]["repeatPurchasePercent"];
                    // 소수점 첫째 자리에서 반올림
                    $rounded = round($repeatPurchasePercent, 1);
                    // 100을 곱한 후 정수로 변환
                    $repeatPurchasePercent = (int)($rounded * 100);
                }

                /** 상품 상태 적용 */
                $status = $detailProduct["status"];
                if( !isset($detailProduct["productSkuInfos"]) || empty($detailProduct["productSkuInfos"]) ){
                    $status = ProductConstant::PRD_STATUS_MISS;
                }

                /** 옵션가 적용 */
                $productSkuInfos = [];
                if( isset($detailProduct["productSkuInfos"]) ){
                    foreach ($detailProduct["productSkuInfos"] as $key => $prdOptions) {
                        $width  = 0;
                        $length = 0;
                        $height = 0;
                        $weight = 0;
        
                        if( isset($detailProduct["productShippingInfo"]) ){
                            $productShippingInfo = $detailProduct["productShippingInfo"];
                            if( isset($productShippingInfo["skuShippingInfoList"]) ){
                                $skuShippingInfoList = $productShippingInfo["skuShippingInfoList"];
                                foreach ($skuShippingInfoList as $skuShippingInfo) {
                                    if( $skuShippingInfo["skuId"] == $prdOptions["skuId"] ){
                                        if( isset($skuShippingInfo["width"]) ) {
                                            $width = $skuShippingInfo["width"];
                                        }
                                        if( isset($skuShippingInfo["length"]) ) {
                                            $length = $skuShippingInfo["length"];
                                        }
                                        if( isset($skuShippingInfo["height"]) ) {
                                            $height = $skuShippingInfo["height"];
                                        }
                                        if( isset($skuShippingInfo["weight"]) ) {
                                            $weight = (int)ceil($skuShippingInfo["weight"] / 1000);
                                        }
                                    }
                                }
                            } else {
                                if( isset($productShippingInfo["width"]) ) {
                                    $width = $productShippingInfo["width"];
                                }
                                if( isset($productShippingInfo["length"]) ) {
                                    $length = $productShippingInfo["length"];
                                }
                                if( isset($productShippingInfo["height"]) ) {
                                    $height = $productShippingInfo["height"];
                                }
                                if( isset($productShippingInfo["weight"]) ) {
                                    $weight = $productShippingInfo["weight"];
                                }
                            }
                        }

                        $deliveryPrice = ProductConstant::WEIGHT_STATUS_NONE_PRICE;

                        if( $weight > 0 ){
                            $deliveryPrice = $weights[$weight];
                        } else {
                            $cateObj = CategoryWeightData::where("category_id", $categoryId)->first();
                            if( $cateObj != null ){
                                $deliveryPrice = $weights[$cateObj->weight];
                            }
                        }

                        $productSkuInfos[$key]           = $prdOptions;
                        $productSkuInfos[$key]["width"]  = $width;
                        $productSkuInfos[$key]["length"] = $length;
                        $productSkuInfos[$key]["height"] = $height;
                        $productSkuInfos[$key]["weight"] = $weight;
                        $productSkuInfos[$key]["ocPrice"] = ocPrice($prdOptions["price"], $deliveryPrice);
                    }
                }
                $res = [
                    "status"                => $status,
                    "offerId"               => $offerId,
                    "categoryId"            => $categoryId,
                    "subject"               => $detailProduct["subject"],
                    "subjectTrans"          => $subjectForbiddenTrans,
                    "description"           => $detailProduct["description"],
                    "productImage"          => $detailProduct["productImage"],
                    "productAttribute"      => $detailProduct["productAttribute"],
                    "soldOut"               => $detailProduct["soldOut"],
                    "tradeScore"            => $detailProduct["tradeScore"],
                    "repeatPurchasePercent" => $repeatPurchasePercent,
                    "productSkuInfos"       => $productSkuInfos
                ];
                $returnMsg = helpers_success_message($res);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func searchCreateImageId
     * @description 'W 상품 이미지 ID 생성'
     * @param UploadedFile $file
     * @return array
    */
    public function searchCreateImageId(UploadedFile $file): array
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
            
            if( !isset($apiDatas["data"]["result"]["result"]) || !$apiDatas["data"]["result"]["result"] ){
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("W_IMAGE_ID"));
            }

            $res = [
                "img_id" => $apiDatas["data"]["result"]["result"]
            ];
            $returnMsg = helpers_success_message($res);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func searchImageQuery
     * @description 'W 상품 이미지 조회'
     * @param array $params
     * @return array
    */
    public function searchImageQuery(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.imageQuery/";
            $sortArr = explode("|", $params["sort"]);
            $sort = [
                $sortArr[0] => $sortArr[1]
            ];
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'imageId'    => $params["img_id"],
                    'sort'       => json_encode($sort),
                    'beginPage'  => $params["begin_page"],
                    'pageSize'   => $params["page_size"],
                    'country'    => $params["country"],
                ]
            ];

            $apiDatas = curl_1688("POST", $endPoint, $payload);
            $datas        = [];
            $totalRecords = 0;
            $totalPage    = 0;

            if( !isset($apiDatas["data"]["result"]["result"]["data"]) || count($apiDatas["data"]["result"]["result"]["data"]) < 1 ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_IMAGEQUERY"));
            }

            $resultData               = $apiDatas["data"]["result"]["result"];
            $datas                    = $resultData["data"];
            $totalRecords             = $resultData["totalRecords"];
            $totalPage                = $resultData["totalPage"];
            $deletePrdForbiddenWords  = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
            $replacePrdForbiddenWords = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();
            foreach ($datas as &$data) {
                $ocPrice          = ocPrice((float)$data["priceInfo"]["price"]);
                $data["oc_orice"] = $ocPrice;

                $subjectTrans = $data["subjectTrans"];
                // 삭제어
                $subjectForbiddenTrans = removeForbiddenText($deletePrdForbiddenWords, $subjectTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
                // 교체어
                $subjectForbiddenTrans = replaceForbiddenText($replacePrdForbiddenWords, $subjectForbiddenTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);

                $subjectForbiddenTrans = trim($subjectForbiddenTrans);
                $subjectForbiddenTrans = removeDuplicateWords($subjectForbiddenTrans);

                $data["subjectTrans"] = $subjectForbiddenTrans;
            }

            $res = [
                "total_records" => $totalRecords,
                "total_page"    => $totalPage,
                "page_size"     => (int)$params["page_size"],
                "records"       => $datas,
            ];

            $returnMsg = helpers_success_message($res);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func searchRecommend
     * @description 'W 인기상품 조회'
     * @param array $params
     * @return array
    */
    public function searchRecommend(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.offerRecommend/";
            $payload  = [
                'access_token'    => $this->accessToken,
                'recommendOfferParam' => [
                    'beginPage'  => $params["begin_page"],
                    'pageSize'   => $params["page_size"],
                    'country'    => $params["country"],
                ]
            ];

            $apiDatas = curl_1688("POST", $endPoint, $payload);

            if( !isset($apiDatas["data"]["result"]["result"]) || count($apiDatas["data"]["result"]["result"]) < 1 ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_OFFERRECOMMEND"));
            }

            $datas                    = $apiDatas["data"]["result"]["result"];
            $deletePrdForbiddenWords  = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
            $replacePrdForbiddenWords = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();
            foreach ($datas as &$data) {
                $ocPrice          = ocPrice((float)$data["priceInfo"]["price"]);
                $data["oc_orice"] = $ocPrice;

                $subjectTrans = $data["subjectTrans"];
                // 삭제어
                $subjectForbiddenTrans = removeForbiddenText($deletePrdForbiddenWords, $subjectTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
                // 교체어
                $subjectForbiddenTrans = replaceForbiddenText($replacePrdForbiddenWords, $subjectForbiddenTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);

                $subjectForbiddenTrans = trim($subjectForbiddenTrans);
                $subjectForbiddenTrans = removeDuplicateWords($subjectForbiddenTrans);

                $data["subjectTrans"] = $subjectForbiddenTrans;
            }

            $res = [
                "page_size" => (int)$params["page_size"],
                "records"   => $datas,
            ];

            $returnMsg = helpers_success_message($res);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
