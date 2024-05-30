<?php

namespace App\Traits;

use App\Abstracts\TransApiAbstract;
use Exception;

trait MallImageTrait
{
    protected array $returnMsg;
    protected string $channel;
    protected TransApiAbstract $transApiAbstract;
    
    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }
    
    public function initImageTrait(string $channel, TransApiAbstract $transApiAbstract): void
    {
        $this->channel          = $channel;
        $this->transApiAbstract = $transApiAbstract;
    }

    /**
     * @func imgTransRequest
     * @description '이미지 번역 요청'
     * @param array $params
     * @return array
    */
    public function imgTransRequest($params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $result = $this->transApiAbstract->channelImgTransRequest($this->channel, $params);
            if( $result["isSuccess"] === true && isset($result["data"]) ){
                $returnMsg = helpers_success_message($result["data"]);
            } else {
                $returnMsg = helpers_fail_message($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgTrans
     * @description '번역 된 이미지 처리'
     * @param array $params
     * @return array
    */
    public function imgTrans($params): array
    {
        $returnMsg = $this->returnMsg;

        try {
            $result = $this->transApiAbstract->channelImgTrans($this->channel, $params);

            if( $result["isSuccess"] === true && isset($result["data"]) ){
                $returnMsg = helpers_success_message($result["data"]);
            } else {
                $returnMsg = helpers_fail_message($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
     * @func imgUpload
     * @description '이미지 S3 upload'
     * @param array $params
     * @return array
    */
    public function imgUpload($params): array
    {
        $channel = $this->channel;
        return $this->transApiAbstract->imgUpload($channel, $params);
    }

    /**
     * @func imgCallBack
     * @description '이미지 콜백'
     * @param array $params
     * @return array
    */
    abstract function imgCallBack(array $params): array;
}
