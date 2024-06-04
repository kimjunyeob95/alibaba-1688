<?php

namespace App\Traits\Genuio;

use App\Constants\GenuioConstant;
use App\Constants\MallConstant;
use App\Models\GenuioQueueData;
use App\Models\OcGeQueueData;
use Exception;
use Illuminate\Support\Facades\DB;

trait WebTrait
{
    protected array $returnMsg;
    
    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    /**
     * @func wappQueueList
     * @description 'WApp 큐 리스트'
     * @param array $params
     * @return array
    */
    public function wappQueueList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $pageSize        = $params["pageSize"];
            $send_type       = $params["send_type"];
            $callback_status = $params["callback_status"];
            $search_cls      = $params["search_cls"];
            $keyword         = $params["keyword"];

            $builder = GenuioQueueData::select(["genuio_queue_datas.*", "b.id as child_id", "b.response_json as child_response_json", "b.created_at as child_created_at"])
            ->leftJoin("genuio_queue_datas as b", "genuio_queue_datas.id", "=", "b.parent_id")
            ->where([
                "genuio_queue_datas.request_user" => MallConstant::MALL_ONCHANNEL
            ])->orderBy("genuio_queue_datas.created_at", "desc")
            ->groupBy("genuio_queue_datas.id");

            if( isset($send_type) && $send_type ){
                $builder->where("genuio_queue_datas.send_type", $send_type);
            }

            if( isset($callback_status) ){
                if( $callback_status == GenuioConstant::CALLBACK_Y ){
                    $builder->whereNotNull("b.id");
                } else if( $callback_status == GenuioConstant::CALLBACK_N ){
                    $builder->whereNull("b.id");
                }
            }

            if(isset($search_cls) && !empty($keyword)){
                if($search_cls == "offer_id"){
                    $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                    $keyword = explode(",", $keyword);
                    // 각 배열 요소의 앞뒤 공백 제거
                    $keyword = array_map('trim', $keyword);
                    // 빈 값을 제거
                    $keyword = array_filter($keyword);
                    // 중복 제거
                    $keyword = array_unique($keyword);

                    $builder->whereIn("genuio_queue_datas.offer_id", $keyword);
                }
            }

            $lists = $builder->paginate($pageSize)->appends($params);

            $requestCnt  = GenuioQueueData::where(["request_user" => MallConstant::MALL_ONCHANNEL])->count();
            $responseCnt = GenuioQueueData::query()
            ->join("genuio_queue_datas as b", "genuio_queue_datas.id", "=", "b.parent_id")
            ->where(["genuio_queue_datas.request_user" => MallConstant::MALL_ONCHANNEL])->count();

            return [
                "paginator"   => $lists,
                "requestCnt"  => $requestCnt,
                "responseCnt" => $responseCnt,
            ];

        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }

    /**
     * @func onchannelQueueList
     * @description '온채널 큐 리스트'
     * @param array $params
     * @return array
    */
    public function onchannelQueueList(array $params): array
    {
        $returnMsg = $this->returnMsg;
        try {
            $pageSize        = $params["pageSize"];
            $send_type       = $params["send_type"];
            $callback_status = $params["callback_status"];

            $builder = OcGeQueueData::select(["oc_ge_queue_datas.*", "b.id as child_id", "b.response_json as child_response_json", "b.created_at as child_created_at"])
            ->leftJoin("oc_ge_queue_datas as b", "oc_ge_queue_datas.id", "=", "b.parent_id")
            ->where([
                "oc_ge_queue_datas.request_user" => MallConstant::MALL_ONCHANNEL
            ])->orderBy("oc_ge_queue_datas.created_at", "desc")
            ->groupBy("oc_ge_queue_datas.id");

            if( isset($send_type) && $send_type ){
                $builder->where("oc_ge_queue_datas.send_type", $send_type);
            }

            if( isset($callback_status) ){
                if( $callback_status == GenuioConstant::CALLBACK_Y ){
                    $builder->whereNotNull("b.id");
                } else if( $callback_status == GenuioConstant::CALLBACK_N ){
                    $builder->whereNull("b.id");
                }
            }

            $lists = $builder->paginate($pageSize)->appends($params);

            $requestCnt = OcGeQueueData::where('request_user', MallConstant::MALL_ONCHANNEL)
                ->groupBy('id')
                ->get();

            $responseCnt = OcGeQueueData::join("oc_ge_queue_datas as b", "oc_ge_queue_datas.id", "=", "b.parent_id")
            ->where('oc_ge_queue_datas.request_user', MallConstant::MALL_ONCHANNEL)
            ->groupBy('oc_ge_queue_datas.id')
            ->get();

            return [
                "paginator"   => $lists,
                "requestCnt"  => count($requestCnt),
                "responseCnt" => count($responseCnt),
            ];

        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }
        return $returnMsg;
    }
}
