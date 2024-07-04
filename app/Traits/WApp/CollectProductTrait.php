<?php

namespace App\Traits\WApp;

use App\Constants\CollectConstatnt;
use App\Constants\ImageConstant;
use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\ProductAddData;
use App\Models\ProductData;
use App\Models\ProductExtendData;
use App\Models\ProductImageData;
use App\Models\ProductNoticeData;
use App\Models\ProductOptionData;
use App\Models\ProductSaleData;
use App\Models\ProductSkuData;
use App\Vo\Product\Product1688Dto;
use App\Vo\Product\Product1688ExtendDto;
use App\Vo\Product\Product1688ImageDto;
use App\Vo\Product\ProductAddDto;
use App\Vo\Product\ProductSaleDto;
use App\Vo\Product\ProductSkuDto;
use Exception;

trait CollectProductTrait
{
    protected string $accessToken;
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function initCollectProductTrait(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @func getProductAddDto
     * @description '상품 추가 정보 Dto 생성'
     * @param array $detailProduct
     * @param array $detailEnProduct
     * @return array
    */
    public function getProductAddDto(array $detailProduct, array $detailEnProduct = []): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $offerId          = $detailProduct["offerId"];
            $topCategoryId    = 0;
            $secondCategoryId = 0;
            $thirdCategoryId  = 0;
            $mainVideo        = "";
            $detailVideo      = "";
            $minOrderQuantity = 1;
            if( isset($detailProduct["topCategoryId"]) ){
                $topCategoryId = $detailProduct["topCategoryId"];
            }
            if( isset($detailProduct["secondCategoryId"]) ){
                $secondCategoryId = $detailProduct["secondCategoryId"];
            }
            if( isset($detailProduct["thirdCategoryId"]) ){
                $thirdCategoryId = $detailProduct["thirdCategoryId"];
            }
            if( isset($detailProduct["mainVideo"]) ){
                $mainVideo = $detailProduct["mainVideo"];
            }
            if( isset($detailProduct["detailVideo"]) ){
                $detailVideo = $detailProduct["detailVideo"];
            }
            if( isset($detailProduct["minOrderQuantity"]) ){
                $minOrderQuantity = $detailProduct["minOrderQuantity"];
            }

            $productAddDto = new ProductAddDto();
            $productAddDto->bind([
                "offerId"          => $offerId,
                "topCategoryId"    => $topCategoryId,
                "secondCategoryId" => $secondCategoryId,
                "thirdCategoryId"  => $thirdCategoryId,
                "mainVideo"        => $mainVideo,
                "detailVideo"      => $detailVideo,
                "minOrderQuantity" => $minOrderQuantity,
            ]);

            $productWhiteImageDto = new Product1688ImageDto();
            if( isset($detailProduct["productImage"]["whiteImage"]) ){
                $imageSrc = $detailProduct["productImage"]["whiteImage"];
                $productWhiteImageDto->bind([
                    "offerId"        => $offerId,
                    "imgType"        => ImageConstant::IMAGE_TYPE_WHITE,
                    "lang"           => WConstant::WAPP_KR,
                    "is_except"      => ImageConstant::IS_EXCEPT_N,
                    "img_url_origin" => $imageSrc,
                    "img_url_trans"  => "",
                    "isChangeImg"    => ImageConstant::IS_CHANGE_IMG,
                    "width"          => 800,
                    "height"         => 800,
                    "byte"           => 8,
                    "mime"           => "image/jpeg",
                ]);
            }

            $productSkuDtos = [];
            if( isset($detailProduct["productSkuInfos"]) ){
                foreach ($detailProduct["productSkuInfos"] as $sku) {
                    $skuId = $sku["skuId"];

                    $price          = 0;
                    $jxhyPrice      = 0;
                    $pfJxhyPrice    = 0;
                    $consignPrice   = 0;
                    $promotionPrice = 0;

                    if( isset($sku["price"]) ){
                        $price = $sku["price"];
                    }
                    if( isset($sku["jxhyPrice"]) ){
                        $jxhyPrice = $sku["jxhyPrice"];
                    }
                    if( isset($sku["pfJxhyPrice"]) ){
                        $pfJxhyPrice = $sku["pfJxhyPrice"];
                    }
                    if( isset($sku["consignPrice"]) ){
                        $consignPrice = $sku["consignPrice"];
                    }
                    if( isset($sku["promotionPrice"]) ){
                        $promotionPrice = $sku["promotionPrice"];
                    }

                    $productSkuDto = new ProductSkuDto();
                    $productSkuDto->bind([
                        "offerId"        => $offerId,
                        "skuId"          => $skuId,
                        "price"          => (float)$price,
                        "jxhyPrice"      => (float)$jxhyPrice,
                        "pfJxhyPrice"    => (float)$pfJxhyPrice,
                        "consignPrice"   => (float)$consignPrice,
                        "promotionPrice" => (float)$promotionPrice,
                    ]);
                    $productSkuDtos[] = $productSkuDto;
                }
            }

            $productSaleDtos = [];
            if( isset($detailProduct["productSaleInfo"]) ){
                $productSaleInfo = $detailProduct["productSaleInfo"];
                $amountOnSale   = 0;
                $quoteType      = 0;
                $consignPrice   = 0;
                $jxhyPrice      = 0;

                if( isset($productSaleInfo["amountOnSale"]) ){
                    $amountOnSale = $productSaleInfo["amountOnSale"];
                }
                if( isset($productSaleInfo["quoteType"]) ){
                    $quoteType = $productSaleInfo["quoteType"];
                }
                if( isset($productSaleInfo["consignPrice"]) ){
                    $consignPrice = $productSaleInfo["consignPrice"];
                }
                if( isset($productSaleInfo["jxhyPrice"]) ){
                    $jxhyPrice = $productSaleInfo["jxhyPrice"];
                }
                foreach ($productSaleInfo["priceRangeList"] as $saleInfo) {
                    $startQuantity  = 1;
                    $price          = 0;
                    $promotionPrice = 0;

                    if( isset($saleInfo["startQuantity"]) ){
                        $startQuantity = $saleInfo["startQuantity"];
                    }
                    if( isset($saleInfo["price"]) ){
                        $price = $saleInfo["price"];
                    }
                    if( isset($saleInfo["promotionPrice"]) ){
                        $promotionPrice = $saleInfo["promotionPrice"];
                    }

                    $productSaleDto = new ProductSaleDto();
                    $productSaleDto->bind([
                        "offerId"        => $offerId,
                        "amountOnSale"   => $amountOnSale,
                        "startQuantity"  => $startQuantity,
                        "price"          => (float)$price,
                        "quoteType"      => $quoteType,
                        "promotionPrice" => (float)$promotionPrice,
                        "consignPrice"   => (float)$consignPrice,
                        "jxhyPrice"      => (float)$jxhyPrice,
                    ]);
                    $productSaleDtos[] = $productSaleDto;
                }
            }

            $result = [
                "productAddDto"        => $productAddDto,
                "productSkuDtos"       => $productSkuDtos,
                "productSaleDtos"      => $productSaleDtos,
                "productWhiteImageDto" => $productWhiteImageDto,
            ];

            $returnMsg = helpers_success_message($result);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function save1688ProductData(
        Product1688Dto $product1688Dto, Product1688ExtendDto $product1688ExtendDto, array $product1688ImageDtoList,
        array $product1688NoticeDtoList, array $product1688OptionDtoList, ProductAddDto $productAddDto,
        array $productSkuDtos, array $productSaleDtos, Product1688ImageDto $productWhiteImageDto,
        array $collectParams = []): array
    {
        $returnMsg = helpers_fail_message();
        try {
            $nCollectOption   = CollectConstatnt::COLLECT_NONE;
            $nTranslateOption = CollectConstatnt::COLLECT_NONE;
            $yCollectOption   = CollectConstatnt::COLLECT_NONE;
            $yTranslateOption = CollectConstatnt::COLLECT_NONE;

            $collectFlag   = false;
            $translateFlag = false;

            if( isset($collectParams["nCollectOption"]) ){
                $nCollectOption = $collectParams["nCollectOption"];
            }
            if( isset($collectParams["nTranslateOption"]) ){
                $nTranslateOption = $collectParams["nTranslateOption"];
            }
            if( isset($collectParams["yCollectOption"]) ){
                $yCollectOption = $collectParams["yCollectOption"];
            }
            if( isset($collectParams["yTranslateOption"]) ){
                $yTranslateOption = $collectParams["yTranslateOption"];
            }

            $offerId    = (int)$product1688Dto->offer_id;
            $hasProduct = ProductData::where("offer_id", $offerId)->count() > 0 ? true : false;

            if( $hasProduct === false ){
                /** 미수집 상품 */
                if( $nCollectOption != CollectConstatnt::COLLECT_NONE ) {
                    $collectFlag = true;
                }
                if( $nCollectOption != CollectConstatnt::COLLECT_NONE && $nTranslateOption != CollectConstatnt::COLLECT_NONE ){
                    $translateFlag = true;
                }
            } else if( $hasProduct === true ){
                /** 수집 상품 */
                if($yCollectOption != CollectConstatnt::COLLECT_NONE ){
                    $collectFlag = true;
                }

                $prdObj = ProductData::where("offer_id", $offerId)->first();
                if( $yTranslateOption == CollectConstatnt::TRANSLATE_ALL ){
                    $translateFlag = true;
                } else if( $yTranslateOption == CollectConstatnt::TRANSLATE_STATUS_Y && $prdObj->trans_status == ProductConstant::TRANS_STATUS_Y ){
                    $translateFlag = true;
                } else if( $yTranslateOption == CollectConstatnt::TRANSLATE_STATUS_N && $prdObj->trans_status == ProductConstant::TRANS_STATUS_N ){
                    $translateFlag = true;
                }
            }

            if( $collectFlag === true ){
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
                                "lang"     => $product1688ImageDto->lang,
                            ],
                            [
                                "img_url_origin" => $product1688ImageDto->img_url_origin,
                                // "img_url_trans"  => "",
                                // "trans_dated_at" => null
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
                                "lang"           => $product1688ImageDto->lang,
                            ],
                            [
                                // "img_url_trans" => "",
                                // "trans_dated_at" => null
                            ]
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

                $upsertWhere = $productAddDto->getAllProperties();
                unset($upsertWhere["offer_id"]);
                ProductAddData::updateOrCreate(
                    ["offer_id" => $offerId],
                    $upsertWhere
                );
                
                foreach ($productSkuDtos as $productSkuDto) {
                    $upsertWhere = $productSkuDto->getAllProperties();
                    unset($upsertWhere["offer_id"]);
                    unset($upsertWhere["sku_id"]);
                    ProductSkuData::updateOrCreate(
                        [
                            "offer_id" => $offerId,
                            "sku_id"   => $product1688OptionDto->sku_id,
                        ],
                        $upsertWhere
                    );
                }

                ProductSaleData::where("offer_id", $offerId)->forceDelete();
                foreach ($productSaleDtos as $productSaleDto) {
                    $upsertWhere = $productSaleDto->getAllProperties();
                    ProductSaleData::create($upsertWhere);
                }

                if( $productWhiteImageDto->offer_id != 0 ){
                    ProductImageData::updateOrCreate(
                        [
                            "offer_id" => $offerId,
                            "img_type" => ImageConstant::IMAGE_TYPE_WHITE,
                        ],
                        [
                            "lang"           => $productWhiteImageDto->lang,
                            "is_except"      => $productWhiteImageDto->is_except,
                            "img_url_origin" => $productWhiteImageDto->img_url_origin,
                            "img_url_trans"  => $productWhiteImageDto->img_url_trans,
                            "trans_dated_at" => null
                        ]
                    );
                }

                // 6. 기존 이미지 삭제
                $this->delProductImage($product1688ImageDtoList);

                /** 번역상태 변경 */   
                chkTransStatus($offerId);
                            
                /** 중량 여부로 판매 상태 업데이트 */
                upWeightStatus($offerId);
            }

            // 7. 이미지 번역 요청 통신
            if( $product1688Dto->status == ProductConstant::PRD_STATUS_PUBLISH && $translateFlag === true ) {
                $params = [
                    "send_easysell" => MallConstant::AUTO_REGIST_TRUE
                ];
                if( $hasProduct === true ){
                    $transResult = $this->transApiAbstract->createTransProductImgAgain($product1688ImageDtoList, $offerId, false, $params);
                } else {
                    $transResult = $this->transApiAbstract->createTransProductImg($product1688ImageDtoList, $offerId, false, $params);
                }
                if( $transResult["isSuccess"] == false ){
                    throw new Exception("이미지 번역 요청 통신 error: " . $transResult["msg"]);
                }
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function delProductImage(array $product1688ImageDtoList): void
    {
        $mainImg    = "";
        $subImgs    = [];
        $descImgs   = [];
        $mainEnImg  = "";
        $subEnImgs  = [];
        $descEnImgs = [];
        $offerId    = 0;
        $prdObj     = null;
        
        foreach ($product1688ImageDtoList as $product1688ImageDto) {
            $offerId = $product1688ImageDto->offer_id;
            if( $prdObj == null ){
                $prdObj = ProductData::where("offer_id", $offerId)->first();
            }

            if( $product1688ImageDto->lang == WConstant::WAPP_KR ){
                if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                    $mainImg = $product1688ImageDto->img_url_origin;
                }
                if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_SUB ){
                    $subImgs[] = $product1688ImageDto->img_url_origin;
                }
                if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_DESC ){
                    $descImgs[] = $product1688ImageDto->img_url_origin;
                }
            } else if( $product1688ImageDto->lang == WConstant::WAPP_EN ){
                if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                    $mainEnImg = $product1688ImageDto->img_url_origin;
                }
                if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_SUB ){
                    $subEnImgs[] = $product1688ImageDto->img_url_origin;
                }
                if( $product1688ImageDto->img_type == ImageConstant::IMAGE_TYPE_DESC ){
                    $descEnImgs[] = $product1688ImageDto->img_url_origin;
                }
            }
        }

        if( $offerId && $prdObj != null ){
            // 1. 메인 이미지 삭제
            if( $mainImg ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_MAIN)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_KR)
                ->where('img_url_origin', '!=', $mainImg)
                ->delete();
            }
            if( $mainEnImg ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_MAIN)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_EN)
                ->where('img_url_origin', '!=', $mainEnImg)
                ->delete();
            }

            // 2. 서브 이미지 삭제
            if( !empty($subImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_KR)
                ->whereNotIn('img_url_origin', $subImgs)
                ->delete();
            }
            if( !empty($subEnImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_EN)
                ->whereNotIn('img_url_origin', $subEnImgs)
                ->delete();
            }

            /** 중복 이미지도 삭제 */
            foreach ($subImgs as $subImg) {
                $imgObjs = ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_KR)
                ->where('img_url_origin', $subImg)
                ->get();

                if( $imgObjs->count() > 1 ){
                    $firstId = $imgObjs->first()->id;

                    ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                    ->where('offer_id', $offerId)
                    ->where('lang', WConstant::WAPP_KR)
                    ->where('img_url_origin', $subImg)
                    ->where('id', '!=', $firstId)
                    ->delete();
                }
            }
            foreach ($subEnImgs as $subEnImg) {
                $imgObjs = ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_EN)
                ->where('img_url_origin', $subEnImg)
                ->get();

                if( $imgObjs->count() > 1 ){
                    $firstId = $imgObjs->first()->id;

                    ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                    ->where('offer_id', $offerId)
                    ->where('lang', WConstant::WAPP_EN)
                    ->where('img_url_origin', $subEnImg)
                    ->where('id', '!=', $firstId)
                    ->delete();
                }
            }

            // 3. 상세 이미지 삭제
            if( !empty($descImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_KR)
                ->whereNotIn('img_url_origin', $descImgs)
                ->delete();
            }
            if( !empty($descEnImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_EN)
                ->whereNotIn('img_url_origin', $descEnImgs)
                ->delete();
            }
            /** 중복 이미지도 삭제 */
            foreach ($descImgs as $descImg) {
                $imgObjs = ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_KR)
                ->where('img_url_origin', $descImg)
                ->get();

                if( $imgObjs->count() > 1 ){
                    $firstId = $imgObjs->first()->id;

                    ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                    ->where('offer_id', $offerId)
                    ->where('lang', WConstant::WAPP_KR)
                    ->where('img_url_origin', $descImg)
                    ->where('id', '!=', $firstId)
                    ->delete();
                }
            }
            foreach ($descEnImgs as $descEnImg) {
                $imgObjs = ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                ->where('offer_id', $offerId)
                ->where('lang', WConstant::WAPP_EN)
                ->where('img_url_origin', $descEnImg)
                ->get();

                if( $imgObjs->count() > 1 ){
                    $firstId = $imgObjs->first()->id;

                    ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                    ->where('offer_id', $offerId)
                    ->where('lang', WConstant::WAPP_EN)
                    ->where('img_url_origin', $descEnImg)
                    ->where('id', '!=', $firstId)
                    ->delete();
                }
            }
        }
    }
}
