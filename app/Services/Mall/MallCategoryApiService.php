<?php

namespace App\Services\Mall;

use App\Abstracts\MallApiAbstract;

class MallCategoryApiService
{
    protected array $returnMsg;
    private MallApiAbstract $mallApiAbstract;

    public function __construct(MallApiAbstract $mallApiAbstract)
    {
        $this->mallApiAbstract = $mallApiAbstract;
        $this->returnMsg       = helpers_fail_message();
    }

    /**
     * @func categoryMapping
     * @description '카테고리 매핑'
     * @return array
     */
    public function categoryMapping(): array
    {
        return $this->mallApiAbstract->categoryMapping();
    }

    /**
     * @func depth
     * @description '채널 카테고리 단계 조회'
     * @param array $params
     * @return array
     */
    public function channelCateDepth(array $params): array
    {
        return $this->mallApiAbstract->channelCateDepth($params);
    }

    /**
     * @func channelCateList
     * @description '채널 카테고리 목록 조회'
     * @param array $params
     * @return array
    */
    public function channelCateList(array $params): array
    {
        return $this->mallApiAbstract->channelCateList($params);
    }

    /**
     * @func channelMapping
     * @description '채널 카테고리 맵핑'
     * @param array $params
     * @return array
    */
    public function channelMapping(array $params): array
    {
        return $this->mallApiAbstract->channelMapping($params);
    }
}