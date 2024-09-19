<?php

namespace App\Abstracts;

use App\Constants\CategoryErrorMessageConstant;
use App\Constants\Constant1688;
use App\Constants\ForbiddenWordConstant;
use App\Constants\MallConstant;
use App\Models\ChannelCategoryRegistData;
use App\Models\ForbiddenWordData;
use Exception;

abstract class CategoryAbstract
{
    protected array $returnMsg;
    protected string $accessToken;

    public function __construct()
    {
        $this->returnMsg   = helpers_fail_message();
        $this->accessToken = env("1688_ACCESS_TOKEN");
    }

    /**
     * @func getAllCategory
     * @description '수집 한 카테고리를 단계별로 정리한 데이터 목록'
     * @param mixed $parent_cate_id
     * @return array
     */
    abstract function getAllCategory(mixed $parent_cate_id): array;

    /**
     * @func getTreeCategory
     * @description '수집 한 최상위 카테고리 단위를 계층별 목록으로 반환'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
    abstract function getTreeCategory(int $categoryId): array;
    
    /**
     * @func getMallCategory
     * @description '오픈API 카테고리 endPoint 조회'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
    abstract function getMallCategory(int $categoryId): array;

    /**
     * @func getMappingCategory
     * @description '1688<->채널 카테고리 맵핑 조회'
     * @param string $channel
     * @return array
     */
    abstract function getMappingCategory(string $channel): array;

    /**
     * @func save1688AllCategory
     * @description '1688 모든 최상위 카테고리 저장'
     * @return void
     */
    abstract function save1688AllCategory(): void;

    /**
     * @func saveCategory
     * @description '1688API 카테고리 endPoint 조회 후 저장'
     * @return void
     */
    abstract function saveCategory(): void;

    /**
     * @func saveCategoryMapping
     * @description 'categories 테이블의 데이터들을 category_mappings 테이블로 정리'
     * @return void
     */
    abstract function saveCategoryMapping(): void;

    /**
     * @func saveWCategory
     * @description 'w_categories 카테고리 테이블로 insert'
     * @return void
     */
    abstract function saveWCategory(): void;

    /**
     * @func saveWCategoryMapping
     * @description 'categories 테이블의 데이터들을 w_categories 테이블로 정리'
     * @return void
     */
    abstract function saveWCategoryMapping(): void;

    /**
     * @func cateList
     * @description '카테고리 리스트'
     * @param array $params
     * @return array
    */
    abstract function cateList(array $params): array;

    /**
     * @func weightList
     * @description '표준 중량(배송비) 관리'
     * @param array $params
     * @return array
    */
    abstract function weightList(array $params): array;

    /**
     * @func sendMallList
     * @description '전송 카테고리 관리'
     * @param array $params
     * @return array
    */
    abstract function sendMallList(array $params): array;

    /**
     * @func getW
     * @description 'W 카테고리 조회'
     * @param array $params
     * @return array
     */
    abstract function getW(array $params): array;

    /**
     * @func wMapping
     * @description 'W 카테고리 맵핑'
     * @param array $params
     * @return array
     */
    abstract function wMapping(array $params): array;

    /**
     * @func getDepth
     * @description '하위 카테고리 조회'
     * @param int $categoryId '카테고리 ID'
     * @return array
     */
    abstract function getDepth(int $categoryId): array;

    /**
     * @func getWDepth
     * @description 'W 하위 카테고리 조회'
     * @param array $params
     * @return array
     */
    abstract function getWDepth(array $params): array;

    /**
     * @func getInfos
     * @description '카테고리 정보 조회'
     * @param array $categoryIds '카테고리 ID'
     * @return array
     */
    abstract function getInfos(array $categoryIds): array;

    /**
     * @func saveCategoryTree
     * @description 'categories 테이블의 데이터들을 category_trees 테이블로 정리'
     * @return void
    */
    abstract function saveCategoryTree(): void;

    /**
     * @func weightSave
     * @description '카테고리 중량 저장'
     * @param array $categoryIds '카테고리 ID'
     * @param float $weight '표준 중량'
     * @return array
    */
    abstract function weightSave(array $categoryIds, float $weight): array;

    /**
     * @func weightRemove
     * @description '카테고리 중량 삭제'
     * @param array $categoryIds '카테고리 ID'
     * @return array
    */
    abstract function weightRemove(array $categoryIds): array;

    /**
     * @func sendMallUpdate
     * @description '채널별 전송 카테고리 수정'
     * @param array $cateParams '카테고리 정보'
     * @return array
    */
    public function sendMallUpdate(array $cateParams): array
    {
        $returnMsg = $this->returnMsg;
        try {
            
            foreach ($cateParams as $cateParam) {
                $categoryId = $cateParam["categoryId"];
                $ocPublic   = $cateParam["ocPublic"] == "true" ? MallConstant::REGIST_Y : MallConstant::REGIST_N;
                $ocPrivate  = $cateParam["ocPrivate"] == "true" ? MallConstant::REGIST_Y : MallConstant::REGIST_N;
                $esW        = $cateParam["esW"] == "true" ? MallConstant::REGIST_Y : MallConstant::REGIST_N;
                $esDropHub  = $cateParam["esDropHub"] == "true" ? MallConstant::REGIST_Y : MallConstant::REGIST_N;

                $channelList = MallConstant::SEND_CHANNEL_NAME_LIST;
                
                foreach ($channelList as $sendType => $channel) {
                    $isRegist = MallConstant::REGIST_N;

                    switch ($sendType) {
                        case MallConstant::OC_PUBLIC:
                            $isRegist = $ocPublic;
                            break;
                        case MallConstant::OC_PRIVATE:
                            $isRegist = $ocPrivate;
                            break;
                        case MallConstant::EASYSELL_W:
                            $isRegist = $esW;
                            break;
                        case MallConstant::EASYSELL_DROPHUB:
                            $isRegist = $esDropHub;
                            break;
                    }
                    ChannelCategoryRegistData::updateOrCreate(
                        [
                            "category_id" => $categoryId,
                            "channel"     => $channel,
                            "send_type"   => $sendType,
                        ],
                        [
                            "is_regist" => $isRegist,
                        ]
                    );
                }
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func topList
     * @description 'W 카테고리별 인기상품 조회'
     * @param int $categoryId '카테고리 ID'
     * @param string $country '언어'
     * @param int $pageSize '페이징 수'
     * @return array
    */
    public function topList(int $categoryId, string $country, int $pageSize): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.topList.query/";
            $payload = [
                'access_token'     => $this->accessToken,
                'rankQueryParams' => [
                    'rankId'   => $categoryId,
                    'rankType' => Constant1688::RANK_TYPE_HOT,
                    'limit'    => $pageSize,
                    'language' => $country,
                ]
            ];
            $result = curl_1688("post", $endPoint, $payload);
            $res    = [];
            if( isset($result["data"]["result"]["result"]) ){
                $datas = $result["data"]["result"]["result"];
                if (isset($datas["rankProductModels"]) && count($datas["rankProductModels"]) > 0) {
                    foreach ($datas["rankProductModels"] as $data) {
                        if (isset($data["sort"])) {
                            $sortKey = $data["sort"];
                            /** sortKey를 비교 후 해당 순서의 앞에 push */
                            $inserted = false;
                            for ($i = 0; $i < count($res); $i++) {
                                if (isset($res[$i]["sort"]) && $res[$i]["sort"] > $sortKey) {
                                    array_splice($res, $i, 0, [$data]);
                                    $inserted = true;
                                    break;
                                }
                            }
                            if (!$inserted) {
                                $res[] = $data;
                            }
                        } else {
                            /** sort가 없을 시 맨뒤로 이동 */
                            $res[] = $data;
                        }
                    }
                }

                $returnMsg = helpers_success_message($res);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func topKeyword
     * @description 'W 카테고리별 인기검색어 조회'
     * @param int $categoryId '카테고리 ID'
     * @param string $country '언어'
     * @return array
    */
    public function topKeyword(int $categoryId, string $country): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.topKeyword/";
            $payload = [
                'access_token'     => $this->accessToken,
                'topSeKeywordParam' => [
                    'sourceId'       => $categoryId,
                    'hotKeywordType' => Constant1688::HOT_KEYWORD_TYPE,
                    'country'        => $country,
                ]
            ];
            $result = curl_1688("post", $endPoint, $payload);
            if( !isset($result["data"]["result"]["result"]) || count($result["data"]["result"]["result"]) < 1 ){
                throw new Exception(CategoryErrorMessageConstant::getFitErrorMessage("SEARCH_TOPKEYWORD"));
            }

            $datas = $result["data"]["result"]["result"];
            $returnMsg = helpers_success_message($datas);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
