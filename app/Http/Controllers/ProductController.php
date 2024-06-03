<?php

namespace App\Http\Controllers;

use App\Constants\InspectConstant;
use App\Constants\ProductConstant;
use App\Http\Controllers\Controller;
use App\Services\Service1688Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class ProductController extends Controller
{
    private Request $request;
    private Service1688Product $service1688Product;

    function __construct(Request $request, Service1688Product $service1688Product)
    {
        $this->request            = $request;
        $this->service1688Product = $service1688Product;
    }

    public function getPrdList(): View
    {
        $page                = $this->request->post("page", 1);
        $pageSize            = $this->request->post("pageSize", 50);
        $search_cls          = $this->request->get("search_cls", "offer_id");
        $w_type              = $this->request->get("w_type", "");
        $keyword             = $this->request->get("keyword", "");
        $trans_status        = $this->request->get("trans_status", "");
        $mapping_status      = $this->request->get("mapping_status", "");
        $prd_status          = $this->request->get("prd_status", "");
        $mdPrice_status      = $this->request->get("mdPrice_status", "");
        $weight_status       = $this->request->get("weight_status", "");
        $sort                = $this->request->get("sort", "updated_at|desc");
        $inspect_status      = $this->request->get("inspect_status", "");
        $inspect_img_status  = $this->request->get("inspect_img_status", "");
        $inspect_prd_status  = $this->request->get("inspect_prd_status", "");
        $inspect_gosi_status = $this->request->get("inspect_gosi_status", "");
        $cate_first          = $this->request->get("cate_first", "");
        $cate_second         = $this->request->get("cate_second", "");
        $cate_third          = $this->request->get("cate_third", "");
        $offset              = ($page - 1) * $pageSize;

        $params = [
            "page"                => $page,
            "pageSize"            => $pageSize,
            "search_cls"          => $search_cls,
            "inspect_status"      => $inspect_status,
            "w_type"              => $w_type,
            "keyword"             => $keyword,
            "trans_status"        => $trans_status,
            "mapping_status"      => $mapping_status,
            "prd_status"          => $prd_status,
            "mdPrice_status"      => $mdPrice_status,
            "weight_status"       => $weight_status,
            "sort"                => $sort,
            "inspect_img_status"  => $inspect_img_status,
            "inspect_prd_status"  => $inspect_prd_status,
            "inspect_gosi_status" => $inspect_gosi_status,
            "cate_first"          => $cate_first,
            "cate_second"         => $cate_second,
            "cate_third"          => $cate_third
        ];
        $result = $this->service1688Product->getPrdList($params);

        $viewParams = [
            "datas"               => $result["paginator"],
            "transYCnt"           => $result["transYCnt"],
            "transNCnt"           => $result["transNCnt"],
            "totalCnt"            => $result["totalCnt"],
            "inspectYCnt"         => $result["inspectYCnt"],
            "inspectNCnt"         => $result["inspectNCnt"],
            "imgInspectYCnt"      => $result["imgInspectYCnt"],
            "imgInspectNCnt"      => $result["imgInspectNCnt"],
            "prdInspectYCnt"      => $result["prdInspectYCnt"],
            "prdInspectNCnt"      => $result["prdInspectNCnt"],
            "gosiInspectYCnt"     => $result["gosiInspectYCnt"],
            "gosiInspectNCnt"     => $result["gosiInspectNCnt"],
            "offset"              => (int) $offset,
            "pageSize"            => (int) $pageSize,
            "search_cls"          => $search_cls,
            "w_type"              => $w_type,
            "keyword"             => $keyword,
            "trans_status"        => $trans_status,
            "mapping_status"      => $mapping_status,
            "weight_status"       => $weight_status,
            "prd_status"          => $prd_status,
            "mdPrice_status"      => $mdPrice_status,
            "inspect_status"      => $inspect_status,
            "inspect_img_status"  => $inspect_img_status,
            "inspect_prd_status"  => $inspect_prd_status,
            "inspect_gosi_status" => $inspect_gosi_status,
            "sort"                => $sort,
            "firstCateObjs"       => $result["firstCateObjs"],
            "secondCateObjs"      => $result["secondCateObjs"],
            "thirdCateObjs"       => $result["thirdCateObjs"],
            "cate_first"          => $cate_first,
            "cate_second"         => $cate_second,
            "cate_third"          => $cate_third
        ];

        return view("product.prdList")->with($viewParams);
    }

    public function getPrdExceptList(): View
    {
        $page                = $this->request->post("page", 1);
        $pageSize            = $this->request->post("pageSize", 50);
        $search_cls          = $this->request->get("search_cls", "offer_id");
        $w_type              = $this->request->get("w_type", "");
        $keyword             = $this->request->get("keyword", "");
        $trans_status        = $this->request->get("trans_status", "");
        $mapping_status      = $this->request->get("mapping_status", "");
        $mdPrice_status      = $this->request->get("mdPrice_status", "");
        $sort                = $this->request->get("sort", "updated_at|desc");
        $prd_status          = $this->request->get("prd_status", ProductConstant::PRD_STATUS_EXCEPT);
        $inspect_status      = $this->request->get("inspect_status", "");
        $inspect_img_status  = $this->request->get("inspect_img_status", "");
        $inspect_prd_status  = $this->request->get("inspect_prd_status", "");
        $inspect_gosi_status = $this->request->get("inspect_gosi_status", "");
        $cate_first          = $this->request->get("cate_first", "");
        $cate_second         = $this->request->get("cate_second", "");
        $cate_third          = $this->request->get("cate_third", "");
        $offset              = ($page - 1) * $pageSize;

        $params = [
            "page"                => $page,
            "pageSize"            => $pageSize,
            "search_cls"          => $search_cls,
            "w_type"              => $w_type,
            "keyword"             => $keyword,
            "trans_status"        => $trans_status,
            "mapping_status"      => $mapping_status,
            "prd_status"          => $prd_status,
            "mdPrice_status"      => $mdPrice_status,
            "sort"                => $sort,
            "inspect_status"      => $inspect_status,
            "inspect_img_status"  => $inspect_img_status,
            "inspect_prd_status"  => $inspect_prd_status,
            "inspect_gosi_status" => $inspect_gosi_status,
            "cate_first"          => $cate_first,
            "cate_second"         => $cate_second,
            "cate_third"          => $cate_third
        ];
        $result = $this->service1688Product->getPrdExceptList($params);

        $viewParams = [
            "datas"               => $result["paginator"],
            "transYCnt"           => $result["transYCnt"],
            "transNCnt"           => $result["transNCnt"],
            "totalCnt"            => $result["totalCnt"],
            "inspectYCnt"         => $result["inspectYCnt"],
            "inspectNCnt"         => $result["inspectNCnt"],
            "imgInspectYCnt"      => $result["imgInspectYCnt"],
            "imgInspectNCnt"      => $result["imgInspectNCnt"],
            "prdInspectYCnt"      => $result["prdInspectYCnt"],
            "prdInspectNCnt"      => $result["prdInspectNCnt"],
            "gosiInspectYCnt"     => $result["gosiInspectYCnt"],
            "gosiInspectNCnt"     => $result["gosiInspectNCnt"],
            "offset"              => (int) $offset,
            "pageSize"            => (int) $pageSize,
            "search_cls"          => $search_cls,
            "w_type"              => $w_type,
            "keyword"             => $keyword,
            "prd_status"          => $prd_status,
            "trans_status"        => $trans_status,
            "mapping_status"      => $mapping_status,
            "mdPrice_status"      => $mdPrice_status,
            "inspect_status"      => $inspect_status,
            "inspect_img_status"  => $inspect_img_status,
            "inspect_prd_status"  => $inspect_prd_status,
            "inspect_gosi_status" => $inspect_gosi_status,
            "sort"                => $sort,
            "firstCateObjs"       => $result["firstCateObjs"],
            "secondCateObjs"      => $result["secondCateObjs"],
            "thirdCateObjs"       => $result["thirdCateObjs"],
            "cate_first"          => $cate_first,
            "cate_second"         => $cate_second,
            "cate_third"          => $cate_third
        ];

        return view("product.prdExceptList")->with($viewParams);
    }

    public function getPrdDetail(int $offerId): View
    {
        $result = $this->service1688Product->getPrdDetail($offerId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "prdObj" => $result["data"]
            ];
        }
        return view("product.prdDetail")->with($viewParams);
    }

    public function getPrdDetailEn(int $offerId): View
    {
        $result = $this->service1688Product->getPrdDetailEn($offerId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "prdObj" => $result["data"]
            ];
        }
        return view("product.prdDetailEn")->with($viewParams);
    }

    public function queryProductDetail(): View
    {
        $keyword = $this->request->get("keyword", "");
        $datas   = [];

        if( $keyword ){
            $offerIds = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
            $offerIds = explode(",", $offerIds);
            // 각 배열 요소의 앞뒤 공백 제거
            $offerIds = array_map('trim', $offerIds);
            // 빈 값을 제거
            $offerIds = array_filter($offerIds);
            // 중복 제거
            $offerIds = array_unique($offerIds);

            $result = $this->service1688Product->getQueryProductDetail($offerIds);
            $datas  = $result;
        }

        $viewParams = [
            "datas"        => $datas,
            "totalRecords" => count($datas),
            "keyword"      => $keyword,
        ];

        return view("product.prdQueryProductDetail")->with($viewParams);
    }

    public function keywordQuery(): View
    {
        $search_cls = $this->request->get("search_cls", "productCollectionId");
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "monthSold|desc");
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        $offset     = ($page - 1) * $pageSize;

        $datas        = [];
        $totalRecords = 0;
        $totalPage    = 0;
        $paginator    = null;
        $payload      = "";
        if( $keyword ){
            $params = [
                "search_cls" => $search_cls,
                "keyword"    => $keyword,
                "sort"       => $sort,
                "page"       => $page,
                "pageSize"   => $pageSize,
            ];
            $result       = $this->service1688Product->getKeywordQuery($params);
            $datas        = $result["datas"] ?? [];
            $totalRecords = $result["totalRecords"];
            $totalPage    = $result["totalPage"];
            $payload      = $result["payload"];
            $paginator    = new LengthAwarePaginator(
                collect($datas)->forPage($page, $pageSize), // 현재 페이지의 아이템들
                $totalRecords, // 총 아이템 수
                $pageSize, // 페이지 당 아이템 수
                $page, // 현재 페이지
                ['path' => LengthAwarePaginator::resolveCurrentPath()] // 현재 URL 경로
            );
            $paginator->appends($this->request->query());
        }

        $viewParams = [
            "datas"        => $datas,
            "totalRecords" => $totalRecords,
            "totalPage"    => $totalPage,
            "offset"       => $offset,
            "search_cls"   => $search_cls,
            "keyword"      => $keyword,
            "sort"         => $sort,
            "page"         => $page,
            "pageSize"     => $pageSize,
            "paginator"    => $paginator,
            "payload"      => $payload,
        ];
        return view("product.prdKeywordQuery")->with($viewParams);
    }

    public function imageQuery(): View
    {
        $search_cls = $this->request->get("search_cls", "");
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "monthSold|desc");
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        $imageId    = $this->request->get("imageId", "");
        $offset     = ($page - 1) * $pageSize;

        $datas        = [];
        $totalRecords = 0;
        $totalPage    = 0;
        $paginator    = null;
        $payload      = "";
        if( $imageId ){
            $params = [
                "imageId"    => $imageId,
                "search_cls" => $search_cls,
                "keyword"    => $keyword,
                "sort"       => $sort,
                "page"       => $page,
                "pageSize"   => $pageSize,
            ];
            $result       = $this->service1688Product->getImageQuery($params);
            $datas        = $result["datas"] ?? [];
            $totalRecords = $result["totalRecords"];
            $totalPage    = $result["totalPage"];
            $payload      = $result["payload"];
            $paginator    = new LengthAwarePaginator(
                collect($datas)->forPage($page, $pageSize), // 현재 페이지의 아이템들
                $totalRecords, // 총 아이템 수
                $pageSize, // 페이지 당 아이템 수
                $page, // 현재 페이지
                ['path' => LengthAwarePaginator::resolveCurrentPath()] // 현재 URL 경로
            );
            $paginator->appends($this->request->query());
        }

        $viewParams = [
            "datas"        => $datas,
            "totalRecords" => $totalRecords,
            "totalPage"    => $totalPage,
            "offset"       => $offset,
            "search_cls"   => $search_cls,
            "keyword"      => $keyword,
            "sort"         => $sort,
            "page"         => $page,
            "pageSize"     => $pageSize,
            "paginator"    => $paginator,
            "imageId"      => $imageId,
            "payload"      => $payload,
        ];
        return view("product.prdImageQuery")->with($viewParams);
    }

    public function imageMultiQuery(): View
    {
        $search_cls = $this->request->get("search_cls", "");
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "monthSold|desc");
        $imageId    = $this->request->get("imageId", "");

        $viewParams = [
            "search_cls"   => $search_cls,
            "keyword"      => $keyword,
            "sort"         => $sort,
            "imageId"      => $imageId
        ];
        return view("product.prdImageMultiQuery")->with($viewParams);
    }

    public function urlQuery(): View
    {
        $page           = $this->request->post("page", 1);
        $pageSize       = $this->request->post("pageSize", 50);
        $offset         = ($page - 1) * $pageSize;

        $params = [
            "page"         => $page,
            "pageSize"     => $pageSize,
        ];
        $result = $this->service1688Product->getUrlQuery($params);

        $viewParams = [
            "datas"    => $result,
            "offset"   => (int) $offset,
            "totalCnt" => (int) $result->total(),
        ];
        return view("product.prdUrlQuery")->with($viewParams);
    }

    public function urlQueryDetail(int $searchId): View
    {
        $result = $this->service1688Product->urlQueryDetail($searchId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "obj" => $result["data"]
            ];
        }
        return view("product.prdUrlQueryDetail")->with($viewParams);
    }

    public function prdCollectLogs(): View
    {
        $page     = $this->request->get("page", 1);
        $pageSize = $this->request->get("pageSize", 100);
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"     => $page,
            "pageSize" => $pageSize,
        ];
        $result = $this->service1688Product->getPrdCollectLogList($params);
        $viewParams = [
            "datas"    => $result,
            "offset"   => (int) $offset,
            "totalCnt" => (int) $result->total(),
        ];
        return view("product.prdCollectLogs")->with($viewParams);
    }

    public function prdCollectLogDetail(int $logId): View
    {
        $result = $this->service1688Product->prdCollectLogDetail($logId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "data" => $result["data"]
            ];
        }
        return view("product.prdCollectLogDetail")->with($viewParams);
    }

    public function update(int $offerId): View
    {
        $result = $this->service1688Product->getPrdDetail($offerId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "prdObj" => $result["data"]
            ];
        }
        return view("product.update")->with($viewParams);
    }

    public function getPrdImageEdit(int $offerId): View
    {
        $result = $this->service1688Product->getPrdImageEdit($offerId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "prdObj"  => $result["data"],
                "offerId" => $offerId
            ];
        }

        return view("product.prdImageEdit")->with($viewParams);
    }

    public function queryW2ProductDetail(): View
    {
        $keyword = $this->request->get("keyword", "");
        $datas   = [];

        if( $keyword ){
            $offerIds = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
            $offerIds = explode(",", $offerIds);
            // 각 배열 요소의 앞뒤 공백 제거
            $offerIds = array_map('trim', $offerIds);
            // 빈 값을 제거
            $offerIds = array_filter($offerIds);
            // 중복 제거
            $offerIds = array_unique($offerIds);

            $result = $this->service1688Product->getQueryProductDetailW2($offerIds);
            $datas  = $result;
        }
        $viewParams = [
            "datas"        => $datas,
            "totalRecords" => count($datas),
            "keyword"      => $keyword,
        ];

        return view("product.prdQueryW2ProductDetail")->with($viewParams);
    }
}
