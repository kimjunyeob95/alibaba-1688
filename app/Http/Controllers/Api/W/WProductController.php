<?php

namespace App\Http\Controllers\Api\W;

use App\Constants\CollectConstatnt;
use App\Constants\Constant1688;
use App\Constants\HttpConstant;
use App\Constants\ImageConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\LogConstant;
use App\Constants\OptionConstants;
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
    private string $phpAlias;

    function __construct(Request $request, Service1688Product $service1688Product)
    {
        $this->request            = $request;
        $this->service1688Product = $service1688Product;
        $this->phpAlias           = env("PHP_ALIAS", "php80");
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
            $log_type = $this->request->post("log_type", LogConstant::COLLECT_API_OFFERID);
            $aiActive = $this->request->post("ai_active", CollectConstatnt::AI_ACTIVE_FALSE);

            $options = "--offerids=" . helperEscape(implode(",", $offerIds)) . " --type=" . helperEscape($log_type) . " --aiactive=" . helperEscape($aiActive);
            $command = "nohup " . $this->phpAlias . " artisan save_1688_collect_product " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function reCollectProduct(): JsonResponse
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
            $log_type = $this->request->post("log_type", LogConstant::RE_COLLECT_API_OFFERID);

            $options = "--offerids=" . helperEscape(implode(",", $offerIds)) . " --type=" . helperEscape($log_type);
            $command = "nohup " . $this->phpAlias . " artisan save_1688_collect_product " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "재 수집 요청 완료"));
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
            $aiActive  = $this->request->post("ai_active", CollectConstatnt::AI_ACTIVE_FALSE);

            if( $keyword == "" ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_KEYWORD"));
            }

            $options = "--search_cls=" . helperEscape($searchCls) . " --keyword=" . helperEscape($keyword) . " --sort=" . helperEscape($sort) . " --aiactive=" . helperEscape($aiActive);
            $command = "nohup " . $this->phpAlias . " artisan save_1688_product_keyword_query " . $options . " > /dev/null 2>&1 &";
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
            $aiActive = $this->request->post("ai_active", CollectConstatnt::AI_ACTIVE_FALSE);
            
            $options = "--offerids=" . helperEscape(implode(",", $offerIds)) . " --type=" . helperEscape(LogConstant::COLLECT_API_IMAGEQUERY) . " --aiactive=" . helperEscape($aiActive);
            $command = "nohup " . $this->phpAlias . " artisan save_1688_collect_product " . $options . " > /dev/null 2>&1 &";
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
            $aiActive = $this->request->post("ai_active", CollectConstatnt::AI_ACTIVE_FALSE);
            if( !$imageIds ){
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMG_ID"));   
            }

            $options = "--imageIds=" . $imageIds . " --sort=" . helperEscape($sort) . " --aiactive=" . helperEscape($aiActive);
            $command = "nohup " . $this->phpAlias . " artisan save_1688_product_image_query " . $options . " > /dev/null 2>&1 &";
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
            
            $options = "--offerids=" . helperEscape(implode(",", $offerIds)) . " --type=" . helperEscape(LogConstant::COLLECT_API_URLQUERY);
            $command = "nohup " . $this->phpAlias . " artisan save_1688_collect_product " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();

            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "수집 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function urlQueryDel(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'ids' => 'required|array',
            ], [
                'ids.required' => 'ids를 입력하세요.'
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
    
            $ids = $this->request->post("ids");
            $result = $this->service1688Product->urlQueryDel($ids);

            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
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
            $search_cls     = $this->request->get("search_cls", "prd_name_kr");
            $w_type         = $this->request->get("w_type", "");
            $keyword        = $this->request->get("keyword", "");
            $trans_status   = $this->request->get("trans_status", ProductConstant::TRANS_STATUS_Y);
            $prd_status     = $this->request->get("prd_status", "");
            $mdPrice_status = $this->request->get("mdPrice_status", "");
            $sort           = $this->request->get("sort", "updated_at|desc");
            
            $params = [
                "page"           => $page,
                "pageSize"       => $pageSize,
                "search_cls"     => $search_cls,
                "w_type"         => $w_type,
                "keyword"        => $keyword,
                "trans_status"   => $trans_status,
                "prd_status"     => $prd_status,
                "mdPrice_status" => $mdPrice_status,
                "mapping_status" => ProductConstant::MAPPING_STATUS_Y,
                "sort"           => $sort,
            ];
            $result = $this->service1688Product->apiPrdList($params);

            return helpers_json_response(HttpConstant::OK, helpers_success_message($result));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function apiPrdDetail(int $offerId): JsonResponse
    {
        try {
            $result = $this->service1688Product->apiPrdDetail($offerId);

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

            $options = "--offerIds=" . helperEscape($offerIds) . " --search_title=" . helperEscape($search_title) . " --search_type=" . helperEscape($search_type);
            $command = "nohup " . $this->phpAlias . " artisan save_product_search_data " . $options . " > /dev/null 2>&1 &";
            $process = Process::fromShellCommandline($command);
            $process->setWorkingDirectory(env("WORK_DIRECTORY", "/web1/1688"));
            $process->setTimeout(null); // 실행 시간 제한 없음
            $process->start();
            
            return helpers_json_response(HttpConstant::OK, helpers_success_message([], "조회 요청 완료"));
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imageExcept(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'imgIds' => 'required|array',
            ], [
                'imgIds.required' => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES_ID"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $imgIds    = $this->request->post("imgIds");
            $is_except = $this->request->post("is_except", ImageConstant::IS_EXCEPT_N);
            $result    = $this->service1688Product->imageExcept($imgIds, $is_except);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imageAccept(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'aiImgIds' => 'required|array',
            ], [
                'aiImgIds.required' => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES_AI_ID"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $aiImgIds = $this->request->post("aiImgIds");
            $result   = $this->service1688Product->imageAccept($aiImgIds);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function mdPriceUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offerIds' => 'required|array',
                'mdPrice'  => 'required|int',
            ], [
                'offerIds.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("OFFER_IDS"),
                'mdPrice.required'  => ProductErrorMessageConstant::getNotHaveErrorMessage("MD_PRICE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $offerIds = $this->request->post("offerIds");
            $mdPrice  = $this->request->post("mdPrice");
            $result   = $this->service1688Product->mdPriceUpdate($offerIds, $mdPrice);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function statusUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offerIds' => 'required|array',
                'status'   => 'required|string',
            ], [
                'offerIds.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("OFFER_IDS"),
                'status.required'   => ProductErrorMessageConstant::getNotHaveErrorMessage("STATUS"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $offerIds = $this->request->post("offerIds");
            $status   = $this->request->post("status");
            $result   = $this->service1688Product->statusUpdate($offerIds, $status);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function imageMainApply(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'aiImgIds' => 'required|array',
            ], [
                'aiImgIds.required' => ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGES_AI_ID"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $aiImgIds = $this->request->post("aiImgIds");
            $result   = $this->service1688Product->imageMainApply($aiImgIds);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function gosiExcept(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'gosiList' => 'required|array',
            ], [
                'gosiList.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("GOSILIST"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $gosiList = $this->request->post("gosiList");
            $result   = $this->service1688Product->gosiExcept($gosiList);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function update(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offer_id'               => 'required|int',
                'prd_name_kr'            => 'required|string',
                'prd_name_en'            => 'required|string',
                'optionList'             => 'required|array',
                'optionList.*.id'        => 'required|int',
                'optionList.*.is_except' => 'required|string',
                'gosiKrList'             => 'required|array',
                'gosiKrList.*.id'        => 'required|int',
                'gosiKrList.*.is_except' => 'required|string',
                'gosiEnList'             => 'required|array',
                'gosiEnList.*.id'        => 'required|int',
                'gosiEnList.*.is_except' => 'required|string',
            ], [
                'offer_id.required'                    => ProductErrorMessageConstant::getNotHaveErrorMessage("OFFER_ID"),
                'prd_name_kr.required'                 => ProductErrorMessageConstant::getNotHaveErrorMessage("PRD_NAME_KR"),
                'prd_name_en.required'                 => ProductErrorMessageConstant::getNotHaveErrorMessage("PRD_NAME_EN"),
                'optionList.required'                  => ProductErrorMessageConstant::getNotHaveErrorMessage("OPTIONLIST"),
                'optionList.*.id.required'             => ProductErrorMessageConstant::getNotHaveErrorMessage("OPTIONLIST_ID"),
                'optionList.*.option_name_kr.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("OPTIONLIST_OPTION_NAME_KR"),
                'optionList.*.option_name_en.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("OPTIONLIST_OPTION_NAME_EN"),
                'optionList.*.is_except.required'      => ProductErrorMessageConstant::getNotHaveErrorMessage("OPTIONLIST_IS_EXCEPT"),
                'gosiKrList.required'                  => ProductErrorMessageConstant::getNotHaveErrorMessage("GOSIKRLIST"),
                'gosiKrList.*.id.required'             => ProductErrorMessageConstant::getNotHaveErrorMessage("GOSIKRLIST_ID"),
                'gosiKrList.*.is_except.required'      => ProductErrorMessageConstant::getNotHaveErrorMessage("GOSIKRLIST_IS_EXCEPT"),
                'gosiEnList.required'                  => ProductErrorMessageConstant::getNotHaveErrorMessage("GOSIENLIST"),
                'gosiEnList.*.id.required'             => ProductErrorMessageConstant::getNotHaveErrorMessage("GOSIENLIST_ID"),
                'gosiEnList.*.is_except.required'      => ProductErrorMessageConstant::getNotHaveErrorMessage("GOSIENLIST_IS_EXCEPT"),
            ]);

            foreach ($this->request->post('optionList') as $key => $option) {
                if ($option['is_except'] == OptionConstants::IS_EXCEPT_N) {
                    $validator->sometimes('optionList.' . $key . '.option_name_kr', 'required|string', function () {
                        return true;
                    });
                    $validator->sometimes('optionList.' . $key . '.option_name_en', 'required|string', function () {
                        return true;
                    });
                }
            }

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result   = $this->service1688Product->updateW1($this->request->all());
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function inspectStatusUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offerIds'            => 'required|array',
                'inspect_img_status'  => 'required|string',
                'inspect_prd_status'  => 'required|string',
                'inspect_gosi_status' => 'required|string',
            ], [
                'offerIds.required'            => ProductErrorMessageConstant::getNotHaveErrorMessage("OFFER_IDS"),
                'inspect_img_status.required'  => ProductErrorMessageConstant::getNotHaveErrorMessage("INSPECT_IMG_STATUS"),
                'inspect_prd_status.required'  => ProductErrorMessageConstant::getNotHaveErrorMessage("INSPECT_PRD_STATUS"),
                'inspect_gosi_status.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("INSPECT_GOSI_STATUS"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $result   = $this->service1688Product->inspectStatusUpdate($this->request->all());
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function weightSave(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'offerIds' => 'required|array',
                'weight'   => 'required|int',
            ], [
                'offerIds.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("OFFER_IDS"),
                'weight.required'   => ProductErrorMessageConstant::getNotHaveErrorMessage("WEIGHT"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $offerIds = $this->request->post("offerIds");
            $weight   = $this->request->post("weight");
            $result   = $this->service1688Product->weightSave($offerIds, $weight);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function noticeNameUpdate(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'attribute_ids' => 'required|array',
            ], [
                'attribute_ids.required' => ProductErrorMessageConstant::getNotHaveErrorMessage("ATTRIBUTE_IDS"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $attributeIds       = $this->request->post("attribute_ids");
            $applyAttributeName = $this->request->post("apply_attribute_name", "") ?? "";
            $result             = $this->service1688Product->noticeNameUpdate($attributeIds, $applyAttributeName);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function searchKeywordQuery(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'keyword'    => 'required|string',
                'begin_page' => 'required|string',
                'page_size'  => 'required|string',
                'sort'       => 'required|string',
                'country'    => 'required|string',
            ], [
                "keyword.required"    => ProductErrorMessageConstant::getNotHaveErrorMessage("KEYWORD"),
                "begin_page.required" => ProductErrorMessageConstant::getNotHaveErrorMessage("BEGINPAGE"),
                "page_size.required"  => ProductErrorMessageConstant::getNotHaveErrorMessage("PAGESIZE"),
                "sort.required"       => ProductErrorMessageConstant::getNotHaveErrorMessage("SORT"),
                "country.required"    => ProductErrorMessageConstant::getNotHaveErrorMessage("COUNTRY"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
    
            $params = [
                "keyword"    => $this->request->get("keyword"),
                "begin_page" => $this->request->get("begin_page"),
                "page_size"  => $this->request->get("page_size") > 50 ? 50 : $this->request->get("page_size"),
                "sort"       => $this->request->get("sort"),
                "country"    => $this->request->get("country"),
            ];
    
            $result = $this->service1688Product->searchKeywordQuery($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function searchDetail(int $offerId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'country' => 'required|string',
            ], [
                "country.required" => ProductErrorMessageConstant::getNotHaveErrorMessage("COUNTRY"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $country = $this->request->get("country", Constant1688::LANGUAGE_KO);
            $result = $this->service1688Product->searchDetail($offerId, $country);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function searchCreateImageId(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'img_file' => 'required|file',
            ], [
                "img_file.required" => ProductErrorMessageConstant::getNotHaveErrorMessage("IMG_FILE"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $imgFile = $this->request->file('img_file');
            if (strpos($imgFile->getClientMimeType(), 'image') !== 0) {
                throw new Exception(ImageErrorMessageConstant::getFitErrorMessage("TYPE"));
            }
            if ($imgFile->getSize() > 300 * 1024) {
                throw new Exception(ImageErrorMessageConstant::getFitErrorMessage("SIZE"));
            }

            $result = $this->service1688Product->searchCreateImageId($imgFile);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function searchCreateImageIdByUrl(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'img_url' => 'required|string',
            ], [
                "img_url.required" => ProductErrorMessageConstant::getNotHaveErrorMessage("IMG_URL"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $imgUrl = $this->request->post('img_url');
            $result = $this->service1688Product->searchCreateImageIdByUrl($imgUrl);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function searchImageQuery(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'img_id'     => 'required|int',
                'begin_page' => 'required|int',
                'page_size'  => 'required|int',
                'sort'       => 'required|string',
                'country'    => 'required|string',
            ], [
                "img_id.required"     => ProductErrorMessageConstant::getNotHaveErrorMessage("IMG_ID"),
                "begin_page.required" => ProductErrorMessageConstant::getNotHaveErrorMessage("BEGINPAGE"),
                "page_size.required"  => ProductErrorMessageConstant::getNotHaveErrorMessage("PAGESIZE"),
                "sort.required"       => ProductErrorMessageConstant::getNotHaveErrorMessage("SORT"),
                "country.required"    => ProductErrorMessageConstant::getNotHaveErrorMessage("COUNTRY"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $params = [
                "img_id"     => $this->request->get("img_id"),
                "begin_page" => $this->request->get("begin_page"),
                "page_size"  => $this->request->get("page_size") > 50 ? 50 : $this->request->get("page_size"),
                "sort"       => $this->request->get("sort"),
                "country"    => $this->request->get("country"),
            ];

            $result = $this->service1688Product->searchImageQuery($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function searchRecommend(): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'begin_page' => 'required|int',
                'page_size'  => 'required|int',
                'country'    => 'required|string',
            ], [
                "begin_page.required" => ProductErrorMessageConstant::getNotHaveErrorMessage("BEGINPAGE"),
                "page_size.required"  => ProductErrorMessageConstant::getNotHaveErrorMessage("PAGESIZE"),
                "country.required"    => ProductErrorMessageConstant::getNotHaveErrorMessage("COUNTRY"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $params = [
                "begin_page" => $this->request->get("begin_page"),
                "page_size"  => $this->request->get("page_size") > 20 ? 20 : $this->request->get("page_size"),
                "country"    => $this->request->get("country"),
            ];

            $result = $this->service1688Product->searchRecommend($params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }

    public function searchRelatedRecommend(int $offerId): JsonResponse
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'begin_page' => 'required|int',
                'page_size'  => 'required|int',
                'country'    => 'required|string',
            ], [
                "begin_page.required" => ProductErrorMessageConstant::getNotHaveErrorMessage("BEGINPAGE"),
                "page_size.required"  => ProductErrorMessageConstant::getNotHaveErrorMessage("PAGESIZE"),
                "country.required"    => ProductErrorMessageConstant::getNotHaveErrorMessage("COUNTRY"),
            ]);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $params = [
                "begin_page" => $this->request->get("begin_page"),
                "page_size"  => $this->request->get("page_size") > 10 ? 10 : $this->request->get("page_size"),
                "country"    => $this->request->get("country"),
            ];

            $result = $this->service1688Product->searchRelatedRecommend($offerId, $params);
            if( $result["isSuccess"] == true ){
                return helpers_json_response(HttpConstant::OK, $result);
            } else {
                return helpers_json_response(HttpConstant::BAD_REQUEST, [], $result["msg"]);
            }
        } catch (Exception $e) {
            return helpers_json_response(HttpConstant::BAD_REQUEST, [], $e->getMessage());
        }
    }
}
