<?php

namespace App\Services\Product;

use App\Abstracts\ProductAbstract;
use App\Abstracts\TransApiAbstract;
use App\Abstracts\UploadAbstract;
use App\Constants\CategoryConstant;
use App\Constants\Constant1688;
use App\Constants\ExceptConstant;
use App\Constants\ForbiddenWordConstant;
use App\Constants\GenuioConstant;
use App\Constants\GosiConstants;
use App\Constants\ImageConstant;
use App\Constants\ImageErrorMessageConstant;
use App\Constants\InspectConstant;
use App\Constants\LogConstant;
use App\Constants\MallConstant;
use App\Constants\OnchannelConstant;
use App\Constants\OptionConstants;
use App\Constants\ProductConstant;
use App\Constants\ProductErrorMessageConstant;
use App\Constants\TransApiConstant;
use App\Constants\WConstant;
use App\Models\Category;
use App\Models\CategoryMapping;
use App\Models\ForbiddenNoticeWordData;
use App\Models\ForbiddenWordData;
use App\Models\GenuioImageData;
use App\Models\GenuioQueueData;
use App\Models\ProductCollectDetailLog;
use App\Models\ProductCollectLog;
use App\Models\ProductData;
use App\Models\ProductExceptData;
use App\Models\ProductExtendData;
use App\Models\ProductForbiddenData;
use App\Models\ProductImageData;
use App\Models\ProductImageDetailData;
use App\Models\ProductInspectData;
use App\Models\ProductNoticeData;
use App\Models\ProductOptionData;
use App\Models\ProductSearchData;
use App\Models\ProductSearchDetailData;
use App\Models\ProductWeightData;
use App\Models\WCategory;
use App\Models\WNoticeData;
use App\Vo\Product\Product1688Dto;
use App\Vo\Product\Product1688ExtendDto;
use App\Vo\Product\Product1688ImageDto;
use App\Vo\Product\Product1688NoticeDto;
use App\Vo\Product\Product1688OptionDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Psr\Log\LogLevel;
use UnexpectedValueException;
use ValueError;

class ProductW1 extends ProductAbstract
{
    private string $accessToken;
    private TransApiAbstract $transApiAbstract;
    private UploadAbstract $uploadAbstract;
    private ProductAbstract $productW2;

    public function __construct(
        TransApiAbstract $transApiAbstract,
        UploadAbstract $uploadAbstract,
        ProductAbstract $productAbstract
    )
    {
        parent::__construct();
        $this->accessToken      = env("1688_ACCESS_TOKEN");
        $this->transApiAbstract = $transApiAbstract;
        $this->uploadAbstract   = $uploadAbstract;
        $this->productW2        = $productAbstract;
    }

    public function getPrdList(array $params): array
    {
        $pageSize        = $params["pageSize"];
        $search_cls      = $params["search_cls"];
        $w_type          = $params["w_type"];
        $keyword         = $params["keyword"];
        $collect_status  = "";
        $trans_status    = $params["trans_status"];
        $mapping_status  = $params["mapping_status"];
        $prd_status      = $params["prd_status"];
        $mdPrice_status  = $params["mdPrice_status"];
        $weight_status   = "";
        $sortArr         = explode("|", $params["sort"]);
        $no_send_channel = "";

        $cate_first     = "";
        $cate_second    = "";
        $cate_third     = "";

        if( isset($params["collect_status"]) ){
            $collect_status = $params["collect_status"];
        }
        if( isset($params["no_send_channel"]) ){
            $no_send_channel = $params["no_send_channel"];
        }
        if( isset($params["cate_first"]) ){
            $cate_first = $params["cate_first"];
        }
        if( isset($params["cate_second"]) ){
            $cate_second = $params["cate_second"];
        }
        if( isset($params["cate_third"]) ){
            $cate_third = $params["cate_third"];
        }
        $firstCateObjs  = [];
        $secondCateObjs = [];
        $thirdCateObjs  = [];
        if( $cate_first == "" ){
            $firstCateObjs = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
        }else {
            if( $cate_first && $cate_second ){
                $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
                $thirdCateObjs  = Category::where("parent_cate_id", $cate_second)->orderBy("category_name", "asc")->get();
            } else if( $cate_first ){
                $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
            }
        }

        $inspect_status      = "";
        $inspect_img_status  = "";
        $inspect_prd_status  = "";
        $inspect_gosi_status = "";
        if( isset($params["inspect_status"]) ){
            $inspect_status = $params["inspect_status"];
        }
        if( isset($params["inspect_img_status"]) ){
            $inspect_img_status = $params["inspect_img_status"];
        }
        if( isset($params["inspect_prd_status"]) ){
            $inspect_prd_status = $params["inspect_prd_status"];
        }
        if( isset($params["inspect_gosi_status"]) ){
            $inspect_gosi_status = $params["inspect_gosi_status"];
        }
        if( isset($params["weight_status"]) ){
            $weight_status = $params["weight_status"];
        }

        $prdBuilder = ProductData::select([
            "product_datas.*",
        ])->with([
            "main_img",
            "options", 
            "images.ai_all_imgs",
            "img_inspect",
            "prd_inspect",
            "gosi_inspect",
            "es_w_log",
            "es_drop_hub_log",
            "oc_public_log",
            "oc_private_log",
        ]);

        if( !empty($keyword) ){
            if( $search_cls == "offer_id"){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                $prdBuilder->whereIn("product_datas." . $search_cls, $keyword);
            } else if( $search_cls == "prd_name_kr" || $search_cls == "prd_name"){
                $prdBuilder->where(function($query1) use ($keyword) {
                    $query1->where("product_datas." . "prd_name", "like", "%" . $keyword . "%")
                    ->orWhere("product_datas." . "prd_name_kr", "like", "%" . $keyword . "%")
                    ->orWhere("product_datas." . "prd_name_en", "like", "%" . $keyword . "%");
                });
            } else if( $search_cls == "option_name" || $search_cls == "option_name" ){
                $prdBuilder->whereHas('options', function ($query) use ($keyword, $search_cls) {
                    $query->where(function($query1) use ($keyword) {
                        $query1->where("option_name", "like", "%" . $keyword . "%")
                        ->orWhere("option_name_kr", "like", "%" . $keyword . "%")
                        ->orWhere("option_name_en", "like", "%" . $keyword . "%");
                    });
                });
            }
        }
        
        if( in_array($sortArr[0], ["option_price", "md_price"]) || !empty($mdPrice_status) ){
            $optSubquery = DB::table('product_option_datas');
            $optSubquery->selectRaw('offer_id, md_price');
            $prdBuilder->groupBy('product_datas.offer_id');

            if( in_array($sortArr[0], ["option_price", "md_price"]) ){
                if( $sortArr[0] == "option_price" ){
                    $optSubquery->selectRaw('MAX(option_price) as max_price');
                } else if( $sortArr[0] == "md_price" ) {
                    $optSubquery->selectRaw('MAX(md_price) as max_price');
                }
                $optSubquery->groupBy('offer_id');
                $prdBuilder->orderBy('opt_sub_qry.max_price', $sortArr[1]);
            }

            $prdBuilder->leftJoinSub($optSubquery, 'opt_sub_qry', function ($join) {
                $join->on('product_datas.offer_id', '=', 'opt_sub_qry.offer_id');
            });

            if( !empty($mdPrice_status) ) {
                if ($mdPrice_status == ProductConstant::MD_PRICE_Y) {
                    $prdBuilder->where(function ($query) {
                        $query->where('opt_sub_qry.md_price', '!=', 0)
                                ->whereNotNull('opt_sub_qry.md_price');
                    });
                } else if ($mdPrice_status == ProductConstant::MD_PRICE_N) {
                    $prdBuilder->where(function ($query) {
                        $query->where('opt_sub_qry.md_price', '=', 0)
                                ->orWhereNull('opt_sub_qry.md_price');
                    });
                }
            }
        } else {
            $prdBuilder->orderBy("product_datas." . $sortArr[0], $sortArr[1]);
        }

        if( !empty($w_type) ){
            $prdBuilder->where("product_datas.w_type", $w_type);
        }

        if( !empty($collect_status) ){
            if( $collect_status == ProductConstant::COLLECT_KR ){
                $prdBuilder->where("product_datas.prd_name_kr", "!=" , "");
            } else if( $collect_status == ProductConstant::COLLECT_EN ){
                $prdBuilder->where("product_datas.prd_name_en", "!=" , "");
            }
        }

        if( !empty($trans_status) ){
            $prdBuilder->where("product_datas.trans_status", $trans_status);
        }

        if( !empty($mapping_status) ){
            $prdBuilder->where("product_datas.mapping_status", $mapping_status);
        }

        if( !empty($inspect_status) ){
            $prdBuilder->where("product_datas.inspect_status", $inspect_status );
        }

        if( !empty($weight_status) ){
            $prdBuilder->leftJoin('product_weight_datas as pwd', function ($join) {
                $join->on('product_datas.offer_id', '=', 'pwd.offer_id');
            });

            $prdBuilder->addSelect("pwd.weight_type", "pwd.weight", "pwd.delivery_price");

            if( $weight_status != ProductConstant::WEIGHT_STATUS_NONE ){
                $prdBuilder->where("pwd.weight_type", $weight_status);
            } else if( $weight_status == ProductConstant::WEIGHT_STATUS_NONE ) {
                $prdBuilder->where(function($query1) {
                    $query1->whereNull("pwd.weight_type")
                    ->orWhere("pwd.weight_type", ProductConstant::WEIGHT_STATUS_NONE);
                });
            }
        }

        if( !empty($no_send_channel) ){
            $no_send_channels = explode(",", $no_send_channel);
            foreach ($no_send_channels as $no_send_ch) {
                if( empty($no_send_ch) ) continue;

                if( $no_send_ch == OnchannelConstant::PRD_CHANNEL ){
                    $prdBuilder->leftJoin('onchannel_product_logs as ocl', function($join) use ($no_send_ch) {
                        $join->on('product_datas.offer_id', '=', 'ocl.offer_id')
                            ->where('ocl.send_type', $no_send_ch);
                    });
                    $prdBuilder->where("ocl.id");
                }
                if( $no_send_ch == OnchannelConstant::PRD_CHANNEL_PRIVATE ){
                    $prdBuilder->leftJoin('onchannel_product_logs as ocl2', function($join) use ($no_send_ch) {
                        $join->on('product_datas.offer_id', '=', 'ocl2.offer_id')
                            ->where('ocl2.send_type', $no_send_ch);
                    });
                    $prdBuilder->whereNull("ocl2.id");
                }
            }
        }

        if( !empty($inspect_img_status) || !empty($inspect_prd_status) || !empty($inspect_gosi_status) ){

            if( !empty($inspect_img_status) ){
                $prdBuilder->leftJoin('product_inspect_datas as pid1', function ($join) {
                    $join->on('product_datas.offer_id', '=', 'pid1.offer_id')
                         ->where('pid1.inspect_type', InspectConstant::INSPECT_IMAGE);
                });

                if( $inspect_img_status == InspectConstant::IS_INSPECT_Y ){
                    $prdBuilder->where([
                        "pid1.is_inspect" => InspectConstant::IS_INSPECT_Y,
                    ]);
                } else if( $inspect_img_status == InspectConstant::IS_INSPECT_N ){
                    $prdBuilder->where(function ($query) {
                        $query->where('pid1.is_inspect', InspectConstant::IS_INSPECT_N)
                        ->orWhereNull('pid1.id');
                    });
                }
            }

            if( !empty($inspect_prd_status) ){
                $prdBuilder->leftJoin('product_inspect_datas as pid2', function ($join) {
                    $join->on('product_datas.offer_id', '=', 'pid2.offer_id')
                         ->where('pid2.inspect_type', InspectConstant::INSPECT_PRODUCT);
                });

                if( $inspect_prd_status == InspectConstant::IS_INSPECT_Y ){
                    $prdBuilder->where([
                        "pid2.is_inspect" => InspectConstant::IS_INSPECT_Y,
                    ]);
                } else if( $inspect_prd_status == InspectConstant::IS_INSPECT_N ){
                    $prdBuilder->where(function ($query) {
                        $query->where('pid2.is_inspect', InspectConstant::IS_INSPECT_N)
                        ->orWhereNull('pid2.id');
                    });
                }
            }

            if( !empty($inspect_gosi_status) ){
                $prdBuilder->leftJoin('product_inspect_datas as pid3', function ($join) {
                    $join->on('product_datas.offer_id', '=', 'pid3.offer_id')
                         ->where('pid3.inspect_type', InspectConstant::INSPECT_NOTICE);
                });

                if( $inspect_gosi_status == InspectConstant::IS_INSPECT_Y ){
                    $prdBuilder->where([
                        "pid3.is_inspect" => InspectConstant::IS_INSPECT_Y,
                    ]);
                } else if( $inspect_gosi_status == InspectConstant::IS_INSPECT_N ){
                    $prdBuilder->where(function ($query) {
                        $query->where('pid3.is_inspect', InspectConstant::IS_INSPECT_N)
                        ->orWhereNull('pid3.id');
                    });
                }
            }
        }

        if( !empty($prd_status) ){
            $prdBuilder->where("product_datas.status", $prd_status);
        } else {
            $prdBuilder->whereIn("product_datas.status", ProductConstant::PRD_SHOW_STATUS);
        }

        if( !empty($cate_third) && !empty($cate_second) && !empty($cate_first) ){
            $prdBuilder->where("product_datas.category_id", $cate_third);
        } else if( !empty($cate_second) && !empty($cate_first) ){
            $childCates = Category::where("parent_cate_id", $cate_second)->pluck("category_id");
            if( !empty($childCates) ){
                $prdBuilder->where(function ($query) use ($childCates, $cate_second){
                    $query->whereIn("product_datas.category_id", $childCates)
                    ->orWhere("product_datas.category_id", $cate_second);
                });
            }
        } else if( !empty($cate_first) ){
            $secondChildCates = Category::where("parent_cate_id", $cate_first)->pluck("category_id");
            $thirdChildCates  = Category::whereIn("parent_cate_id", $secondChildCates)->pluck("category_id");
            $allChildCates    = $secondChildCates->merge($thirdChildCates);
            if( !empty($allChildCates) ){
                $prdBuilder->where(function ($query) use ($allChildCates, $cate_first){
                    $query->whereIn("product_datas.category_id", $allChildCates)
                    ->orWhere("product_datas.category_id", $cate_first);
                });
            }
        }

        $totalCnt  = ProductData::whereIn("status", ProductConstant::PRD_SHOW_STATUS)->count();
        $transYCnt = ProductData::whereIn("status", ProductConstant::PRD_SHOW_STATUS)
        ->where([
            "trans_status"   => ProductConstant::TRANS_STATUS_Y
        ])->count();
        $transNCnt = $totalCnt - $transYCnt;

        $inspectYCnt = ProductData::whereIn("status", ProductConstant::PRD_SHOW_STATUS)
        ->where([
            "inspect_status" => InspectConstant::IS_INSPECT_Y
        ])->count();
        $inspectNCnt = $totalCnt - $inspectYCnt;

        $imgInspectYCnt = ProductData::query()
        ->join('product_inspect_datas as b', 'product_datas.offer_id', '=', 'b.offer_id')
        ->whereIn("status", ProductConstant::PRD_SHOW_STATUS)
        ->where([
            "b.inspect_type" => InspectConstant::INSPECT_IMAGE,
            "b.is_inspect"   => InspectConstant::IS_INSPECT_Y
        ])->count();
        $imgInspectNCnt = $totalCnt - $imgInspectYCnt;

        $prdInspectYCnt = ProductData::query()
        ->join('product_inspect_datas as b', 'product_datas.offer_id', '=', 'b.offer_id')
        ->whereIn("status", ProductConstant::PRD_SHOW_STATUS)
        ->where([
            "b.inspect_type" => InspectConstant::INSPECT_PRODUCT,
            "b.is_inspect"   => InspectConstant::IS_INSPECT_Y
        ])->count();
        $prdInspectNCnt = $totalCnt - $prdInspectYCnt;

        $gosiInspectYCnt = ProductData::query()
        ->join('product_inspect_datas as b', 'product_datas.offer_id', '=', 'b.offer_id')
        ->whereIn("status", ProductConstant::PRD_SHOW_STATUS)
        ->where([
            "b.inspect_type" => InspectConstant::INSPECT_NOTICE,
            "b.is_inspect"   => InspectConstant::IS_INSPECT_Y
        ])->count();
        $gosiInspectNCnt = $totalCnt - $gosiInspectYCnt;

        $lists = $prdBuilder->paginate($pageSize)->appends($params);
        return [
            "paginator"       => $lists,
            "totalCnt"        => $totalCnt,
            "transYCnt"       => $transYCnt,
            "transNCnt"       => $transNCnt,
            "inspectYCnt"     => $inspectYCnt,
            "inspectNCnt"     => $inspectNCnt,
            "imgInspectYCnt"  => $imgInspectYCnt,
            "imgInspectNCnt"  => $imgInspectNCnt,
            "prdInspectYCnt"  => $prdInspectYCnt,
            "prdInspectNCnt"  => $prdInspectNCnt,
            "gosiInspectYCnt" => $gosiInspectYCnt,
            "gosiInspectNCnt" => $gosiInspectNCnt,
            "firstCateObjs"   => $firstCateObjs,
            "secondCateObjs"  => $secondCateObjs,
            "thirdCateObjs"   => $thirdCateObjs,
        ];
    }

    public function getPrdExceptList(array $params): array
    {
        $pageSize       = $params["pageSize"];
        $search_cls     = $params["search_cls"];
        $w_type         = $params["w_type"];
        $keyword        = $params["keyword"];
        $trans_status   = $params["trans_status"];
        $mapping_status = $params["mapping_status"];
        $prd_status     = $params["prd_status"];
        $mdPrice_status = $params["mdPrice_status"];
        $sortArr        = explode("|", $params["sort"]);

        $cate_first     = "";
        $cate_second    = "";
        $cate_third     = "";

        if( isset($params["cate_first"]) ){
            $cate_first = $params["cate_first"];
        }
        if( isset($params["cate_second"]) ){
            $cate_second = $params["cate_second"];
        }
        if( isset($params["cate_third"]) ){
            $cate_third = $params["cate_third"];
        }
        $firstCateObjs  = [];
        $secondCateObjs = [];
        $thirdCateObjs  = [];
        if( $cate_first == "" ){
            $firstCateObjs = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
        }else {
            if( $cate_first && $cate_second ){
                $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
                $thirdCateObjs  = Category::where("parent_cate_id", $cate_second)->orderBy("category_name", "asc")->get();
            } else if( $cate_first ){
                $firstCateObjs  = Category::where("parent_cate_id", 0)->orderBy("category_name", "asc")->get();
                $secondCateObjs = Category::where("parent_cate_id", $cate_first)->orderBy("category_name", "asc")->get();
            }
        }
        
        $inspect_status      = "";
        $inspect_img_status  = "";
        $inspect_prd_status  = "";
        $inspect_gosi_status = "";
        if( isset($params["inspect_status"]) ){
            $inspect_status = $params["inspect_status"];
        }
        if( isset($params["inspect_img_status"]) ){
            $inspect_img_status = $params["inspect_img_status"];
        }
        if( isset($params["inspect_prd_status"]) ){
            $inspect_prd_status = $params["inspect_prd_status"];
        }
        if( isset($params["inspect_gosi_status"]) ){
            $inspect_gosi_status = $params["inspect_gosi_status"];
        }

        $prdBuilder = ProductData::select(["product_datas.*"])->with([
            "main_img",
            "options", 
            "images.ai_all_imgs",
            "img_inspect",
            "prd_inspect",
            "gosi_inspect",
        ]);

        if( !empty($keyword) ){
            if( $search_cls == "offer_id"){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                $prdBuilder->whereIn("product_datas." . $search_cls, $keyword);
            } else if( $search_cls == "prd_name_kr" || $search_cls == "prd_name"){
                $prdBuilder->where(function($query1) use ($keyword) {
                    $query1->where("product_datas." . "prd_name", "like", "%" . $keyword . "%")
                    ->orWhere("product_datas." . "prd_name_kr", "like", "%" . $keyword . "%")
                    ->orWhere("product_datas." . "prd_name_en", "like", "%" . $keyword . "%");
                });
            } else if( $search_cls == "option_name" || $search_cls == "option_name" ){
                $prdBuilder->whereHas('options', function ($query) use ($keyword, $search_cls) {
                    $query->where(function($query1) use ($keyword) {
                        $query1->where("option_name", "like", "%" . $keyword . "%")
                        ->orWhere("option_name_kr", "like", "%" . $keyword . "%")
                        ->orWhere("option_name_en", "like", "%" . $keyword . "%");
                    });
                });
            }
        }
        
        if( in_array($sortArr[0], ["option_price", "md_price"]) || !empty($mdPrice_status) ){
            $optSubquery = DB::table('product_option_datas');
            $optSubquery->selectRaw('offer_id, md_price');
            $prdBuilder->groupBy('product_datas.offer_id');

            if( in_array($sortArr[0], ["option_price", "md_price"]) ){
                if( $sortArr[0] == "option_price" ){
                    $optSubquery->selectRaw('MAX(option_price) as max_price');
                } else if( $sortArr[0] == "md_price" ) {
                    $optSubquery->selectRaw('MAX(md_price) as max_price');
                }
                $optSubquery->groupBy('offer_id');
                $prdBuilder->orderBy('opt_sub_qry.max_price', $sortArr[1]);
            }

            $prdBuilder->leftJoinSub($optSubquery, 'opt_sub_qry', function ($join) {
                $join->on('product_datas.offer_id', '=', 'opt_sub_qry.offer_id');
            });

            if( !empty($mdPrice_status) ) {
                if ($mdPrice_status == ProductConstant::MD_PRICE_Y) {
                    $prdBuilder->where(function ($query) {
                        $query->where('opt_sub_qry.md_price', '!=', 0)
                                ->whereNotNull('opt_sub_qry.md_price');
                    });
                } else if ($mdPrice_status == ProductConstant::MD_PRICE_N) {
                    $prdBuilder->where(function ($query) {
                        $query->where('opt_sub_qry.md_price', '=', 0)
                                ->orWhereNull('opt_sub_qry.md_price');
                    });
                }
            }
        } else {
            $prdBuilder->orderBy("product_datas." . $sortArr[0], $sortArr[1]);
        }

        if( !empty($w_type) ){
            $prdBuilder->where("product_datas.w_type", $w_type);
        }

        if( !empty($trans_status) ){
            $prdBuilder->where("product_datas.trans_status", $trans_status);
        }

        if( !empty($mapping_status) ){
            $prdBuilder->where("product_datas.mapping_status", $mapping_status);
        }

        if( !empty($inspect_status) ){
            $prdBuilder->where("product_datas.inspect_status", $inspect_status );
        }

        if( !empty($inspect_img_status) || !empty($inspect_prd_status) || !empty($inspect_gosi_status) ){

            if( !empty($inspect_img_status) ){
                $prdBuilder->leftJoin('product_inspect_datas as pid1', function ($join) {
                    $join->on('product_datas.offer_id', '=', 'pid1.offer_id')
                         ->where('pid1.inspect_type', InspectConstant::INSPECT_IMAGE);
                });

                if( $inspect_img_status == InspectConstant::IS_INSPECT_Y ){
                    $prdBuilder->where([
                        "pid1.is_inspect" => InspectConstant::IS_INSPECT_Y,
                    ]);
                } else if( $inspect_img_status == InspectConstant::IS_INSPECT_N ){
                    $prdBuilder->where(function ($query) {
                        $query->where('pid1.is_inspect', InspectConstant::IS_INSPECT_N)
                        ->orWhereNull('pid1.id');
                    });
                }
            }

            if( !empty($inspect_prd_status) ){
                $prdBuilder->leftJoin('product_inspect_datas as pid2', function ($join) {
                    $join->on('product_datas.offer_id', '=', 'pid2.offer_id')
                         ->where('pid2.inspect_type', InspectConstant::INSPECT_PRODUCT);
                });

                if( $inspect_prd_status == InspectConstant::IS_INSPECT_Y ){
                    $prdBuilder->where([
                        "pid2.is_inspect" => InspectConstant::IS_INSPECT_Y,
                    ]);
                } else if( $inspect_prd_status == InspectConstant::IS_INSPECT_N ){
                    $prdBuilder->where(function ($query) {
                        $query->where('pid2.is_inspect', InspectConstant::IS_INSPECT_N)
                        ->orWhereNull('pid2.id');
                    });
                }
            }

            if( !empty($inspect_gosi_status) ){
                $prdBuilder->leftJoin('product_inspect_datas as pid3', function ($join) {
                    $join->on('product_datas.offer_id', '=', 'pid3.offer_id')
                         ->where('pid3.inspect_type', InspectConstant::INSPECT_NOTICE);
                });

                if( $inspect_gosi_status == InspectConstant::IS_INSPECT_Y ){
                    $prdBuilder->where([
                        "pid3.is_inspect" => InspectConstant::IS_INSPECT_Y,
                    ]);
                } else if( $inspect_gosi_status == InspectConstant::IS_INSPECT_N ){
                    $prdBuilder->where(function ($query) {
                        $query->where('pid3.is_inspect', InspectConstant::IS_INSPECT_N)
                        ->orWhereNull('pid3.id');
                    });
                }
            }
        }
        if( !empty($prd_status) ){
            $prdBuilder->where("product_datas.status", $prd_status);
        }

        if( !empty($cate_third) && !empty($cate_second) && !empty($cate_first) ){
            $prdBuilder->where("product_datas.category_id", $cate_third);
        } else if( !empty($cate_second) && !empty($cate_first) ){
            $childCates = Category::where("parent_cate_id", $cate_second)->pluck("category_id");
            if( !empty($childCates) ){
                $prdBuilder->where(function ($query) use ($childCates, $cate_second){
                    $query->whereIn("product_datas.category_id", $childCates)
                    ->orWhere("product_datas.category_id", $cate_second);
                });
            }
        } else if( !empty($cate_first) ){
            $secondChildCates = Category::where("parent_cate_id", $cate_first)->pluck("category_id");
            $thirdChildCates  = Category::whereIn("parent_cate_id", $secondChildCates)->pluck("category_id");
            $allChildCates    = $secondChildCates->merge($thirdChildCates);
            if( !empty($allChildCates) ){
                $prdBuilder->where(function ($query) use ($allChildCates, $cate_first){
                    $query->whereIn("product_datas.category_id", $allChildCates)
                    ->orWhere("product_datas.category_id", $cate_first);
                });
            }
        }

        $totalCnt = ProductData::where([
            "status" => ProductConstant::PRD_STATUS_EXCEPT
        ])->count();
        $transYCnt = ProductData::where([
            "trans_status" => ProductConstant::TRANS_STATUS_Y,
            "status"       => ProductConstant::PRD_STATUS_EXCEPT
        ])->count();
        $transNCnt = ProductData::where([
            "trans_status" => ProductConstant::TRANS_STATUS_N,
            "status"       => ProductConstant::PRD_STATUS_EXCEPT
        ])->count();

        $inspectYCnt = ProductData::where([
            "inspect_status" => InspectConstant::IS_INSPECT_Y,
            "status"         => ProductConstant::PRD_STATUS_EXCEPT
        ])->count();
        $inspectNCnt = $totalCnt - $inspectYCnt;

        $imgInspectYCnt = ProductData::query()
        ->join('product_inspect_datas as b', 'product_datas.offer_id', '=', 'b.offer_id')
        ->where([
            "b.inspect_type" => InspectConstant::INSPECT_IMAGE,
            "b.is_inspect"   => InspectConstant::IS_INSPECT_Y,
            "status"         => ProductConstant::PRD_STATUS_EXCEPT
        ])->count();
        $imgInspectNCnt = $totalCnt - $imgInspectYCnt;

        $prdInspectYCnt = ProductData::query()
        ->join('product_inspect_datas as b', 'product_datas.offer_id', '=', 'b.offer_id')
        ->where([
            "b.inspect_type" => InspectConstant::INSPECT_PRODUCT,
            "b.is_inspect"   => InspectConstant::IS_INSPECT_Y,
            "status"         => ProductConstant::PRD_STATUS_EXCEPT
        ])->count();
        $prdInspectNCnt = $totalCnt - $prdInspectYCnt;

        $gosiInspectYCnt = ProductData::query()
        ->join('product_inspect_datas as b', 'product_datas.offer_id', '=', 'b.offer_id')
        ->where([
            "b.inspect_type" => InspectConstant::INSPECT_NOTICE,
            "b.is_inspect"   => InspectConstant::IS_INSPECT_Y,
            "status"         => ProductConstant::PRD_STATUS_EXCEPT
        ])->count();
        $gosiInspectNCnt = $totalCnt - $gosiInspectYCnt;

        $lists = $prdBuilder->paginate($pageSize)->appends($params);
        return [
            "paginator"       => $lists,
            "totalCnt"        => $totalCnt,
            "transYCnt"       => $transYCnt,
            "transNCnt"       => $transNCnt,
            "inspectYCnt"     => $inspectYCnt,
            "inspectNCnt"     => $inspectNCnt,
            "imgInspectYCnt"  => $imgInspectYCnt,
            "imgInspectNCnt"  => $imgInspectNCnt,
            "prdInspectYCnt"  => $prdInspectYCnt,
            "prdInspectNCnt"  => $prdInspectNCnt,
            "gosiInspectYCnt" => $gosiInspectYCnt,
            "gosiInspectNCnt" => $gosiInspectNCnt,
            "firstCateObjs"   => $firstCateObjs,
            "secondCateObjs"  => $secondCateObjs,
            "thirdCateObjs"   => $thirdCateObjs,
        ];
    }

    public function apiPrdDetail(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductData::with([
                "images.ai_all_imgs",
                "extends",
                "options",
                "notices",
                "category",
                "w_mapping.w_cate_name",
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getPrdCollectLogList(array $params): LengthAwarePaginator
    {
        $pageSize  = $params["pageSize"];

        $prdBuilder = ProductCollectLog::where("version", WConstant::WAPP_W1)->orderBy("created_at", "desc");
        $lists = $prdBuilder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function getPrdDetail(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductData::with([
                "images",
                "extends",
                "options",
                "notices.except_data",
                "category",
                "w_mapping.w_cate_name",
                "img_inspect",
                "prd_inspect",
                "gosi_inspect",
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getPrdDetailEn(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductData::with([
                "en_images",
                "extends",
                "options",
                "notices.except_data",
                "category",
                "w_mapping.w_cate_name",
                "img_inspect",
                "prd_inspect",
                "gosi_inspect",
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function prdCollectLogDetail(int $logId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $prdObj = ProductCollectLog::with([
                "details"
            ])->where("id", $logId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }

            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getProductData(int $offerId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
            $payload = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'country' => Constant1688::LANGUAGE_KO,
                    'offerId' => $offerId,
                ]
            ];
            $returnMsg = curl_1688("post", $endPoint, $payload);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    public function saveMallProductByCategotyId(int $categoryId): void
    {
        $msg = "======================== 실행 시작 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");

        $page     = 1;
        $pageSize = 50;
        try {
            $this->saveMallProductRecursively($categoryId, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (categoryId: {$categoryId}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 (categoryId: {$categoryId}) ========================";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");
    }

    public function saveMallProductRecursively(int $categoryId, int $page, int $pageSize, int $totalPage = 0): void
    {
        $msg = "start saveMallProductRecursively | page: {$page} | categoryId: {$categoryId}";
        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId");

        $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | categoryId: {$categoryId} | page: {$page}";
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'keyword'    => '',
                    'beginPage'  => $page,
                    'pageSize'   => $pageSize,
                    'country'    => Constant1688::LANGUAGE_KO,
                    'categoryId' => $categoryId,
                ]
            ];
            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != true ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                foreach ($productDatas as $productData) {
                    try {
                        $offerId = $productData["offerId"];

                        /** 1. W2로 수집 시도 */
                        // $resultW2 = $this->productW2->collectProductNotLog($offerId);
                        $resultW2["isSuccess"] = false;

                        /** 2. W2로 수집 실패 시 W1으로 수집 시도 */
                        if( $resultW2["isSuccess"] != true ){
                            $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                            $payload        = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_KO,
                                ]
                            ];
                            $detailResult = curl_1688("POST", $endPoint, $payload);
                            if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                            }

                            $payload_detail = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_EN,
                                ]
                            ];
                            $detailEnResult = curl_1688("POST", $endPoint, $payload_detail);
                            if( $detailEnResult["isSuccess"] != true || $detailEnResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("SEARCH_QUERYPRODUCTDETAIL_EN"));
                            }

                            $detailProduct = $detailResult["data"]["result"]["result"];
                            $prdCategoryId = $detailProduct["categoryId"];

                            $prdDto                   = $this->get1688ProductDto($detailResult, $detailEnResult);
                            $product1688Dto           = $prdDto["product1688Dto"];
                            $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                            $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                            $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                            $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                            $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                            if( $saveResult["isSuccess"] != true ){
                                throw new Exception($saveResult["msg"]);
                            }
                        }
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId} | categoryId: {$categoryId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
                    } catch (UnexpectedValueException $ue) {
                        $msg = $ue->getMessage() . " | page: {$page} | offerId: {$offerId} | categoryId: {$categoryId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);
                    }
                }
            } else {
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | page: {$page} | categoryId: {$categoryId}");
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "saveMallProductByCategotyId", "saveMallProductByCategotyId", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductRecursively($categoryId, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function saveMallProductByImageId(string $imageId): void
    {
        $msg = "======================== 실행 시작 (imageId: {$imageId}) ========================";
        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId");

        $page     = 1;
        $pageSize = 50;
        try {
            $this->saveMallProductByImageIdRecursively($imageId, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (imageId: {$imageId}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 (imageId: {$imageId}) ========================";
        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId");
    }

    public function saveMallProductByImageIdRecursively(string $imageId, int $page, int $pageSize, int $totalPage = 0): void
    {
        $msg = "start saveMallProductByImageIdRecursively | page: {$page} | imageId: {$imageId}";
        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId");

        $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_IMAGEQUERY") . " | imageId: {$imageId} | page: {$page}";
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.imageQuery/";
            $payload = [
                'access_token'    => $this->accessToken,
                'offerQueryParam' => [
                    'beginPage' => $page,
                    'pageSize'  => $pageSize,
                    'country'   => Constant1688::LANGUAGE_KO,
                    'imageId'   => $imageId,
                ]
            ];

            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != "true" ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                foreach ($productDatas as $productData) {
                    try {
                        $offerId = $productData["offerId"];

                        /** 1. W2로 수집 시도 */
                        // $resultW2 = $this->productW2->collectProductNotLog($offerId);
                        $resultW2["isSuccess"] = false;

                        /** 2. W2로 수집 실패 시 W1으로 수집 시도 */
                        if( $resultW2["isSuccess"] != true ){
                            $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                            $payload        = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_KO,
                                ]
                            ];
                            $detailResult = curl_1688("POST", $endPoint, $payload);
                            if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                            }

                            $payload_detail = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_EN,
                                ]
                            ];
                            $detailEnResult = curl_1688("POST", $endPoint, $payload_detail);
                            if( $detailEnResult["isSuccess"] != true || $detailEnResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("SEARCH_QUERYPRODUCTDETAIL_EN"));
                            }

                            $detailProduct = $detailResult["data"]["result"]["result"];
                            $prdCategoryId = $detailProduct["categoryId"];

                            $prdDto                   = $this->get1688ProductDto($detailResult, $detailEnResult);
                            $product1688Dto           = $prdDto["product1688Dto"];
                            $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                            $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                            $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                            $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                            $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                            if( $saveResult["isSuccess"] != true ){
                                throw new Exception($saveResult["msg"]);
                            }
                        }
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId} | imageId: {$imageId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);
                    } catch (UnexpectedValueException $ue) {
                        $msg = $ue->getMessage() . " | page: {$page} | offerId: {$offerId} | imageId: {$imageId} | prdCategoryId: {$prdCategoryId}";
                        debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);
                    }
                }
            } else {
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_SEARCH_IMAGEQUERY") . " | page: {$page} | imageId: {$imageId}");
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductByImageIdRecursively($imageId, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "saveMallProductByImageId", "saveMallProductByImageId", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveMallProductByImageIdRecursively($imageId, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function collectProduct(array $offerIds, string $type = LogConstant::COLLECT_API_KEYWORDQUERY): void
    {
        $logId = ProductCollectLog::insertGetId([
            "type"       => $type,
            "status"     => LogConstant::COLLECT_RUNNING,
            "payload"    => implode(", ", $offerIds),
            "log_count"  => 0,
            "version"    => WConstant::WAPP_W1,
            "created_at" => Carbon::now()
        ]);
        $successCnt = 0;
        $failCnt    = 0;
        foreach ($offerIds as $offerId) {
            $offerId = trim($offerId);
            try {

                /** 1. W2로 수집 시도 */
                // $resultW2 = $this->productW2->collectProductNotLog($offerId);
                $resultW2["isSuccess"] = false;

                /** 2. W2로 수집 실패 시 W1으로 수집 시도 */
                if( $resultW2["isSuccess"] != true ){
                    $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                    $payload  = [
                        'access_token'     => $this->accessToken,
                        'offerDetailParam' => [
                            'offerId' => $offerId,
                            'country' => Constant1688::LANGUAGE_KO,
                        ]
                    ];
                    $detailResult = curl_1688("POST", $endPoint, $payload);
                    if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                        throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                    }

                    $payload_detail = [
                        'access_token'     => $this->accessToken,
                        'offerDetailParam' => [
                            'offerId' => $offerId,
                            'country' => Constant1688::LANGUAGE_EN,
                        ]
                    ];
                    $detailEnResult = curl_1688("POST", $endPoint, $payload_detail);
                    if( $detailEnResult["isSuccess"] != true || $detailEnResult["data"]["result"]["success"] != true ){
                        throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("SEARCH_QUERYPRODUCTDETAIL_EN"));
                    }
    
                    $prdDto                   = $this->get1688ProductDto($detailResult, $detailEnResult);
                    $product1688Dto           = $prdDto["product1688Dto"];
                    $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                    $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                    $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                    $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];
    
                    $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);
                    if( $saveResult["isSuccess"] != true ){
                        throw new Exception($saveResult["msg"]);
                    }
                }

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                    "msg"        => ""
                ]);

                $successCnt++;
            } catch (Exception $e) {
                $msg = "offerId: {$offerId} | error: " . $e->getMessage();
                debug_log($msg, "collectProduct/".$type, $type, LogLevel::ERROR);

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_N,
                    "msg"        => $e->getMessage()
                ]);

                $failCnt++;
            }
        }

        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $successCnt + $failCnt,
            "completed_at" => Carbon::now()
        ]);
    }

    public function collectProductNotLog(int $offerId): array
    {
        $returnMsg = helpers_fail_message();
        
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
            $payload  = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'offerId' => $offerId,
                    'country' => Constant1688::LANGUAGE_KO,
                ]
            ];
            $detailResult = curl_1688("POST", $endPoint, $payload);
            if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
            }

            $payload_detail = [
                'access_token'     => $this->accessToken,
                'offerDetailParam' => [
                    'offerId' => $offerId,
                    'country' => Constant1688::LANGUAGE_EN,
                ]
            ];
            $detailEnResult = curl_1688("POST", $endPoint, $payload_detail);
            if( $detailEnResult["isSuccess"] != true || $detailEnResult["data"]["result"]["success"] != true ){
                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("SEARCH_QUERYPRODUCTDETAIL_EN"));
            }

            $prdDto                   = $this->get1688ProductDto($detailResult, $detailEnResult);
            $product1688Dto           = $prdDto["product1688Dto"];
            $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
            $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
            $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
            $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

            $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);
            if( $saveResult["isSuccess"] != true ){
                throw new Exception($saveResult["msg"]);
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $msg = "offerId: {$offerId} | error: " . $e->getMessage();

            $returnMsg = helpers_fail_message($msg);
        }

        return $returnMsg;
    }

    public function collectProductImage(array $offerIds): void
    {
        $logId = ProductCollectLog::insertGetId([
            "type"       => LogConstant::COLLECT_API_KEYWORDQUERY,
            "status"     => LogConstant::COLLECT_RUNNING,
            "payload"    => implode(", ", $offerIds),
            "log_count"  => 0,
            "version"    => WConstant::WAPP_W1,
            "created_at" => Carbon::now()
        ]);
        $successCnt = 0;
        $failCnt    = 0;
        foreach ($offerIds as $offerId) {
            try {
                $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                $payload  = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_KO,
                    ]
                ];
                $detailResult = curl_1688("POST", $endPoint, $payload);
                if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                }

                $payload_detail = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_EN,
                    ]
                ];
                $detailEnResult = curl_1688("POST", $endPoint, $payload_detail);
                if( $detailEnResult["isSuccess"] != true || $detailEnResult["data"]["result"]["success"] != true ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("SEARCH_QUERYPRODUCTDETAIL_EN"));
                }

                $prdDto                   = $this->get1688ProductDto($detailResult, $detailEnResult);
                $product1688Dto           = $prdDto["product1688Dto"];
                $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);
                if( $saveResult["isSuccess"] != true ){
                    throw new Exception($saveResult["msg"]);
                }

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                    "msg"        => ""
                ]);

                $successCnt++;
            } catch (Exception $e) {
                $msg = "offerId: {$offerId} | error: " . $e->getMessage();
                debug_log($msg, "collectProduct", "collectProduct", LogLevel::ERROR);

                ProductCollectDetailLog::create([
                    "log_id"     => $logId,
                    "offer_id"   => $offerId,
                    "is_collect" => LogConstant::COLLECT_DETAIL_N,
                    "msg"        => $e->getMessage()
                ]);

                $failCnt++;
            }
        }

        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $successCnt + $failCnt,
            "completed_at" => Carbon::now()
        ]);
    }

    public function get1688ProductDto(array $detailResult, array $detailEnResult): array
    {
        if( empty($detailEnResult) ){
            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL_W2_EN"));
        }

        $detailProduct   = $detailResult["data"]["result"]["result"];
        $detailEnProduct = $detailEnResult["data"]["result"]["result"];
        $offerId         = $detailProduct["offerId"];
        $prdCategoryId   = $detailProduct["categoryId"];
        $status          = $detailProduct["status"];
        if( $status != ProductConstant::PRD_STATUS_PUBLISH ){
            $status = ProductConstant::PRD_STATUS_STOP;
        }
        if( !isset($detailProduct["productSkuInfos"]) || empty($detailProduct["productSkuInfos"]) ){
            $status = ProductConstant::PRD_STATUS_MISS;
        }

        $prdObj = ProductData::where("offer_id", $offerId)->first();
        if( $prdObj != null && $prdObj->status == ProductConstant::PRD_STATUS_EXCEPT ){
            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_EXCEPT"));
        }

        $deletePrdForbiddenWords     = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
        $replacePrdForbiddenWords    = ForbiddenWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();
        $deleteNoticeForbiddenWords  = ForbiddenNoticeWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_DELETE)->get();
        $replaceNoticeForbiddenWords = ForbiddenNoticeWordData::where("keyword_type", ForbiddenWordConstant::KEYWORD_REPLACE)->get();

        // 1. 상품 이미지
        $product1688ImageDtoList = [];
        // 1-1. 국문 이미지
        foreach ($detailProduct["productImage"]["images"] as $imgKey => $prdImage) {
            if( $imgKey == 0 ) {
                $imgType = ImageConstant::IMAGE_TYPE_MAIN;
            } else {
                $imgType = ImageConstant::IMAGE_TYPE_SUB;
            }
            $is_except     = ImageConstant::IS_EXCEPT_N;
            $img_url_trans = "";
            $imgObj        = ProductImageData::where([
                "offer_id"       => $offerId,
                "img_type"       => $imgType,
                "img_url_origin" => $prdImage,
                "lang"           => WConstant::WAPP_KR,
            ])->first();
            if( $imgObj != null ){
                $is_except     = $imgObj->is_except;
                $img_url_trans = $imgObj->img_url_trans;
            }
            if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                continue;
            }

            $isChangeImg = $this->isChangeImage($offerId, $prdImage, $imgType);
            $imgWidth    = 0;
            $imgHeight   = 0;
            $imgByte     = 0;
            $imgMime     = "";
            if( $isChangeImg == true ){
                $imageInfo = $this->checkImageSize($prdImage);
                $imgWidth  = $imageInfo["width"];
                $imgHeight = $imageInfo["height"];
                $imgByte   = $imageInfo["byte"];
                $imgMime   = $imageInfo["mime"];
            }
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgType,
                "lang"           => WConstant::WAPP_KR,
                "is_except"      => $is_except,
                "img_url_origin" => $prdImage,
                "img_url_trans"  => $img_url_trans,
                "isChangeImg"    => $isChangeImg,
                "width"          => $imgWidth,
                "height"         => $imgHeight,
                "byte"           => $imgByte,
                "mime"           => $imgMime
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }
        if( isset($detailProduct["productSkuInfos"]) ){
            foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                if( isset($prdOptions["skuAttributes"]) ){

                    $prdImage = "";
                    foreach ($prdOptions["skuAttributes"] as $prdOption) {
                        if( isset($prdOption["skuImageUrl"]) ){
                            $prdImage = $prdOption["skuImageUrl"];
                        }
                    }
                    if( $prdImage == "" ){
                        continue;
                    }

                    $imgType       = ImageConstant::IMAGE_TYPE_SUB;
                    $is_except     = ImageConstant::IS_EXCEPT_N;
                    $img_url_trans = "";
                    $imgObj        = ProductImageData::where([
                        "offer_id"       => $offerId,
                        "img_type"       => $imgType,
                        "img_url_origin" => $prdImage,
                        "lang"           => WConstant::WAPP_KR,
                    ])->first();
                    if( $imgObj != null ){
                        $is_except = $imgObj->is_except;
                        $img_url_trans = $imgObj->img_url_trans;
                    }
                    if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                        continue;
                    }
    
                    $isChangeImg = $this->isChangeImage($offerId, $prdImage, $imgType);
                    $imgWidth    = 0;
                    $imgHeight   = 0;
                    $imgByte     = 0;
                    $imgMime     = "";
                    if( $isChangeImg == true ){
                        $imageInfo = $this->checkImageSize($prdImage);
                        $imgWidth  = $imageInfo["width"];
                        $imgHeight = $imageInfo["height"];
                        $imgByte   = $imageInfo["byte"];
                        $imgMime   = $imageInfo["mime"];
                    }
                    $product1688ImageDto = new Product1688ImageDto();
                    $product1688ImageDto->bind([
                        "offerId"        => $offerId,
                        "imgType"        => $imgType,
                        "lang"           => WConstant::WAPP_KR,
                        "is_except"      => $is_except,
                        "img_url_origin" => $prdImage,
                        "img_url_trans"  => $img_url_trans,
                        "isChangeImg"    => $isChangeImg,
                        "width"          => $imgWidth,
                        "height"         => $imgHeight,
                        "byte"           => $imgByte,
                        "mime"           => $imgMime
                    ]);
                    $product1688ImageDtoList[] = $product1688ImageDto;
                }
            }
        }

        // 1-1. 영문 이미지
        foreach ($detailEnProduct["productImage"]["images"] as $imgKey => $prdImage) {
            if( $imgKey == 0 ) {
                $imgType = ImageConstant::IMAGE_TYPE_MAIN;
            } else {
                $imgType = ImageConstant::IMAGE_TYPE_SUB;
            }
            $is_except     = ImageConstant::IS_EXCEPT_N;
            $img_url_trans = "";
            $imgObj        = ProductImageData::where([
                "offer_id"       => $offerId,
                "img_type"       => $imgType,
                "img_url_origin" => $prdImage,
                "lang"           => WConstant::WAPP_EN,
            ])->first();
            if( $imgObj != null ){
                $is_except     = $imgObj->is_except;
                $img_url_trans = $imgObj->img_url_trans;
            }
            if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                continue;
            }

            $isChangeImg = $this->isChangeImage($offerId, $prdImage, $imgType);
            $imgWidth    = 0;
            $imgHeight   = 0;
            $imgByte     = 0;
            $imgMime     = "";
            if( $isChangeImg == true ){
                $imageInfo = $this->checkImageSize($prdImage);
                $imgWidth  = $imageInfo["width"];
                $imgHeight = $imageInfo["height"];
                $imgByte   = $imageInfo["byte"];
                $imgMime   = $imageInfo["mime"];
            }
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgType,
                "lang"           => WConstant::WAPP_EN,
                "is_except"      => $is_except,
                "img_url_origin" => $prdImage,
                "img_url_trans"  => $img_url_trans,
                "isChangeImg"    => $isChangeImg,
                "width"          => $imgWidth,
                "height"         => $imgHeight,
                "byte"           => $imgByte,
                "mime"           => $imgMime
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }
        if( isset($detailEnProduct["productSkuInfos"]) ){
            foreach ($detailEnProduct["productSkuInfos"] as $prdOptions) {
                if( isset($prdOptions["skuAttributes"]) ){

                    $prdImage = "";
                    foreach ($prdOptions["skuAttributes"] as $prdOption) {
                        if( isset($prdOption["skuImageUrl"]) ){
                            $prdImage = $prdOption["skuImageUrl"];
                        }
                    }
                    if( $prdImage == "" ){
                        continue;
                    }

                    $imgType       = ImageConstant::IMAGE_TYPE_SUB;
                    $is_except     = ImageConstant::IS_EXCEPT_N;
                    $img_url_trans = "";
                    $imgObj        = ProductImageData::where([
                        "offer_id"       => $offerId,
                        "img_type"       => $imgType,
                        "img_url_origin" => $prdImage,
                        "lang"           => WConstant::WAPP_EN,
                    ])->first();
                    if( $imgObj != null ){
                        $is_except = $imgObj->is_except;
                        $img_url_trans = $imgObj->img_url_trans;
                    }
                    if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                        continue;
                    }
    
                    $isChangeImg = $this->isChangeImage($offerId, $prdImage, $imgType);
                    $imgWidth    = 0;
                    $imgHeight   = 0;
                    $imgByte     = 0;
                    $imgMime     = "";
                    if( $isChangeImg == true ){
                        $imageInfo = $this->checkImageSize($prdImage);
                        $imgWidth  = $imageInfo["width"];
                        $imgHeight = $imageInfo["height"];
                        $imgByte   = $imageInfo["byte"];
                        $imgMime   = $imageInfo["mime"];
                    }
                    $product1688ImageDto = new Product1688ImageDto();
                    $product1688ImageDto->bind([
                        "offerId"        => $offerId,
                        "imgType"        => $imgType,
                        "lang"           => WConstant::WAPP_EN,
                        "is_except"      => $is_except,
                        "img_url_origin" => $prdImage,
                        "img_url_trans"  => $img_url_trans,
                        "isChangeImg"    => $isChangeImg,
                        "width"          => $imgWidth,
                        "height"         => $imgHeight,
                        "byte"           => $imgByte,
                        "mime"           => $imgMime
                    ]);
                    $product1688ImageDtoList[] = $product1688ImageDto;
                }
            }
        }

        // 2. 상품 상세 이미지

        // 2-1. 국문 이미지
        $prdDescription = $detailProduct["description"];
        preg_match_all('/<img[^>]+src="([^">]+)"/', $prdDescription, $matches);
        $imageSrcs = $matches[1];
        foreach ($imageSrcs as $imageSrc) {
            $imgType       = ImageConstant::IMAGE_TYPE_DESC;
            $is_except     = ImageConstant::IS_EXCEPT_N;
            $img_url_trans = "";
            $imgObj        = ProductImageData::where([
                "offer_id"       => $offerId,
                "img_type"       => $imgType,
                "img_url_origin" => $imageSrc,
                "lang"           => WConstant::WAPP_KR,
            ])->first();
            if( $imgObj != null ){
                $is_except     = $imgObj->is_except;
                $img_url_trans = $imgObj->img_url_trans;
            }
            if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                continue;
            }

            $isChangeImg = $this->isChangeImage($offerId, $imageSrc, $imgType);
            $imgWidth    = 0;
            $imgHeight   = 0;
            $imgByte     = 0;
            $imgMime     = "";
            if( $isChangeImg == true ){
                $imageInfo = $this->checkImageSize($imageSrc);
                $imgWidth  = $imageInfo["width"];
                $imgHeight = $imageInfo["height"];
                $imgByte   = $imageInfo["byte"];
                $imgMime   = $imageInfo["mime"];
            }
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgType,
                "lang"           => WConstant::WAPP_KR,
                "is_except"      => $is_except,
                "img_url_origin" => $imageSrc,
                "img_url_trans"  => $img_url_trans,
                "isChangeImg"    => $isChangeImg,
                "width"          => $imgWidth,
                "height"         => $imgHeight,
                "byte"           => $imgByte,
                "mime"           => $imgMime
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }

        // 2-2. 영문 이미지
        $prdEnDescription = $detailEnProduct["description"];
        preg_match_all('/<img[^>]+src="([^">]+)"/', $prdEnDescription, $matches);
        $imageSrcs = $matches[1];
        foreach ($imageSrcs as $imageSrc) {
            $imgType       = ImageConstant::IMAGE_TYPE_DESC;
            $is_except     = ImageConstant::IS_EXCEPT_N;
            $img_url_trans = "";
            $imgObj        = ProductImageData::where([
                "offer_id"       => $offerId,
                "img_type"       => $imgType,
                "img_url_origin" => $imageSrc,
                "lang"           => WConstant::WAPP_EN,
            ])->first();
            if( $imgObj != null ){
                $is_except     = $imgObj->is_except;
                $img_url_trans = $imgObj->img_url_trans;
            }
            if( $is_except == ImageConstant::IS_EXCEPT_Y ){
                continue;
            }

            $isChangeImg = $this->isChangeImage($offerId, $imageSrc, $imgType);
            $imgWidth    = 0;
            $imgHeight   = 0;
            $imgByte     = 0;
            $imgMime     = "";
            if( $isChangeImg == true ){
                $imageInfo = $this->checkImageSize($imageSrc);
                $imgWidth  = $imageInfo["width"];
                $imgHeight = $imageInfo["height"];
                $imgByte   = $imageInfo["byte"];
                $imgMime   = $imageInfo["mime"];
            }
            $product1688ImageDto = new Product1688ImageDto();
            $product1688ImageDto->bind([
                "offerId"        => $offerId,
                "imgType"        => $imgType,
                "lang"           => WConstant::WAPP_EN,
                "is_except"      => $is_except,
                "img_url_origin" => $imageSrc,
                "img_url_trans"  => $img_url_trans,
                "isChangeImg"    => $isChangeImg,
                "width"          => $imgWidth,
                "height"         => $imgHeight,
                "byte"           => $imgByte,
                "mime"           => $imgMime
            ]);
            $product1688ImageDtoList[] = $product1688ImageDto;
        }

        // 3. 상품 기본정보
        $mapping_status = ProductConstant::MAPPING_STATUS_N;
        $getCategoryMappingObj = CategoryMapping::select(["mapping_code"])
        ->where([
            "mapping_channel" => ProductConstant::MAPPING_WAPP,
            "category_id"     => $prdCategoryId,
        ])->first();
        if( $getCategoryMappingObj != null ){
            $mapping_status = ProductConstant::MAPPING_STATUS_Y;
        }

        $inspect_status = ProductConstant::INSPECT_STATUS_N;
        if( $prdObj != null ){
            $inspect_status = $prdObj->inspect_status;
        }

        $startQuantity = $detailProduct["productSaleInfo"]["priceRangeList"][0]["startQuantity"];

        $subjectTrans = $detailProduct["subjectTrans"];

        // 3-1. 삭제어
        $subjectForbiddenTrans = $this->removeForbiddenText($deletePrdForbiddenWords, $subjectTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);
        // 3-2. 교체어
        $subjectForbiddenTrans = $this->replaceForbiddenText($replacePrdForbiddenWords, $subjectForbiddenTrans, ForbiddenWordConstant::KEYWORD_APPLY_TITLE);

        $subjectForbiddenTrans = trim($subjectForbiddenTrans);
        $subjectForbiddenTrans = removeDuplicateWords($subjectForbiddenTrans);

        if( $subjectTrans != $subjectForbiddenTrans ){ 
            ProductForbiddenData::updateOrCreate(
                [
                    "offer_id"   => $offerId,
                    "apply_type" => ForbiddenWordConstant::KEYWORD_APPLY_TITLE
                ],
                [
                    "origin_text" => $subjectTrans,
                    "trans_text"  => $subjectForbiddenTrans
                ]
            );
        }

        $soldOut = 0;
        if( isset($detailProduct["soldOut"]) ){
            $soldOut = (int)$detailProduct["soldOut"];
        }

        $prdDescKr = "";
        $prdDescEn = "";
        if( $prdObj != null ){
            $prdDescKr = $prdObj->prd_desc_kr;
            $prdDescEn = $prdObj->prd_desc_en;
        }

        $product1688Dto = new Product1688Dto();
        $product1688Dto->bind([
            "offerId"        => $offerId,
            "categoryId"     => $prdCategoryId,
            "status"         => $status,
            "wType"          => WConstant::WAPP_W1,
            "subject"        => $detailProduct["subject"],
            "subjectTrans"   => $subjectForbiddenTrans,
            "subjectTransEn" => $detailEnProduct["subjectTrans"],
            "startQuantity"  => $startQuantity,
            "description"    => $prdDescription,
            "enDescription"  => $prdEnDescription,
            "prdDescKr"      => $prdDescKr,
            "prdDescEn"      => $prdDescEn,
            "soldOut"        => $soldOut,
            "mapping_status" => $mapping_status,
            "inspect_status" => $inspect_status,
        ]);

        // 4. 상품 확장정보
        $product1688ExtendDto = new Product1688ExtendDto();
        $product1688ExtendDto->bind([
            "offerId" => $offerId,
        ]);

        // 5. 상품 고시정보
        $product1688NoticeDtoList = [];
        foreach ($detailProduct["productAttribute"] as $prdNotice) {
            $is_except = GosiConstants::IS_EXCEPT_N;

            $exceptObj = ProductExceptData::where([
                "except_type"  => ExceptConstant::EXCEPT_NOTICE,
                "attribute_id" => $prdNotice["attributeId"],
            ])->first();
            if( $exceptObj != null ){
                $is_except = $exceptObj->is_except;
            }

            $gosiObj = ProductNoticeData::where([
                "offer_id"     => $offerId,
                "attribute_id" => $prdNotice["attributeId"]
            ])->first();
            if( $gosiObj != null ){
                $is_except = $gosiObj->is_except;
            }

            $prdNoticeEn = null;
            foreach ($detailEnProduct["productAttribute"] as $enNotice) {
                if( $prdNotice["attributeId"] == $enNotice["attributeId"] ){
                    $prdNoticeEn = $enNotice;
                }
            }

            $attributeNameTrans = $prdNotice["attributeNameTrans"];
            $attrValueTrans     = trim($prdNotice["valueTrans"]);

            if( $attrValueTrans == "" ){
                $is_except = GosiConstants::IS_EXCEPT_Y;
            }

            $cnNotice = WNoticeData::where([
                'attribute_id'    => $prdNotice['attributeId'],
                'lang'            => Constant1688::LANGUAGE_CN,
                'attribute_name'  => $prdNotice['attributeName'],
            ])->first();
            if ( $cnNotice == null ) {
                WNoticeData::create([
                    'attribute_id'         => $prdNotice['attributeId'],
                    'lang'                 => Constant1688::LANGUAGE_CN,
                    'attribute_name'       => $prdNotice['attributeName'],
                    'apply_attribute_name' => "",
                ]);
            }
            $krNotice = WNoticeData::where([
                'attribute_id'    => $prdNotice['attributeId'],
                'lang'            => Constant1688::LANGUAGE_KR,
                'attribute_name'  => $attributeNameTrans,
            ])->first();
            if ( $krNotice == null ) {
                WNoticeData::create([
                    'attribute_id'         => $prdNotice['attributeId'],
                    'lang'                 => Constant1688::LANGUAGE_KR,
                    'attribute_name'       => $attributeNameTrans,
                    'apply_attribute_name' => "",
                ]);
            }

            if( $prdNoticeEn != null ){
                $enNotice = WNoticeData::where([
                    'attribute_id'    => $prdNoticeEn['attributeId'],
                    'lang'            => Constant1688::LANGUAGE_EN,
                    'attribute_name'  => $prdNoticeEn["attributeNameTrans"],
                ])->first();
                if ( $enNotice == null ) {
                    WNoticeData::create([
                        'attribute_id'         => $prdNoticeEn['attributeId'],
                        'lang'                 => Constant1688::LANGUAGE_EN,
                        'attribute_name'       => $prdNoticeEn["attributeNameTrans"],
                        'apply_attribute_name' => "",
                    ]);
                }
            }

            if( $krNotice != null && $krNotice->apply_attribute_name != "" ){
                $attributeNameTrans = $krNotice->apply_attribute_name;
            }

            // 고시명 삭제어
            $nameTrans = $this->removeForbiddenText($deleteNoticeForbiddenWords, $attributeNameTrans, ForbiddenWordConstant::KEYWORD_APPLY_ATTR_NAME);
            // 고시명 교체어
            $nameTrans = $this->replaceForbiddenText($replaceNoticeForbiddenWords, $nameTrans, ForbiddenWordConstant::KEYWORD_APPLY_ATTR_NAME);
            $nameTrans = trim($nameTrans);
            if( $attributeNameTrans != $nameTrans ){ 
                ProductForbiddenData::updateOrCreate(
                    [
                        "offer_id"    => $offerId,
                        "apply_type"  => ForbiddenWordConstant::KEYWORD_APPLY_ATTR_NAME,
                        "origin_text" => $attributeNameTrans,
                    ],
                    [
                        "trans_text"  => $nameTrans
                    ]
                );
            }

            // 고시값 삭제어
            $valueTrans = $this->removeForbiddenText($deleteNoticeForbiddenWords, $attrValueTrans, ForbiddenWordConstant::KEYWORD_APPLY_ATTR_VALUE);
            // 고시값 교체어
            $valueTrans = $this->replaceForbiddenText($replaceNoticeForbiddenWords, $valueTrans, ForbiddenWordConstant::KEYWORD_APPLY_ATTR_VALUE);
            $valueTrans = trim($valueTrans);
            if( $attrValueTrans != $valueTrans ){ 
                ProductForbiddenData::updateOrCreate(
                    [
                        "offer_id"   => $offerId,
                        "apply_type" => ForbiddenWordConstant::KEYWORD_APPLY_ATTR_VALUE,
                        "origin_text" => $attrValueTrans,
                    ],
                    [
                        "trans_text"  => $valueTrans
                    ]
                );
            }

            $product1688NoticeDto = new Product1688NoticeDto();
            $product1688NoticeDto->bind([
                "offerId"              => $offerId,
                "attributeId"          => $prdNotice["attributeId"],
                "is_except"            => $is_except,
                "attributeName"        => $prdNotice["attributeName"],
                "value"                => $prdNotice["value"],
                "attributeNameTrans"   => $nameTrans,
                "valueTrans"           => $valueTrans,
                "attributeNameTransEn" => $prdNoticeEn != null ? $prdNoticeEn["attributeNameTrans"] : "",
                "valueTransEn"         => $prdNoticeEn != null ? $prdNoticeEn["valueTrans"] : "",
            ]);
            $product1688NoticeDtoList[] = $product1688NoticeDto;
        }

        // 6. 상품 옵션정보
        $product1688OptionDtoList = [];

        $price_1688 = 0;
        // 6-1. price 컬럼이 있을 경우
        if( isset($detailProduct["productSkuInfos"][0]["price"]) ){
            foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                if( $prdOptions["price"] > $price_1688 ){
                    $price_1688 = $prdOptions["price"];
                }
            }
        } else if( !isset($detailProduct["productSkuInfos"][0]["price"]) &&
            isset($detailProduct["productSaleInfo"]["priceRangeList"])
        ) {
            $price_1688 = $detailProduct["productSaleInfo"]["priceRangeList"][0]["price"];
        }

        if( $price_1688 == 0 ){
            throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRICE_1688"));
        }

        if( isset($detailProduct["productSkuInfos"]) ){
            foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                $opt_status = ProductConstant::OPTION_SEC_ON_SALE_NUMBER;
                if( $status != ProductConstant::PRD_STATUS_PUBLISH ){
                    $opt_status = ProductConstant::OPTION_SEC_OUT_OF_STOCK_NUMBER;
                }
    
                $prdOptionsEn = null;
                foreach ($detailEnProduct["productSkuInfos"] as $enOpt) {
                    if( $prdOptions["skuId"] == $enOpt["skuId"] ){
                        $prdOptionsEn = $enOpt;
                    }
                }

                $optionName        = "";
                $optionNameTrans   = "";
                $optionNameTransEn = "";
                foreach ($prdOptions["skuAttributes"] as $prdOption) {
                    $optionName      .= $prdOption["value"] .  "_";
                    $optionNameTrans .= $prdOption["valueTrans"] .  "_";

                    $prdOptionEn = null;
                    if( $prdOptionsEn != null ){
                        foreach ($prdOptionsEn["skuAttributes"] as $enSku) {
                            if( $prdOption["attributeId"] == $enSku["attributeId"] ){
                                $prdOptionEn = $enSku;
                            }
                        }
                        if( $prdOptionEn != null ){
                            $optionNameTransEn .= $prdOptionEn["valueTrans"] .  "_";
                        }
                    }
                }

                $width  = 0;
                $length = 0;
                $height = 0;
                $weight = 0;

                if( isset($detailProduct["productShippingInfo"]) ){
                    $productShippingInfo = $detailProduct["productShippingInfo"];
                    if( isset($productShippingInfo["skuShippingInfoList"]) ){
                        $skuShippingInfoList = $productShippingInfo["skuShippingInfoList"];
                        foreach ($skuShippingInfoList as $skuShippingInfo) {
                            if( $skuShippingInfo["skuId"] == $prdOptions["skuId"] ){
                                if( isset($skuShippingInfo["width"]) ) {
                                    $width = $skuShippingInfo["width"];
                                }
                                if( isset($skuShippingInfo["length"]) ) {
                                    $length = $skuShippingInfo["length"];
                                }
                                if( isset($skuShippingInfo["height"]) ) {
                                    $height = $skuShippingInfo["height"];
                                }
                                if( isset($skuShippingInfo["weight"]) ) {
                                    $weight = (int)ceil($skuShippingInfo["weight"] / 1000);
                                }
                            }
                        }
                    } else {
                        if( isset($productShippingInfo["width"]) ) {
                            $width = $productShippingInfo["width"];
                        }
                        if( isset($productShippingInfo["length"]) ) {
                            $length = $productShippingInfo["length"];
                        }
                        if( isset($productShippingInfo["height"]) ) {
                            $height = $productShippingInfo["height"];
                        }
                        if( isset($productShippingInfo["weight"]) ) {
                            $weight = $productShippingInfo["weight"];
                        }
                    }
                }

                $is_except = OptionConstants::IS_EXCEPT_N;
                $optionObj = ProductOptionData::where([
                    "offer_id" => $offerId,
                    "sku_id"   => $prdOptions["skuId"],
                    "spec_id"  => $prdOptions["specId"],
                ])->first();
                if( $optionObj != null ){
                    $is_except = $optionObj->is_except;
                }

                $product1688OptionDto = new Product1688OptionDto();
                $product1688OptionDto->bind([
                    "offerId"           => $offerId,
                    "skuId"             => $prdOptions["skuId"],
                    "specId"            => $prdOptions["specId"],
                    "status"            => $opt_status,
                    "is_except"         => $is_except,
                    "price_1688"        => $price_1688,
                    "optionName"        => rtrim($optionName, "_"),
                    "optionNameTrans"   => rtrim($optionNameTrans, "_"),
                    "optionNameTransEn" => rtrim($optionNameTransEn, "_"),
                    "amountOnSale"      => $prdOptions["amountOnSale"],
                    "cargoNumber"       => $prdOptions["cargoNumber"] ?? "",
                    "width"             => (float) sprintf("%.2f", $width),
                    "length"            => (float) sprintf("%.2f", $length),
                    "height"            => (float) sprintf("%.2f", $height),
                    "weight"            => (float) sprintf("%.2f", $weight),
                ]);
                $product1688OptionDtoList[] = $product1688OptionDto;
            }
        }

        return [
            "product1688Dto"           => $product1688Dto,
            "product1688ImageDtoList"  => $product1688ImageDtoList,
            "product1688ExtendDto"     => $product1688ExtendDto,
            "product1688NoticeDtoList" => $product1688NoticeDtoList,
            "product1688OptionDtoList" => $product1688OptionDtoList,
        ];
    }

    public function save1688ProductData(
        Product1688Dto $product1688Dto, Product1688ExtendDto $product1688ExtendDto, array $product1688ImageDtoList,
        array $product1688NoticeDtoList, array $product1688OptionDtoList): array
    {
        $returnMsg = helpers_fail_message();
        try {
            $offerId = (int)$product1688Dto->offer_id;

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

            chkTransStatus($offerId);
            
            // 7. 이미지 번역 요청 통신
            if( env("APP_ENV", "local") == "production" && $product1688Dto->status == ProductConstant::PRD_STATUS_PUBLISH ) {
                // $transResult = $this->transApiAbstract->createTransProductImg($product1688ImageDtoList, $offerId);
                // if( $transResult["isSuccess"] == false ){
                //     throw new Exception($transResult["msg"]);
                // }
            }

            /** 중량 여부로 판매 상태 업데이트 */
            upPrdStatusByWeight($offerId);

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

        $prdObj = ProductData::where("offer_id", $offerId)->first();

        foreach ($product1688ImageDtoList as $product1688ImageDto) {
            $offerId = $product1688ImageDto->offer_id;
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
                ->where('is_except', ImageConstant::IS_EXCEPT_N)
                ->where('lang', WConstant::WAPP_KR)
                ->where('img_url_origin', '!=', $mainImg)
                ->delete();
            }
            if( $mainEnImg ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_MAIN)
                ->where('offer_id', $offerId)
                ->where('is_except', ImageConstant::IS_EXCEPT_N)
                ->where('lang', WConstant::WAPP_EN)
                ->where('img_url_origin', '!=', $mainEnImg)
                ->delete();
            }

            // 2. 서브 이미지 삭제
            if( !empty($subImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                ->where('offer_id', $offerId)
                ->where('is_except', ImageConstant::IS_EXCEPT_N)
                ->where('lang', WConstant::WAPP_KR)
                ->whereNotIn('img_url_origin', $subImgs)
                ->delete();
            }
            if( !empty($subEnImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_SUB)
                ->where('offer_id', $offerId)
                ->where('is_except', ImageConstant::IS_EXCEPT_N)
                ->where('lang', WConstant::WAPP_EN)
                ->whereNotIn('img_url_origin', $subEnImgs)
                ->delete();
            }

            // 3. 상세 이미지 삭제
            if( !empty($descImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                ->where('offer_id', $offerId)
                ->where('is_except', ImageConstant::IS_EXCEPT_N)
                ->where('lang', WConstant::WAPP_KR)
                ->whereNotIn('img_url_origin', $descImgs)
                ->delete();
            }
            if( !empty($descEnImgs) ){
                ProductImageData::where('img_type', ImageConstant::IMAGE_TYPE_DESC)
                ->where('offer_id', $offerId)
                ->where('is_except', ImageConstant::IS_EXCEPT_N)
                ->where('lang', WConstant::WAPP_EN)
                ->whereNotIn('img_url_origin', $descEnImgs)
                ->delete();
            }
        }
    }

    public function isChangeImage(int $offerId, string $imagePath, string $imgType): bool
    {
        return true;
        $isChange = false;

        $getOriginImgObj = ProductImageDetailData::where("offer_id", $offerId)
        ->where("img_type", $imgType)
        ->where("img_url_origin", $imagePath)->first();

        if( $getOriginImgObj == null ){
            $isChange = true;
        } else {
            $imageInfo = $this->checkImageSize($imagePath);
            $imgWidth  = $imageInfo["width"];
            $imgHeight = $imageInfo["height"];
            $imgByte   = $imageInfo["byte"];
            $imgMime   = $imageInfo["mime"];

            if( $getOriginImgObj->width != $imgWidth || $getOriginImgObj->height != $imgHeight
            && $getOriginImgObj->byte != $imgByte && $getOriginImgObj->mime != $imgMime){
                $isChange = true;
            }
        }

        return $isChange;
    }

    public function checkImageSize(string $imagePath): array
    {
        // try {
        //     $imageInfo = getimagesize($imagePath);
        // } catch (Exception $e) {
        //     $errorMsg = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_CHECK_IMG_SIZE") . " {$imagePath}" . " | error: " . $e->getMessage();
        //     throw new UnexpectedValueException($errorMsg);
        // }

        // return [
        //     "width"  => $imageInfo[0],
        //     "height" => $imageInfo[1],
        //     "byte"   => $imageInfo["bits"],
        //     "mime"   => $imageInfo["mime"],
        // ];

        return [
            "width"  => 800,
            "height" => 800,
            "byte"   => 8,
            "mime"   => "image/jpeg",
        ];
    }

    public function getQueryProductDetail(array $offerIds): array
    {
        $datas = [];
        foreach ($offerIds as $offerId) {
            try {
                $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                $payload        = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_KO,
                    ]
                ];
                $apiDatas = curl_1688("POST", $endPoint, $payload);
                if( $apiDatas["isSuccess"] == true && isset($apiDatas["data"]["result"]["result"]) ){
                    $detailData               = $apiDatas["data"]["result"]["result"];
                    $price_1688               = getPrice1688($detailData);
                    $detailData["price_1688"] = $price_1688;
                    $datas[]                  = $detailData;
                }
            } catch (Exception $e) {
            }
        }

        foreach ($datas as &$data) {
            $ocPrice                 = ocPrice($data["price_1688"]);
            $data["onch_price"]      = $ocPrice["onch_price"];
            $data["option_price"]    = $ocPrice["option_price"];
            $data["cus_price"]       = $ocPrice["cus_price"];
            $data["recom_cus_price"] = $ocPrice["recom_cus_price"];
            $data["hasPrd"]          = ProductConstant::HAS_PRD_N;
            $prdCnt                  = ProductData::where("offer_id", $data["offerId"])->count();
            if( $prdCnt > 0 ){
                $data["hasPrd"] = ProductConstant::HAS_PRD_Y;
            }
        }

        return $datas;
    }

    public function getKeywordQuery(array $params): array
    {
        $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";
        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $payload = [
            'access_token'    => $this->accessToken,
            'offerQueryParam' => [
                'sort'       => json_encode($sort),
                'beginPage'  => $params["page"],
                'pageSize'   => $params["pageSize"],
                'country'    => Constant1688::LANGUAGE_KO,
            ]
        ];
        if( !empty($params["search_cls"]) && !empty($params["keyword"]) ){
            $payload["offerQueryParam"][$params["search_cls"]] = $params["keyword"];
        }

        $apiDatas = curl_1688("POST", $endPoint, $payload);

        $resultData   = $apiDatas["data"]["result"]["result"];
        $datas        = $resultData["data"] ?? [];
        $totalRecords = $resultData["totalRecords"];
        $totalPage    = $resultData["totalPage"];
        foreach ($datas as &$data) {
            $ocPrice                 = ocPrice((float)$data["priceInfo"]["price"]);
            $data["price_1688"]      = (float)$data["priceInfo"]["price"];
            $data["onch_price"]      = $ocPrice["onch_price"];
            $data["option_price"]    = $ocPrice["option_price"];
            $data["cus_price"]       = $ocPrice["cus_price"];
            $data["recom_cus_price"] = $ocPrice["recom_cus_price"];
            $data["hasPrd"]          = ProductConstant::HAS_PRD_N;
            $prdCnt                  = ProductData::where("offer_id", $data["offerId"])->count();
            if( $prdCnt > 0 ){
                $data["hasPrd"] = ProductConstant::HAS_PRD_Y;
            }
        }

        return [
            "datas"        => $datas,
            "totalRecords" => $totalRecords,
            "totalPage"    => $totalPage,
            "payload"      => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ];
    }

    public function saveKeywordQuery(array $params): array
    {
        $returnMsg = $this->returnMsg;

        $params_json = json_encode($params, JSON_UNESCAPED_UNICODE);
        $msg = "======================== 실행 시작 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll");

        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $page     = $params["page"];
        $pageSize = $params["pageSize"];
        $payload  = [
            'access_token'    => $this->accessToken,
            'offerQueryParam' => [
                'sort'      => json_encode($sort),
                'country'   => Constant1688::LANGUAGE_KO,
            ]
        ];
        if( !empty($params["search_cls"]) && !empty($params["keyword"]) ){
            $payload["offerQueryParam"][$params["search_cls"]] = $params["keyword"];
        }

        try {
            $payload_json = json_encode($payload["offerQueryParam"], JSON_UNESCAPED_UNICODE);
            $logId = ProductCollectLog::insertGetId([
                "type"       => LogConstant::COLLECT_API_KEYWORDQUERY_ALL,
                "status"     => LogConstant::COLLECT_RUNNING,
                "payload"    => $payload_json,
                "log_count"  => 0,
                "version"    => WConstant::WAPP_W1,
                "created_at" => Carbon::now()
            ]);

            $this->saveKeywordQueryRecursively($logId, $payload, $page, $pageSize);
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (params_json: {$params_json}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll", LogLevel::ERROR);
        }

        $log_count = ProductCollectDetailLog::where([
            "log_id" => $logId
        ])->count();
        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $log_count,
            "completed_at" => Carbon::now()
        ]);

        $msg = "======================== 실행 종료 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll");

        return $returnMsg;
    }

    public function saveKeywordQueryRecursively(int $logId, array $payload, int $page, int $pageSize, int $totalPage = 0): void
    {
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.keywordQuery/";

            $payload["offerQueryParam"]["beginPage"] = $page;
            $payload["offerQueryParam"]["pageSize"]  = $pageSize;

            $payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $errorMsg     = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_KEYWORDQUERY") . " | payload: {$payload_json}";

            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != true ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                foreach ($productDatas as $productData) {
                    try {
                        $offerId = $productData["offerId"];

                        /** 1. W2로 수집 시도 */
                        // $resultW2 = $this->productW2->collectProductNotLog($offerId);
                        $resultW2["isSuccess"] = false;

                        /** 2. W2로 수집 실패 시 W1으로 수집 시도 */
                        if( $resultW2["isSuccess"] != true ){
                            $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                            $payload_detail = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_KO,
                                ]
                            ];
                            $detailResult = curl_1688("POST", $endPoint, $payload_detail);
                            if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                            }

                            $payload_detail = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_EN,
                                ]
                            ];
                            $detailEnResult = curl_1688("POST", $endPoint, $payload_detail);
                            if( $detailEnResult["isSuccess"] != true || $detailEnResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("SEARCH_QUERYPRODUCTDETAIL_EN"));
                            }

                            $prdDto                   = $this->get1688ProductDto($detailResult, $detailEnResult);
                            $product1688Dto           = $prdDto["product1688Dto"];
                            $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                            $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                            $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                            $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                            $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                            if( $saveResult["isSuccess"] != true ){
                                throw new Exception($saveResult["msg"]);
                            }
                        }

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                            "msg"        => ""
                        ]);
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId}";
                        debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll", LogLevel::ERROR);

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_N,
                            "msg"        => $de->getMessage()
                        ]);
                    }
                }
            } else {
                throw new Exception($errorMsg);
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveKeywordQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "collectProduct/keywordQueryAll", "keywordQueryAll", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveKeywordQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function createImgId(UploadedFile $file): array
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

            if( $apiDatas["isSuccess"] != true || $apiDatas["data"]["result"]["success"] != "true" || !isset($apiDatas["data"]["result"]["result"]) ){
                throw new Exception(ImageErrorMessageConstant::getNotHaveErrorMessage("IMG_ID"));
            }

            $returnMsg = helpers_success_message($apiDatas["data"]["result"]);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getImageQuery(array $params): array
    {
        $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.imageQuery/";
        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $payload = [
            'access_token'    => $this->accessToken,
            'offerQueryParam' => [
                'imageId'    => $params["imageId"],
                'sort'       => json_encode($sort),
                'beginPage'  => $params["page"],
                'pageSize'   => $params["pageSize"],
                'country'    => Constant1688::LANGUAGE_KO,
            ]
        ];
        if( !empty($params["search_cls"]) && !empty($params["keyword"]) ){
            $payload["offerQueryParam"][$params["search_cls"]] = $params["keyword"];
        }

        $apiDatas = curl_1688("POST", $endPoint, $payload);

        $resultData   = $apiDatas["data"]["result"]["result"];
        $datas        = $resultData["data"] ?? [];
        $totalRecords = $resultData["totalRecords"];
        $totalPage    = $resultData["totalPage"];
        foreach ($datas as &$data) {
            $ocPrice                 = ocPrice((float)$data["priceInfo"]["price"]);
            $data["price_1688"]      = (float)$data["priceInfo"]["price"];
            $data["onch_price"]      = $ocPrice["onch_price"];
            $data["option_price"]    = $ocPrice["option_price"];
            $data["cus_price"]       = $ocPrice["cus_price"];
            $data["recom_cus_price"] = $ocPrice["recom_cus_price"];
            $data["hasPrd"]          = ProductConstant::HAS_PRD_N;
            $prdCnt                  = ProductData::where("offer_id", $data["offerId"])->count();
            if( $prdCnt > 0 ){
                $data["hasPrd"] = ProductConstant::HAS_PRD_Y;
            }
        }

        return [
            "datas"        => $datas,
            "totalRecords" => $totalRecords,
            "totalPage"    => $totalPage,
            "payload"      => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ];
    }

    public function saveImageQuery(array $params): array
    {
        $returnMsg = $this->returnMsg;

        $params_json = json_encode($params, JSON_UNESCAPED_UNICODE);
        $msg = "======================== 실행 시작 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll");

        $sortArr = explode("|", $params["sort"]);
        $sort = [
            $sortArr[0] => $sortArr[1]
        ];
        $page     = $params["page"];
        $pageSize = $params["pageSize"];
        $imageIds = $params["imageIds"];

        try {
            $logId = ProductCollectLog::insertGetId([
                "type"       => LogConstant::COLLECT_API_IMAGEQUERY_ALL,
                "status"     => LogConstant::COLLECT_RUNNING,
                "payload"    => implode(",", $imageIds),
                "log_count"  => 0,
                "version"    => WConstant::WAPP_W1,
                "created_at" => Carbon::now()
            ]);

            foreach ($imageIds as $imageId) {
                $payload  = [
                    'access_token'    => $this->accessToken,
                    'offerQueryParam' => [
                        'sort'      => json_encode($sort),
                        'country'   => Constant1688::LANGUAGE_KO,
                        'imageId'   => $imageId
                    ]
                ];

                $this->saveImageQueryRecursively($logId, $payload, $page, $pageSize);
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 (params_json: {$params_json}) ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll", LogLevel::ERROR);
        }

        $log_count = ProductCollectDetailLog::where([
            "log_id" => $logId
        ])->count();
        ProductCollectLog::where("id", $logId)->update([
            "status"       => LogConstant::COLLECT_COMPLETE,
            "log_count"    => $log_count,
            "completed_at" => Carbon::now()
        ]);

        $msg = "======================== 실행 종료 (params_json: {$params_json}) ========================";
        debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll");

        return $returnMsg;
    }

    public function saveImageQueryRecursively(int $logId, array $payload, int $page, int $pageSize, int $totalPage = 0): void
    {
        try {
            $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.imageQuery/";

            $payload["offerQueryParam"]["beginPage"] = $page;
            $payload["offerQueryParam"]["pageSize"]  = $pageSize;

            $payload_json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $errorMsg     = ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_IMAGEQUERY") . " | payload: {$payload_json}";

            $apiDatas = curl_1688("POST", $endPoint, $payload);
            if( $apiDatas["isSuccess"] != true ){
                throw new Exception($apiDatas["msg"] . " | " . $errorMsg);
            }
            if( $apiDatas["data"]["result"]["success"] != true ){
                throw new Exception($errorMsg);
            }

            $apiResult = $apiDatas["data"]["result"]["result"];
            if( isset($apiResult["data"]) ){
                $productDatas = $apiResult["data"];
                foreach ($productDatas as $productData) {
                    try {
                        $offerId = $productData["offerId"];

                        /** 1. W2로 수집 시도 */
                        // $resultW2 = $this->productW2->collectProductNotLog($offerId);
                        $resultW2["isSuccess"] = false;

                        /** 2. W2로 수집 실패 시 W1으로 수집 시도 */
                        if( $resultW2["isSuccess"] != true ){
                            $endPoint       = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                            $payload_detail = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_KO,
                                ]
                            ];
                            $detailResult = curl_1688("POST", $endPoint, $payload_detail);
                            if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                            }

                            $payload_detail = [
                                'access_token'     => $this->accessToken,
                                'offerDetailParam' => [
                                    'offerId' => $offerId,
                                    'country' => Constant1688::LANGUAGE_EN,
                                ]
                            ];
                            $detailEnResult = curl_1688("POST", $endPoint, $payload_detail);
                            if( $detailEnResult["isSuccess"] != true || $detailEnResult["data"]["result"]["success"] != true ){
                                throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("SEARCH_QUERYPRODUCTDETAIL_EN"));
                            }

                            $prdDto                   = $this->get1688ProductDto($detailResult, $detailEnResult);
                            $product1688Dto           = $prdDto["product1688Dto"];
                            $product1688ExtendDto     = $prdDto["product1688ExtendDto"];
                            $product1688ImageDtoList  = $prdDto["product1688ImageDtoList"];
                            $product1688NoticeDtoList = $prdDto["product1688NoticeDtoList"];
                            $product1688OptionDtoList = $prdDto["product1688OptionDtoList"];

                            $saveResult = $this->save1688ProductData($product1688Dto, $product1688ExtendDto, $product1688ImageDtoList, $product1688NoticeDtoList, $product1688OptionDtoList);

                            if( $saveResult["isSuccess"] != true ){
                                throw new Exception($saveResult["msg"]);
                            }
                        }

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_Y,
                            "msg"        => ""
                        ]);
                    } catch (Exception $de) {
                        $msg = $de->getMessage() . " | page: {$page} | offerId: {$offerId}";
                        debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll", LogLevel::ERROR);

                        ProductCollectDetailLog::create([
                            "log_id"     => $logId,
                            "offer_id"   => $offerId,
                            "is_collect" => LogConstant::COLLECT_DETAIL_N,
                            "msg"        => $de->getMessage()
                        ]);
                    }
                }
            } else {
                throw new Exception($errorMsg);
            }

            $totalPage = $apiDatas["data"]["result"]["result"]["totalPage"];
            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveImageQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            debug_log($msg, "collectProduct/imageQueryAll", "imageQueryAll", LogLevel::ERROR);

            if( $page < $totalPage ){
                $nextPage = $page + 1;
                $this->saveImageQueryRecursively($logId, $payload, $nextPage, $pageSize, $totalPage);
            }
        }
    }

    public function getUrlQuery(array $params): LengthAwarePaginator
    {
        $pageSize  = $params["pageSize"];

        $prdBuilder = ProductSearchData::orderBy("created_at", "desc");
        $lists = $prdBuilder->paginate($pageSize)->appends($params);

        return $lists;
    }

    public function urlQueryDetail(int $searchId): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $searchObjs = ProductSearchData::with(["details"])->where("id", $searchId)->orderBy("created_at", "desc")->first();
            if( $searchObjs != null ){
                foreach ($searchObjs->details as &$detail) {
                    $prdCnt = ProductData::where("offer_id", $detail->offer_id)->count();
                    if( $prdCnt > 0 ){
                        $detail->hasPrd = ProductConstant::HAS_PRD_Y;
                    } else {
                        $detail->hasPrd = ProductConstant::HAS_PRD_N;
                    }
                }
            }
            $returnMsg  = helpers_success_message($searchObjs);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function productsUpdateImages(int $offerId, array $images): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $resultImgs = [];
            $prdObj     = ProductData::where("offer_id", $offerId)->first();

            if( $prdObj == null ){
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT"));
            }

            $prd_desc = $prdObj->prd_desc;
            $dateName = $prdObj->created_at->format('Y/m/d');

            $descTransImgs = [];
            foreach ($images as $image) {
                $imgId     = $image["id"];
                try {
                    $imgObj = ProductImageData::where([
                        "id"       => $imgId,
                        "offer_id" => $offerId
                    ])->first();
                    if( $imgObj == null ){
                        throw new ValueError(ImageErrorMessageConstant::getNotHaveErrorMessage("IMAGE"));
                    }

                    $img_url_origin = $imgObj->img_url_origin;
                    $mime           = pathinfo($img_url_origin, PATHINFO_EXTENSION);
                    if (preg_match('/^(jpg|jpeg|png|gif)/i', $mime, $matches)) {
                        $mime = $matches[0];
                    }
                    if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_MAIN ){
                        $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->img_type . "." . $mime;
                    } else {
                        $imgName  = "/product/" . $dateName . "/" . $offerId . "_" . $imgObj->id . "_" . $imgObj->img_type . "." . $mime;
                    }

                    $uploadResult = $this->uploadAbstract->uploadFile($imgName, base64_decode($image["base64"]));

                    if( $uploadResult == true ) {
                        $img_url_trans = env("AWS_URL") . $imgName;
                        ProductImageData::where("id", $imgId)->update([
                            "img_url_trans"  => $img_url_trans,
                            "trans_dated_at" => Carbon::now(),
                        ]);

                        if( $imgObj->img_type == ImageConstant::IMAGE_TYPE_DESC ){
                            $descTransImgs[] = [
                                "img_url_origin" => $img_url_origin,
                                "img_url_trans"  => $img_url_trans
                            ];
                        }
                    } else {
                        throw new ValueError(ImageErrorMessageConstant::getFitErrorMessage("S3_IMG_UPLOAD"));
                    }

                    $resultImgs[] = [
                        "id"        => $imgId,
                        "isSuccess" => true,
                    ];
                } catch (ValueError $ve) {
                    $resultImgs[] = [
                        "id"        => $imgId,
                        "isSuccess" => false,
                        "msg"       => $ve->getMessage()
                    ];
                }
            }

            foreach ($descTransImgs as $descTransImg) {
                $prd_desc_trans = str_replace($descTransImg["img_url_origin"], $descTransImg["img_url_trans"], $prd_desc);
                $prd_desc       = $prd_desc_trans;
                ProductData::where("offer_id", $offerId)->update([
                    "prd_desc_trans" => $prd_desc_trans
                ]);
            }

            $returnMsg = helpers_success_message($resultImgs);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function saveProductSearchData(array $params): void
    {
        $offerIds     = $params["offerIds"];
        $search_title = $params["search_title"];
        $search_type  = $params["search_type"];

        $searchId = ProductSearchData::insertGetId([
            "search_title" => $search_title,
            "search_type"  => $search_type,
            "status"       => ProductConstant::SEARCH_STATUS_R,
            "search_count" => count($offerIds),
            "created_at"   => Carbon::now(),
        ]);

        foreach ($offerIds as $offerId) {
            $offerId        = trim($offerId);
            $prdCategoryId  = 0;
            $prd_name_trans = "";
            $price_1688     = 0;
            $prd_image      = "";
            $sold_out       = 0;

            try {
                $endPoint = "param2/1/com.alibaba.fenxiao.crossborder/product.search.queryProductDetail/";
                $payload  = [
                    'access_token'     => $this->accessToken,
                    'offerDetailParam' => [
                        'offerId' => $offerId,
                        'country' => Constant1688::LANGUAGE_KO,
                    ]
                ];
                $detailResult = curl_1688("POST", $endPoint, $payload);
                if( $detailResult["isSuccess"] != true || $detailResult["data"]["result"]["success"] != true ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRODUCT_SEARCH_QUERYPRODUCTDETAIL"));
                }

                $detailProduct = $detailResult["data"]["result"]["result"];
                $prdCategoryId = $detailProduct["categoryId"];
                $prd_name_trans = $detailProduct["subjectTrans"];
                if( isset($detailProduct["productSkuInfos"][0]["price"]) ){
                    foreach ($detailProduct["productSkuInfos"] as $prdOptions) {
                        if( $prdOptions["price"] > $price_1688 ){
                            $price_1688 = $prdOptions["price"];
                        }
                    }
                } else if( !isset($detailProduct["productSkuInfos"][0]["price"]) &&
                    isset($detailProduct["productSaleInfo"]["priceRangeList"])
                ) {
                    $price_1688 = $detailProduct["productSaleInfo"]["priceRangeList"][0]["price"];
                }
                if( $price_1688 == 0 ){
                    throw new Exception(ProductErrorMessageConstant::getFitErrorMessage("PRICE_1688"));
                }

                if( count($detailProduct["productImage"]["images"]) < 5 ) {
                    throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("PRODUCT_MAIN_IMG"));
                }
                $prd_image = $detailProduct["productImage"]["images"][4];

                if( isset($detailProduct["soldOut"]) ){
                    $sold_out = (int)$detailProduct["soldOut"];
                }

                ProductSearchDetailData::create([
                    "search_id"      => $searchId,
                    "offer_id"       => $offerId,
                    "category_id"    => $prdCategoryId,
                    "prd_name_trans" => $prd_name_trans,
                    "price_1688"     => $price_1688,
                    "prd_image"      => $prd_image,
                    "sold_out"       => $sold_out,
                    "is_search"      => ProductConstant::IS_SEARCH_Y,
                    "msg"            => ""
                ]);

            } catch (Exception $e) {
                ProductSearchDetailData::create([
                    "search_id"      => $searchId,
                    "offer_id"       => $offerId,
                    "category_id"    => $prdCategoryId,
                    "prd_name_trans" => $prd_name_trans,
                    "price_1688"     => $price_1688,
                    "prd_image"      => $prd_image,
                    "sold_out"       => $sold_out,
                    "is_search"      => ProductConstant::IS_SEARCH_N,
                    "msg"            => $e->getMessage()
                ]);
            }
        }

        ProductSearchData::where("id", $searchId)->update([
            "status"       => ProductConstant::SEARCH_STATUS_C,
            "completed_at" => Carbon::now()
        ]);
    }

    public function urlQueryDel(array $ids): array
    {
        $returnMsg = $this->returnMsg;

        try {
            foreach ($ids as $id) {
                ProductSearchData::where("id", $id)->forceDelete();
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function getPrdImageEdit(int $offerId):array
    {
        $returnMsg = $this->returnMsg;

        try {
            $imgObjs = ProductImageData::where([
                "offer_id" => $offerId,
                "lang"     => WConstant::WAPP_KR,
            ])
            ->where("img_url_trans", "!=", "")->get();
            foreach ($imgObjs as $imgObj) {
                $gObj = GenuioImageData::where([
                    "offer_id" => $offerId,
                    "img_id"   => $imgObj->id,
                    "ai_type"  => GenuioConstant::IMG_Ai_TRANS,
                ])->count();
                if( $gObj < 1 ){
                    GenuioImageData::insert([
                        [
                            "offer_id"   => $offerId,
                            "img_id"     => $imgObj->id,
                            "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                            "is_origin"  => GenuioConstant::IS_ORIGIN_Y,
                            "img_url_ai" => $imgObj->img_url_origin,
                            "created_at" => $imgObj->created_at,
                        ],
                        [
                            "offer_id"   => $offerId,
                            "img_id"     => $imgObj->id,
                            "ai_type"    => GenuioConstant::IMG_Ai_TRANS,
                            "is_origin"  => GenuioConstant::IS_ORIGIN_N,
                            "img_url_ai" => $imgObj->img_url_trans,
                            "created_at" => $imgObj->trans_dated_at,
                        ],
                    ]);
                }
            }

            $prdObj = ProductData::with([
                "main_img.ai_origin_img",
                "main_img.ai_imgs",
                "sub_imgs.ai_origin_img",
                "sub_imgs.ai_imgs",
                "desc_imgs.ai_origin_img",
                "desc_imgs.ai_imgs",
            ])->where("offer_id", $offerId)->first();
            if( $prdObj == null ){
                throw new Exception("No Data");
            }
            // dd($prdObj->toArray());
            $returnMsg = helpers_success_message($prdObj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function wAppProductMapping(): void
    {
        $msg = "======================== 실행 시작 ========================";
        debug_log($msg, "wAppProductMapping", "wAppProductMapping");

        try {
            //1. category_mappings upsert
            $wObjs = WCategory::where("category_id", "!=", 0)->get();
            foreach ($wObjs as $wObj) {
                $category_id = $wObj->category_id;
                $upsertWhere = [
                    "mapping_chaneel" => ProductConstant::MAPPING_WAPP,
                    "mapping_code"    => $wObj->mapping_code,
                ];
                CategoryMapping::updateOrCreate(
                    ["category_id" => $category_id],
                    $upsertWhere
                );
            }

            $prdObjs = ProductData::where()->get();
            foreach ($prdObjs as $prdObj) {
                $mapping_status = ProductConstant::MAPPING_STATUS_N;
                $cateObj = CategoryMapping::where([
                    "mapping_channel" => ProductConstant::MAPPING_WAPP,
                    "category_id"     => $prdObj->category_id,
                ])->first();

                if( $cateObj != null ){
                    $mapping_status = ProductConstant::MAPPING_STATUS_Y;
                }

                ProductData::where("id", $prdObj->id)->update([
                    "mapping_status" => $mapping_status
                ]);
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= $e->getMessage();
            debug_log($msg, "wAppProductMapping", "wAppProductMapping", LogLevel::ERROR);
        }

        $msg = "======================== 실행 종료 ========================";
        debug_log($msg, "wAppProductMapping", "wAppProductMapping");
    }

    public function imageExcept(array $imgIds, string $is_except): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($imgIds as $imgId) {
                $imgObj = ProductImageData::where("id", $imgId)->first();
                if( $imgObj != null && $imgObj->img_type != ImageConstant::IMAGE_TYPE_MAIN ){
                    ProductImageData::where("id", $imgId)->update([
                        "is_except" => $is_except
                    ]);
                    // 상세이미지 업데이트
                    upPrdDescTrans($imgObj->offer_id);
                    
                    // 수정 상품 저장
                    saveModiProduct($imgObj->offer_id);
                }
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function imageAccept(array $aiImgIds): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($aiImgIds as $aiImgId) {
                $aiImgObj = GenuioImageData::with(["image"])->where("id", $aiImgId)->first();
                if( $aiImgObj != null && $aiImgObj->image ){
                    $imgObj = $aiImgObj->image;
                    if( $imgObj->img_url_trans ){
                        ProductImageData::where("id", $imgObj->id)->update([
                            "img_url_trans" => $aiImgObj->img_url_ai
                        ]);

                        // 상세이미지 업데이트
                        upPrdDescTrans($imgObj->offer_id);

                        // 수정 상품 저장
                        saveModiProduct($imgObj->offer_id);
                    }
                }
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function mdPriceUpdate(array $offerIds, int $mdPrice): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($offerIds as $offerId) {
                ProductOptionData::where("offer_id", $offerId)->update([
                    "md_price" => $mdPrice
                ]);

                // 수정 상품 저장
                saveModiProduct($offerId);
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function statusUpdate(array $offerIds, string $status): array
    {
        $returnMsg = $this->returnMsg;
        try {
            ProductData::whereIn("offer_id", $offerIds)->update([
                "status" => $status
            ]);

            foreach ($offerIds as $offerId) {
                // 수정 상품 저장
                saveModiProduct($offerId);

                if( $status == ProductConstant::PRD_STATUS_EXCEPT ){
                    $geObj = GenuioQueueData::query()
                    ->select('genuio_queue_datas.*')
                    ->leftJoin('genuio_queue_datas as b', 'genuio_queue_datas.id', '=', 'b.parent_id')
                    ->where('genuio_queue_datas.offer_id', $offerId)
                    ->where('genuio_queue_datas.request_user', TransApiConstant::API_USER_COMPANY_OC)
                    ->whereNull('b.id')
                    ->first();
    
                    if( $geObj != null ){
                        $this->transApiAbstract->removeQueue($geObj->id);
                    }
                }
            }

            $returnMsg = helpers_success_message([], "판매 상태가 변경되었습니다.");
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function imageMainApply(array $aiImgIds): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($aiImgIds as $aiImgId) {
                $aiImgObj = GenuioImageData::with(["image"])->where("id", $aiImgId)->first();
                if( $aiImgObj != null && $aiImgObj->image ){
                    $imgObj = $aiImgObj->image;

                    // 1. 기존 main 이미지 sub로 변경
                    ProductImageData::where([
                        "offer_id" => $imgObj->offer_id,
                        "img_type" => ImageConstant::IMAGE_TYPE_MAIN,
                    ])->update([
                        "img_type" => ImageConstant::IMAGE_TYPE_SUB
                    ]);

                    // 2. 타켓 이미지 main으로 변경
                    ProductImageData::where([
                        "id" => $imgObj->id
                    ])->update([
                        "img_type" => ImageConstant::IMAGE_TYPE_MAIN
                    ]);

                    // 수정 상품 저장
                    saveModiProduct($imgObj->offer_id);
                }
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function gosiExcept(array $gosiList): array
    {
        $returnMsg = $this->returnMsg;
        try {
            foreach ($gosiList as $gosi) {
                ProductNoticeData::where("id", $gosi["id"])->update([
                    "is_except" => $gosi["is_except"]
                ]);

                $gosiObj = ProductNoticeData::where("id", $gosi["id"])->first();

                // 수정 상품 저장
                saveModiProduct($gosiObj->offer_id);
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function update(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            DB::beginTransaction();

            $offerId     = $params["offer_id"];
            $prd_name_kr = trim($params["prd_name_kr"]);
            $prd_name_en = trim($params["prd_name_en"]);
            $optionList  = $params["optionList"];
            $gosiKrList  = $params["gosiKrList"];
            $gosiEnList  = $params["gosiEnList"];

            if( $prd_name_kr && $prd_name_en ){
                ProductData::where("offer_id", $offerId)
                ->update([
                    "prd_name_kr" => $prd_name_kr,
                    "prd_name_en" => $prd_name_en,
                ]);
            }

            foreach ($optionList as $option) {
                $qry = ProductOptionData::where("id", $option["id"]);
                if( $option["is_except"] == OptionConstants::IS_EXCEPT_N ){
                    $qry->update([
                        "is_except"      => $option["is_except"],
                        "option_name_kr" => $option["option_name_kr"],
                        "option_name_en" => $option["option_name_en"],
                    ]);
                } else {
                    $qry->update([
                        "is_except" => $option["is_except"]
                    ]);
                }
            }

            foreach ($gosiKrList as $gosi) {
                ProductNoticeData::where("id", $gosi["id"])
                ->update([
                    "is_except"          => $gosi["is_except"],
                    "attribute_name_kr"  => $gosi["attribute_name_kr"],
                    "attribute_value_kr" => $gosi["attribute_value_kr"],
                ]);
            }

            foreach ($gosiEnList as $gosi) {
                ProductNoticeData::where("id", $gosi["id"])
                ->update([
                    "is_except"          => $gosi["is_except"],
                    "attribute_name_en"  => $gosi["attribute_name_en"],
                    "attribute_value_en" => $gosi["attribute_value_en"],
                ]);
            }

            // 수정 상품 저장
            saveModiProduct($offerId);

            DB::commit();
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            DB::rollBack();
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function inspectStatusUpdate(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            DB::beginTransaction();

            $offerIds            = $params["offerIds"];
            $inspect_img_status  = $params["inspect_img_status"];
            $inspect_prd_status  = $params["inspect_prd_status"];
            $inspect_gosi_status = $params["inspect_gosi_status"];
            
            foreach ($offerIds as $offerId) {
                ProductInspectData::updateOrCreate(
                    [
                        "offer_id"     => $offerId,
                        "inspect_type" => InspectConstant::INSPECT_IMAGE,
                    ],
                    [
                        "is_inspect" => $inspect_img_status,
                    ]
                );

                ProductInspectData::updateOrCreate(
                    [
                        "offer_id"     => $offerId,
                        "inspect_type" => InspectConstant::INSPECT_PRODUCT,
                    ],
                    [
                        "is_inspect" => $inspect_prd_status,
                    ]
                );

                ProductInspectData::updateOrCreate(
                    [
                        "offer_id"     => $offerId,
                        "inspect_type" => InspectConstant::INSPECT_NOTICE,
                    ],
                    [
                        "is_inspect" => $inspect_gosi_status,
                    ]
                );

                /** 검수상태 최종 변경 */
                inspectStatusUpdate($offerId);
            }


            DB::commit();
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            DB::rollBack();
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    public function weightSave(array $offerIds, int $weight): array
    {
        $returnMsg = $this->returnMsg;

        try {

            if( empty($offerIds) ) {
                throw new Exception(ProductErrorMessageConstant::getNotHaveErrorMessage("OFFER_IDS"));
            }

            foreach ($offerIds as $offerId) {
                try {
                    $deliveryPrice = CategoryConstant::WEIGHTS[$weight];
                } catch (Exception $e) {
                    $weight        = 0;
                    $deliveryPrice = CategoryConstant::WEIGHTS[$weight];
                }

                $prdObj = ProductWeightData::where("offer_id", $offerId)->first();

                if( $prdObj != null ){
                    ProductWeightData::where("offer_id", $offerId)->update([
                        'weight'         => $weight,
                        'delivery_price' => $deliveryPrice,
                    ]);
                } else {
                    ProductWeightData::create([
                        'offer_id'       => $offerId,
                        'weight_type'    => ProductConstant::WEIGHT_STATUS_NONE,
                        'weight'         => $weight,
                        'delivery_price' => $deliveryPrice,
                    ]);
                }
            }
            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            DB::rollBack();
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}