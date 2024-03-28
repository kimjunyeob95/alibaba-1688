<?php

namespace App\Http\Controllers\Product;

use App\Constants\HttpConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\LogConstant;
use App\Http\Controllers\Controller;
use App\Services\Product\ProductV1;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;

class ProductController extends Controller
{
    private Request $request;
    private ProductV1 $productService;

    function __construct(Request $request, ProductV1 $productService)
    {
        $this->request        = $request;
        $this->productService = $productService;
    }

    public function getPrdList(): View
    {
        $page     = $this->request->post("page", 1);
        $pageSize = $this->request->post("pageSize", 30);
        $offset   = ($page - 1) * $pageSize;

        $params = [
            "page"     => $page,
            "pageSize" => $pageSize,
        ];
        $result = $this->productService->getPrdList($params);
        $viewParams = [
            "datas"         => $result,
            "offset"        => (int) $offset,
            "totalCnt"      => (int) $result->total(),
        ];
        return view("product.prdList")->with($viewParams);
    }

    public function getPrdDetail(int $offerId): View
    {
        $result = $this->productService->getPrdDetail($offerId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "prdObj" => $result["data"]
            ];
        }
        return view("product.prdDetail")->with($viewParams);
    }

    public function keywordQuery(): View
    {
        $search_cls = $this->request->get("search_cls", "productCollectionId");
        $keyword    = $this->request->get("keyword", "");
        $sort       = $this->request->get("sort", "monthSold|desc");
        $page       = $this->request->get("page", 1);
        $pageSize   = $this->request->get("pageSize", 50);
        $offset     = ($page - 1) * $pageSize;
        
        $params = [
            "search_cls" => $search_cls,
            "keyword"    => $keyword,
            "sort"       => $sort,
            "page"       => $page,
            "pageSize"   => $pageSize,
        ];
        $result       = $this->productService->getKeywordQuery($params);
        $datas        = $result["datas"] ?? [];
        $totalRecords = $result["totalRecords"];
        $totalPage    = $result["totalPage"];
        $paginator    = new LengthAwarePaginator(
            collect($datas)->forPage($page, $pageSize), // 현재 페이지의 아이템들
            $totalRecords, // 총 아이템 수
            $pageSize, // 페이지 당 아이템 수
            $page, // 현재 페이지
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // 현재 URL 경로
        );
        $paginator->appends($this->request->query());

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
            "paginator"    => $paginator
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
        if( $imageId ){
            $params = [
                "imageId"    => $imageId,
                "search_cls" => $search_cls,
                "keyword"    => $keyword,
                "sort"       => $sort,
                "page"       => $page,
                "pageSize"   => $pageSize,
            ];
            $result       = $this->productService->getImageQuery($params);
            $datas        = $result["datas"] ?? [];
            $totalRecords = $result["totalRecords"];
            $totalPage    = $result["totalPage"];
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
        ];
        return view("product.prdImageQuery")->with($viewParams);
    }

    public function collectProduct(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offer_ids' => 'required|array',
            ], [
                'offer_ids.required' => 'offer_ids를 입력하세요.',
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $offerIds = $this->request->post("offer_ids");

            $options = "--offerids=" . escapeshellarg(implode(",", $offerIds)) . " --type=" . escapeshellarg(LogConstant::COLLECT_API_KEYWORDQUERY);
            $command = "nohup php artisan save_1688_collect_product " . $options;
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();
            
            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function collectKeywordQuery(): JsonResponse
    {
        try {
            $searchCls = $this->request->post("search_cls", "productCollectionId");
            $keyword   = $this->request->post("keyword", "");
            $sort      = $this->request->post("sort", "monthSold|desc");

            $options = "--search_cls=" . escapeshellarg($searchCls) . " --keyword=" . escapeshellarg($keyword) . " --sort=" . escapeshellarg($sort);
            $command = "php artisan save_1688_product_keyword_query " . $options;
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();
            
            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
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
        $result = $this->productService->getPrdCollectLogList($params);
        $viewParams = [
            "datas"    => $result,
            "offset"   => (int) $offset,
            "totalCnt" => (int) $result->total(),
        ];
        return view("product.prdCollectLogs")->with($viewParams);
    }

    public function createImgId(): JsonResponse
    {
        try {
            if ($this->request->hasFile('imgFile')) {
                $imgFile = $this->request->file('imgFile');

                if (strpos($imgFile->getClientMimeType(), 'image') !== 0) {
                    throw new Exception(ImageErrorMessageConstant::getFitErrorMessage("TYPE"));
                }

                if ($imgFile->getSize() > 300 * 1024) {
                    throw new Exception(ImageErrorMessageConstant::getFitErrorMessage("SIZE"));
                }

                $result = $this->productService->createImgId($imgFile);
            } else {
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("FILE"));
            }

            return helpers_json_response(HttpConstant::OK, $result);
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function collectProductImage(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offer_ids' => 'required|array',
            ], [
                'offer_ids.required' => 'offer_ids를 입력하세요.'
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $offerIds = $this->request->post("offer_ids");

            $options = "--offerids=" . escapeshellarg(implode(",", $offerIds)) . " --type=" . escapeshellarg(LogConstant::COLLECT_API_IMAGEQUERY);
            $command = "php artisan save_1688_collect_product " . $options;
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();
            
            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function collectImageQuery(): JsonResponse
    {
        try {
            $imageId = $this->request->post("imageId");
            $sort    = $this->request->post("sort", "monthSold|desc");

            if( !$imageId ){
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMG_ID"));   
            }

            $options = "--imageid=" . $imageId . " --sort=" . escapeshellarg($sort);
            $command = "php artisan save_1688_product_image_query " . $options;
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();
            
            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function prdCollectLogDetail(int $logId): View
    {
        $result = $this->productService->prdCollectLogDetail($logId);
        if( $result["isSuccess"] == false ){
            abort(404);
        } else {
            $viewParams = [
                "data" => $result["data"]
            ];
        }
        return view("product.prdCollectLogDetail")->with($viewParams);
    }
}
