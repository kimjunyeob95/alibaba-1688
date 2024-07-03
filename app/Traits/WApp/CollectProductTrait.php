<?php

namespace App\Traits\WApp;

use App\Constants\ImageConstant;
use App\Constants\WConstant;
use App\Vo\Product\Product1688ImageDto;
use App\Vo\Product\ProductAddDto;
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

            if( isset($detailProduct["productImage"]["whiteImage"]) ){
                $imageSrc = $detailProduct["productImage"]["whiteImage"];

                $product1688ImageDto = new Product1688ImageDto();
                $product1688ImageDto->bind([
                    "offerId"        => $offerId,
                    "imgType"        => ImageConstant::IMAGE_TYPE_WHITE,
                    "lang"           => WConstant::WAPP_KR,
                    "is_except"      => ImageConstant::IS_EXCEPT_N,
                    "img_url_origin" => $imageSrc,
                    "img_url_trans"  => "",
                    "isChangeImg"    => ImageConstant::IS_CHANGE_IMG,
                    "width"          => 0,
                    "height"         => 0,
                    "byte"           => 0,
                    "mime"           => 0,
                ]);
            }

            $productSkuDtos = [];
            if( isset($detailProduct["productSkuInfos"]) ){
                
            }


            dd($detailProduct);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
