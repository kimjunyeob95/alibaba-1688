<?php

namespace App\Traits;

use Exception;

trait WProductKeywordTrait
{
    protected string $accessToken;
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg   = helpers_fail_message();
    }

    public function initWProductKeywordTrait(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

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
                $resultData   = $apiDatas["data"]["result"]["result"];
                $datas        = $resultData["data"];
                $totalRecords = $resultData["totalRecords"];
                $totalPage    = $resultData["totalPage"];
                foreach ($datas as &$data) {
                    $ocPrice                 = ocPrice((float)$data["priceInfo"]["price"]);
                    $data["price_1688"]      = (float)$data["priceInfo"]["price"];
                    $data["onch_price"]      = $ocPrice["onch_price"];
                    $data["option_price"]    = $ocPrice["option_price"];
                    $data["cus_price"]       = $ocPrice["cus_price"];
                    $data["recom_cus_price"] = $ocPrice["recom_cus_price"];
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
}
