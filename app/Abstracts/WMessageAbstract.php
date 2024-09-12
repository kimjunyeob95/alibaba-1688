<?php

namespace App\Abstracts;

use App\Models\WMessageLog;
use Exception;

abstract class WMessageAbstract
{
    protected array $returnMsg;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    /**
    * @func list
    * @description '메시지 리스트'
    * @param array $params
    * @return array
    */
    public function list(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $page         = $params["page"];
            $pageSize     = $params["pageSize"];
            $search_cls   = $params["search_cls"];
            $keyword      = $params["keyword"];
            $code         = $params["code"];
            $channel      = $params["channel"];
            $pubSubIsSend = $params["pubSubIsSend"];

            $code = array_filter($code, function($value) {
                return !empty($value);
            });

            $builder = WMessageLog::with([
                "order_base_obj",
            ]);

            if( !empty($code) ){
                $builder->whereIn("code", $code);
            }
            if( !empty($channel) ){
                $builder->whereHas('order_base_obj', function($query) use ($channel) {
                    $query->where('channel', $channel);
                });
            }
            if( !empty($pubSubIsSend) ){
                $builder->where("pub_sub_is_send", $pubSubIsSend);
            }

            if( !empty($keyword) ){
                $keyword = preg_replace("/(\r\n|\r|\n)/", ",", trim($keyword));
                $keyword = explode(",", $keyword);
                // 각 배열 요소의 앞뒤 공백 제거
                $keyword = array_map('trim', $keyword);
                // 빈 값을 제거
                $keyword = array_filter($keyword);
                // 중복 제거
                $keyword = array_unique($keyword);

                if( $search_cls == "order_id"){
                    $builder->whereIn($search_cls, $keyword);
                } else if( $search_cls == "channel_order_id"){
                    $builder->whereIn($search_cls, $keyword);
                } else if( $search_cls == "code"){
                    $builder->whereIn($search_cls, $keyword);
                }
            }

            $builder->orderBy("created_at", "desc");

            $lists     = $builder->paginate($pageSize)->appends($params);
            $returnMsg = helpers_success_message($lists);

        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func detail
    * @description '메시지 상세'
    * @param int $id
    * @return array
    */
    public function detail(int $id): array
    {
        $returnMsg = $this->returnMsg;

        try {
            
            $obj = WMessageLog::where("id", $id)->first();

            $returnMsg = helpers_success_message($obj);
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func message
    * @description 'W1 메세지 카프카 등록'
    * @param array $params
    * @return array
    */
    abstract function message(array $params): array;

    /**
    * @func taobaoCallback
    * @description '타오바오 콜백'
    * @param array $params
    * @return array
    */
    public function taobaoCallback(array $params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            if( !empty($params) ){
                debug_log(json_encode($params, JSON_UNESCAPED_UNICODE), "taobao/callback", "message");
            }

            $returnMsg = helpers_success_message();
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }
}
