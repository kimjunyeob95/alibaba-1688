<?php

namespace App\Traits;

use Exception;
use App\Abstracts\TransApiAbstract;
use Illuminate\Support\Facades\Log;

trait MallImageTrait
{
    protected array $returnMsg;
    protected string $channel;
    protected TransApiAbstract $transApiImageAbstract;

    public function __construct()
    {
        $this->returnMsg = helpers_fail_message();
    }

    public function initImageTrait(string $channel, TransApiAbstract $transApiImageAbstract): void
    {
        $this->channel               = $channel;
        $this->transApiImageAbstract = $transApiImageAbstract;
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
            $result = $this->transApiImageAbstract->channelImgTransRequest($this->channel, $params);
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
            $result = $this->transApiImageAbstract->channelImgTrans($this->channel, $params);

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
        return $this->transApiImageAbstract->imgUpload($channel, $params);
    }

    /**
     * @func imgCallBack
     * @description '이미지 콜백'
     * @param array $params
     * @return array
    */
    abstract function imgCallBack(array $params): array;
}
