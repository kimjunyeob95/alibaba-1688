<?php

namespace App\Traits;

use App\Constants\MallErrorMessageConstant;
use App\Constants\ProductConstant;
use App\Models\CategoryMapping;
use Exception;

trait MallCategoryTrait
{
    protected array $returnMsg;
    protected string $channel;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function initCategoryTrait(string $channel): void
    {
        $this->channel = $channel;
    }

    /**
     * @func channelCateDepth
     * @description '채널 카테고리 단계 조회'
     * @param array $params
     * @return array
    */
    abstract function channelCateDepth(array $params): array;

    /**
     * @func channelCateList
     * @description '채널 카테고리 목록 조회'
     * @param array $params
     * @return array
    */
    abstract function channelCateList(array $params): array;

    /**
     * @func channelMapping
     * @description '채널 카테고리 맵핑'
     * @param array $params
     * @return array
    */
    public function channelMapping(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $categoryId      = $params["categoryId"];
            $channelCateCode = $params["channelCateCode"];
            $channel         = $this->channel;

            $wAppMappingObj = CategoryMapping::where([
                "mapping_channel" => ProductConstant::MAPPING_WAPP,
                "category_id"     => $categoryId,
            ])->first();

            if( $wAppMappingObj == null ){
                throw new Exception(MallErrorMessageConstant::getFitErrorMessage("W_APP_MAPPINGCODE"));
            }

            if( $channel == ProductConstant::MAPPING_ES_CHANNEL ){
                $channel = ProductConstant::MAPPING_ES_FGN_CHANNEL;
            }

            //category_id 와 매핑된 WAPP 카테고리
            $mappingCode = $wAppMappingObj->mapping_code;

            //WAPP 카테고리와 매핑된 category_id 전체 조회
            $selCategoryObj = CategoryMapping::select("category_id")
                ->where("mapping_channel", ProductConstant::MAPPING_WAPP)
                ->where("mapping_code", $mappingCode)
                ->get();

            foreach($selCategoryObj as $category){
                CategoryMapping::updateOrCreate(
                    [
                        "mapping_channel" => $channel,
                        "category_id"     => $category["category_id"],
                    ],
                    [
                        "mapping_code" => $channelCateCode,
                    ]
                );
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    /**
     * @func categoryMapping
     * @description '카테고리 매핑'
     * @return array
     */
    abstract function categoryMapping(): array;
}
