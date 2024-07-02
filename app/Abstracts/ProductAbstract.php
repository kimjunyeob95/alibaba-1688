<?php

namespace App\Abstracts;

use App\Constants\CollectConstatnt;
use App\Constants\Constant1688;
use App\Constants\ImageConstant;
use App\Constants\LogConstant;
use App\Constants\MallConstant;
use App\Constants\ProductConstant;
use App\Constants\WConstant;
use App\Models\ProductData;
use App\Models\ProductExtendData;
use App\Models\ProductImageData;
use App\Models\ProductNoticeData;
use App\Models\ProductOptionData;
use App\Models\WNoticeData;
use App\Traits\WProductSearchTrait;
use App\Vo\Product\Product1688Dto;
use App\Vo\Product\Product1688ExtendDto;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class ProductAbstract
{
    private TransApiAbstract $transApiAbstract;

    use WProductSearchTrait;

    protected array $returnMsg;
    protected string $accessToken;

    public function __construct(TransApiAbstract $transApiAbstract)
    {
        $this->returnMsg        = helpers_fail_message();
        $this->accessToken      = env("1688_ACCESS_TOKEN");
        $this->transApiAbstract = $transApiAbstract;

        $this->initWProductSearchTrait($this->accessToken);
    }


    /**
    * @func getPrdList
    * @description '1688 수집 상품 리스트'
    * @param array $params
    * @return array
    */
    abstract function getPrdList(array $params): array;

    /**
    * @func getPrdExceptList
    * @description '판매제외 상품 리스트'
    * @param array $params
    * @return array
    */
    abstract function getPrdExceptList(array $params): array;

    /**
     * @func apiPrdDetail
     * @description 'w API 상품 상세 조회'
     * @param int $offerId
     * @return array
    */
    abstract function apiPrdDetail(int $offerId): array;

    /**
     * @func getPrdList
     * @description '1688API 수집 상품 디테일'
     * @param int $offerId
     * @return array
    */
    abstract function getPrdDetail(int $offerId): array;

    /**
     * @func getPrdDetailEn
     * @description '1688API 수집 상품 디테일'
     * @param int $offerId
     * @return array
    */
    abstract function getPrdDetailEn(int $offerId): array;

    /**
     * @func getMallCategory
     * @description '1688API 상품상세 endPoint 조회'
     * @param int $offerId '제품ID'
     * @return array
     */
    abstract function getProductData(int $offerId): array;

    /**
     * @func saveMallProductByCategotyId
     * @description '1688API 카테고리ID별 상품수집'
     * @param int $categoryId '카테고리ID'
     * @return void
     */
    abstract function saveMallProductByCategotyId(int $categoryId): void;

    /**
     * @func saveMallProductByImageId
     * @description '1688API 이미지ID로 상품 수집'
     * @param int $string 'imageId'
     * @return void
     */
    abstract function saveMallProductByImageId(string $imageId): void;

    /**
     * @func getQueryProductDetail
     * @description '상품ID로 조회'
     * @param array $offerIds
     * @return array
     */
    abstract function getQueryProductDetail(array $offerIds): array;

    /**
     * @func getKeywordQuery
     * @description '상품 기본 정보로 조회'
     * @param array $params
     * @return array
     */
    abstract function getKeywordQuery(array $params): array;

    /**
     * @func getImageQuery
     * @description '상품 이미지로 조회'
     * @param array $params
     * @return array
     */
    abstract function getImageQuery(array $params): array;

    /**
     * @func getImageQuery
     * @description '상품상세 URL로 수집'
     * @param array $params
     * @return LengthAwarePaginator
     */
    abstract function getUrlQuery(array $params): LengthAwarePaginator;

    /**
     * @func urlQueryDetail
     * @description '상품URL 조회 상세'
     * @param int $searchId
     * @return array
     */
    abstract function urlQueryDetail(int $searchId): array;

    /**
     * @func getPrdCollectLogList
     * @description '상품 수집현황 조회'
     * @param array $params
     * @return LengthAwarePaginator
     */
    abstract function getPrdCollectLogList(array $params): LengthAwarePaginator;

    /**
     * @func saveKeywordQuery
     * @description '1688API keywordQueryAPI로 상품 수집'
     * @param array $params
     * @return array
     */
    abstract function saveKeywordQuery(array $params): array;

    /**
     * @func createImgId
     * @description '1688API 이미지ID 생성'
     * @param UploadedFile $file
     * @return array
     */
    abstract function createImgId(UploadedFile $file): array;

    /**
     * @func saveImageQuery
     * @description '1688API imageQueryAPI로 상품 수집'
     * @param array $params
     * @return array
     */
    abstract function saveImageQuery(array $params): array;

    /**
     * @func prdCollectLogDetail
     * @description '상품 수집 현황 조회'
     * @param int $logId
     * @return array
     */
    abstract function prdCollectLogDetail(int $logId): array;

    /**
     * @func collectProduct
     * @description '1688API 제품ID로 조회 후 DB저장'
     * @param array $offerIds '제품ID'
     * @param string $type '요청 페이지'
     * @return void
     */
    abstract function collectProduct(array $offerIds, string $type = LogConstant::COLLECT_API_KEYWORDQUERY): void;

    /**
     * @func collectProductNotLog
     * @description '1688API 제품ID로 조회 후 DB저장 로그X'
     * @param int $offerId '제품ID'
     * @return array
     */
    abstract function collectProductNotLog(int $offerId): array;

    /**
     * @func productsUpdateImages
     * @description '상품 이미지 업데이트'
     * @param int $offerId
     * @param array $images
     * @return array
     */
    abstract function productsUpdateImages(int $offerId, array $images): array;

    /**
     * @func saveProductSearchData
     * @description '상품상세 URL로 조회 요청'
     * @param array $params
     * @return void
     */
    abstract function saveProductSearchData(array $params): void;

    /**
     * @func urlQueryDel
     * @description '상품상세 URL 수집 데이터 삭제'
     * @param array $ids
     * @return array
    */
    abstract function urlQueryDel(array $ids): array;

    /**
     * @func getPrdImageEdit
     * @description '상품 이미지 수정'
     * @param int $offerId '제품ID'
     * @return array
     */
    abstract function getPrdImageEdit(int $offerId): array;

    /**
     * @func wAppProductMapping
     * @description 'wapp 상품 미맵핑 컬럼 업데이트'
     * @return void
    */
    abstract function wAppProductMapping(): void;

    /**
     * @func imageExcept
     * @description '이미지 수집 제외 처리'
     * @param array $imgIds
     * @param string $is_except
     * @return array
    */
    abstract function imageExcept(array $imgIds, string $is_except): array;

    /**
     * @func imageAccept
     * @description 'AI 이미지 적용'
     * @param array $aiImgIds
     * @return array
    */
    abstract function imageAccept(array $aiImgIds): array;

    /**
     * @func mdPriceUpdate
     * @description 'MD price 수정'
     * @param array $offerIds
     * @param int $mdPrice
     * @return array
    */
    abstract function mdPriceUpdate(array $offerIds, int $mdPrice): array;

    /**
     * @func statusUpdate
     * @description '판매상태 변경'
     * @param array $offerIds
     * @param string $status
     * @return array
    */
    abstract function statusUpdate(array $offerIds, string $status): array;

    /**
     * @func imageMainApply
     * @description '대표 이미지 적용'
     * @param array $aiImgIds
     * @return array
    */
    abstract function imageMainApply(array $aiImgIds): array;

    /**
     * @func gosiExcept
     * @description '고시정보 제외 처리'
     * @param array $gosiList
     * @return array
    */
    abstract function gosiExcept(array $gosiList): array;

    /**
     * @func update
     * @description '상품 update'
     * @param array $params
     * @return array
    */
    abstract function update(array $params): array;

    /**
     * @func inspectStatusUpdate
     * @description '검수상태 update'
     * @param array $params
     * @return array
    */
    abstract function inspectStatusUpdate(array $params): array;
    
    /**
     * @func weightSave
     * @description '상품 중량 저장'
     * @param array $offerIds '제품 ID'
     * @param int $weight '표준 중량'
     * @return array
    */
    abstract function weightSave(array $offerIds, int $weight): array;

    /**
     * @func noticeNameUpdate
     * @description '정보고시 적용 항목명 update'
     * @param array $attributeIds
     * @param string $applyAttributeName
     * @return array
    */
    public function noticeNameUpdate(array $attributeIds, string $applyAttributeName = ""): array
    {
        $returnMsg = $this->returnMsg;
        try {
            WNoticeData::whereIn("attribute_id", $attributeIds)
            ->where("lang", Constant1688::LANGUAGE_KR)
            ->update(["apply_attribute_name" => $applyAttributeName]);

            if( $applyAttributeName == "" ){
                $objs = WNoticeData::whereIn("attribute_id", $attributeIds)
                ->where("lang", Constant1688::LANGUAGE_KR)->groupBy("attribute_id")->get();

                foreach ($objs as $obj) {
                    ProductNoticeData::where("attribute_id", $obj->attribute_id)
                    ->update([
                        "attribute_name_kr" => $obj->attribute_name
                    ]);
                }
            } else {
                ProductNoticeData::whereIn("attribute_id", $attributeIds)
                ->update(["attribute_name_kr" => $applyAttributeName]);
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function save1688ProductData(
        Product1688Dto $product1688Dto, Product1688ExtendDto $product1688ExtendDto, array $product1688ImageDtoList,
        array $product1688NoticeDtoList, array $product1688OptionDtoList, string $aiActive = CollectConstatnt::AI_ACTIVE_FALSE): array
    {
        $returnMsg = helpers_fail_message();
        try {
            $offerId    = (int)$product1688Dto->offer_id;
            $hasProduct = ProductData::where("offer_id", $offerId)->count() > 0 ? true : false;

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

            // 6. 기존 이미지 삭제
            $this->delProductImage($product1688ImageDtoList);

            /** 번역상태 변경 */   
            chkTransStatus($offerId);
                        
            /** 중량 여부로 판매 상태 업데이트 */
            upWeightStatus($offerId);

            // 7. 이미지 번역 요청 통신
            if( $product1688Dto->status == ProductConstant::PRD_STATUS_PUBLISH ) {
                $params = [
                    "send_easysell" => MallConstant::AUTO_REGIST_TRUE
                ];
                if( $aiActive === CollectConstatnt::AI_ACTIVE_TRUE ){
                    $transResult = $this->transApiAbstract->createTransProductImg($product1688ImageDtoList, $offerId, false, $params);
                    if( $transResult["isSuccess"] == false ){
                        throw new Exception("createTransProductImg error: " . $transResult["msg"]);
                    }
                } else if( $aiActive === CollectConstatnt::AI_ACTIVE_FALSE && $hasProduct === true ){
                    // $transResult = $this->transApiAbstract->createTransProductImgAgain($product1688ImageDtoList, $offerId, false, $params);
                    // if( $transResult["isSuccess"] == false ){
                    //     throw new Exception("createTransProductImgAgain error: " . $transResult["msg"]);
                    // }
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
