<?php

namespace App\Traits;

use App\Constants\ImageErrorMessageConstant;
use App\Constants\ProductErrorMessageConstant;
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

            $resultData   = $apiDatas["data"]["result"]["result"];
            $datas        = $resultData["data"];
            $totalRecords = $resultData["totalRecords"];
            $totalPage    = $resultData["totalPage"];
            foreach ($datas as &$data) {
                $ocPrice          = ocPrice((float)$data["priceInfo"]["price"]);
                $data["oc_orice"] = $ocPrice;
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
            $res = [];
            if( isset($apiDatas["data"]["result"]["result"]) && isset($apiDatas["data"]["result"]["result"]["offerId"]) ){
                $res = $apiDatas["data"]["result"]["result"];
                $returnMsg = helpers_success_message($res);
            } else {
                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
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

            $resultData   = $apiDatas["data"]["result"]["result"];
            $datas        = $resultData["data"];
            $totalRecords = $resultData["totalRecords"];
            $totalPage    = $resultData["totalPage"];
            foreach ($datas as &$data) {
                $ocPrice          = ocPrice((float)$data["priceInfo"]["price"]);
                $data["oc_orice"] = $ocPrice;
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
            $datas = $apiDatas["data"]["result"]["result"];
            foreach ($datas as &$data) {
                $ocPrice          = ocPrice((float)$data["priceInfo"]["price"]);
                $data["oc_orice"] = $ocPrice;
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
