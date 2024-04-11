<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\HttpConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\LogConstant;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Http\Controllers\Controller;
use App\Services\Service1688Product;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Process\Process;

class WProductController extends Controller
{
    private Request $request;
    private Service1688Product $service1688Product;

    function __construct(Request $request, Service1688Product $service1688Product)
    {
        $this->request            = $request;
        $this->service1688Product = $service1688Product;
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
            $log_type = $this->request->post("log_type", LogConstant::COLLECT_API_KEYWORDQUERY);

            $options = "--offerids=" . escapeshellarg(implode(",", $offerIds)) . " --type=" . escapeshellarg($log_type);
            $command = "nohup php artisan save_1688_collect_product " . $options . " > /dev/null 2>&1 &";
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

            if( $keyword == "" ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_KEYWORD"));
            }

            $options = "--search_cls=" . escapeshellarg($searchCls) . " --keyword=" . escapeshellarg($keyword) . " --sort=" . escapeshellarg($sort);
            $command = "nohup php artisan save_1688_product_keyword_query " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();
            
            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function createImgId(): JsonResponse
    {
        $returnArr = [
            "result" => []
        ];
        try {
            if ($this->request->hasFile('imgFile')) {
                $imgFiles = $this->request->file('imgFile');
                foreach ($imgFiles as $imgFile) {
                    if (strpos($imgFile->getClientMimeType(), 'image') !== 0) {
                        throw new Exception(ImageErrorMessageConstant::getFitErrorMessage("TYPE"));
                    }
    
                    if ($imgFile->getSize() > 300 * 1024) {
                        throw new Exception(ImageErrorMessageConstant::getFitErrorMessage("SIZE"));
                    }
    
                    $result = $this->service1688Product->createImgId($imgFile);
                    if( isset($result["data"]["result"] )){
                        $returnArr["result"][] = $result["data"]["result"];
                    }
                }
                $returnArr["result"] = implode(",", $returnArr["result"]);
            } else {
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("FILE"));
            }

            return helpers_json_response(HttpConstant::OK, $returnArr);
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
            $command = "nohup php artisan save_1688_collect_product " . $options . " > /dev/null 2>&1 &";
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
            $imageIds = $this->request->post("imageIds");
            $sort     = $this->request->post("sort", "monthSold|desc");
            if( !$imageIds ){
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMG_ID"));   
            }

            $options = "--imageIds=" . $imageIds . " --sort=" . escapeshellarg($sort);
            $command = "nohup php artisan save_1688_product_image_query " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function collectProductUrl(): JsonResponse
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
            
            $options = "--offerids=" . escapeshellarg(implode(",", $offerIds)) . " --type=" . escapeshellarg(LogConstant::COLLECT_API_URLQUERY);
            $command = "nohup php artisan save_1688_collect_product " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function getProductData(int $offerId): JsonResponse
    {
        $result = $this->service1688Product->getProductData($offerId);
        if( $result["isSuccess"] == true ){
            return helpers_json_response(HttpConstant::OK, $result);
        } else {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
        }
    }

    public function apiPrdList(): JsonResponse
    {
        try {
            $page           = $this->request->get("page", 1);
            $pageSize       = $this->request->get("pageSize", 50);
            if( $pageSize > 50 ) $pageSize = 50;
            $search_cls     = $this->request->get("search_cls", "prd_name_trans");
            $keyword        = $this->request->get("keyword", "");
            $trans_status   = $this->request->get("trans_status", ProductConstant::TRANS_STATUS_Y);
            
            $params = [
                "page"         => $page,
                "pageSize"     => $pageSize,
                "search_cls"   => $search_cls,
                "keyword"      => $keyword,
                "trans_status" => $trans_status,
            ];
            $result = $this->service1688Product->apiPrdList($params);

            return helpers_json_response(HttpConstant::OK, helpers_success_message($result));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function productsUpdateImages(int $offerId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'images'          => 'required|array',
                'images.*.id'     => 'required|int',
                'images.*.base64' => 'required|string',
            ], [
                'images.required'          => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES"),
                'images.*.id.required'     => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES_ID"),
                'images.*.base64.required' => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES_BASE64")
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $images = $this->request->post("images");
            $result = $this->service1688Product->productsUpdateImages($offerId, $images);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function productSearchData(): JsonResponse
    {
        try {
            $keyword      = $this->request->post("keyword", "");
            $search_title = $this->request->post("search_title", "");
            $search_type  = ProductConstant::SEARCH_TYPE_URL;

            if( $keyword == "" ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_KEYWORD"));
            }

            if( $search_title == "" ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("SEARCH_TITLE"));
            }

            $urls = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
            $urls = explode(",", $urls);
            // 각 배열 요소의 앞뒤 공백 제거
            $urls = array_map('trim', $urls);
            // 빈 값을 제거
            $urls = array_filter($urls);
            // 중복 제거
            $urls = array_unique($urls);

            $offerIds = [];
            foreach ($urls as $url) {
                if (preg_match("/offer\/(\d+)\.html/", $url, $matches)) {
                    $offerIds[] = $matches[1];
                }
            }
            $offerIds = implode(",", $offerIds);

            $options = "--offerIds=" . escapeshellarg($offerIds) . " --search_title=" . escapeshellarg($search_title) . " --search_type=" . escapeshellarg($search_type);
            $command = "nohup php artisan save_product_search_data " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();
            
            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "조회 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
