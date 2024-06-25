<?php

namespace App\Traits;

use App\Constants\CategoryConstant;
use App\Constants\ExceptConstant;
use App\Constants\ForbiddenWordConstant;
use App\Constants\GosiConstants;
use App\Constants\ProductConstant;
use App\Models\CategoryWeightData;
use App\Models\ForbiddenWordData;
use App\Models\ProductExceptData;
use App\Models\ProductNoticeData;
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

            if( isset($apiDatas["data"]["result"]["result"]["data"]) && count($apiDatas["data"]["result"]["result"]["data"]) > 0 ){
                $resultData               = $apiDatas["data"]["result"]["result"];
                $datas                    = $resultData["data"];
                $totalRecords             = $resultData["totalRecords"];
                $totalPage                = $resultData["totalPage"];
                $deletePrdForbiddenWords  = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
                $replacePrdForbiddenWords = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();
                foreach ($datas as &$data) {
                    $ocPrice                 = ocPrice((float)$data["priceInfo"]["price"]);
                    $data["price_1688"]      = (float)$data["priceInfo"]["price"];
                    $data["onch_price"]      = $ocPrice["onch_price"];
                    $data["option_price"]    = $ocPrice["option_price"];
                    $data["cus_price"]       = $ocPrice["cus_price"];
                    $data["recom_cus_price"] = $ocPrice["recom_cus_price"];

                    $subjectTrans = $data["subjectTrans"];
                    // 삭제어
                    $subjectForbiddenTrans = removeForbiddenText($deletePrdForbiddenWords, $subjectTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
                    // 교체어
                    $subjectForbiddenTrans = replaceForbiddenText($replacePrdForbiddenWords, $subjectForbiddenTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);

                    $subjectForbiddenTrans = trim($subjectForbiddenTrans);
                    $subjectForbiddenTrans = removeDuplicateWords($subjectForbiddenTrans);

                    $data["subjectTrans"] = $subjectForbiddenTrans;
                }
            }

            $res = [
                "total_records" => $totalRecords,
                "total_page"    => $totalPage,
                "page_size"     => $params["page_size"],
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
}
