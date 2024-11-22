<?php

namespace App\Abstracts;

use App\Constants\WmsConstant;
use App\Models\ApiUser;
use App\Packages\Bonaera;
use App\Packages\JwtPackage;
use App\Packages\Slack;
use Carbon\Carbon;
use Exception;
use Throwable;

abstract class WmsAbstract
{
    protected array $returnMsg;
    protected string $unipass_token;
    protected Bonaera $bonaera;
    private JwtPackage $jwtPackage;
    protected Slack $slack;

    public function __construct(Bonaera $bonaera, JwtPackage $jwtPackage, Slack $slack)
    {
        $this->returnMsg     = helpers_fail_message();
        $this->bonaera       = $bonaera;
        $this->jwtPackage    = $jwtPackage;
        $this->slack         = $slack;
        $this->unipass_token = env("UNIPASS_TOKEN", "g230z224g099q163r080k040u0");
    }

    /**
    * @func hsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    abstract function hsCodeList(array $params): array;

    /**
    * @func inList
    * @description '입고관리 라스트'
    * @param array $params
    * @return array
    */
    abstract function inList(array $params): array;

    /**
    * @func inFailList
    * @description '입고 실패 리스트'
    * @param array $params
    * @return array
    */
    abstract function inFailList(array $params): array;

    /**
    * @func apiHsCodeList
    * @description 'HS code 라스트'
    * @param array $params
    * @return array
    */
    public function apiHsCodeList(array $params): array
    {
        $result    = $this->hsCodeList($params);
        $paginator = $result["data"];
        $datas     = [];
        foreach ($paginator as $prdObj) {
           $datas[] = $prdObj;
        }
  
        $lastPage  = $paginator->lastPage();
        $page      = $paginator->currentPage();
        $pageSize  = $paginator->perPage();
  
        return [
           "result"    => $datas,
           "last_page" => $lastPage,
           "page"      => $page,
           "page_size" => (int)$pageSize,
        ];
    }

    /**
    * @func getTariff
    * @description '관세율조회'
    * @param string $hsCode
    * @return array
    */
    abstract function getTariff(string $hsCode): array;

    /**
    * @func bonaeraInHscodeUpdate
    * @description '입고성공 HS code 적용'
    * @param array $ids
    * @param string $hsCode
    * @return array
    */
    abstract function bonaeraInHscodeUpdate(array $ids, string $hsCode): array;

    /**
    * @func bonaeraInFailHscodeUpdate
    * @description '입고실패 HS code 적용'
    * @param array $ids
    * @param string $hsCode
    * @return array
    */
    abstract function bonaeraInFailHscodeUpdate(array $ids, string $hsCode): array;

    /**
    * @func bonaeraInFailCreate
    * @description '입고실패 데이터 입고신청'
    * @param int $id
    * @return void
    */
    abstract function bonaeraInFailCreate(int $id): void;

    /**
    * @func bonaeraCreateStockApi
    * @description '입고신청'
    * @param string $orderId
    * @return void
    */
    public function bonaeraCreateStockApi(string $orderId): void
    {
        try {
            $this->bonaera->createStockApi($orderId);
        } catch (Throwable $e) {
            $msg = "error: " . $e->getMessage(). " | orderId: " . $orderId;
            debug_log($msg, "boneara/bonaeraCreateStockApi", "bonaeraCreateStockApi");
        }
    }

    /**
    * @func bonaeraInUpdate
    * @description '입고정보 업데이트'
    * @param int $id
    * @return void
    */
    abstract function bonaeraInUpdate(int $id): void;

    /**
    * @func bonaeraOutCreate
    * @description '출고신청'
    * @param int $id
    * @return void
    */
    abstract function bonaeraOutCreate(int $id): void;

    /**
    * @func bonaeraOutUpdate
    * @description '출고정보 업데이트'
    * @param int $id
    * @return void
    */
    abstract function bonaeraOutUpdate(int $id): void;

    /**
    * @func bonaeraOutPay
    * @description '출고 배송비 결제'
    * @param int $id
    * @return void
    */
    abstract function bonaeraOutPay(int $id): void;

    /**
    * @func inDetail
    * @description '입고정보 상세'
    * @param string $stockNo
    * @return array
    */
    abstract function inDetail(string $stockNo): array;

    /**
    * @func outSignList
    * @description '출고 신청관리'
    * @param array $params
    * @return array
    */
    abstract function outSignList(array $params): array;

    /**
    * @func outList
    * @description '출고관리 라스트'
    * @param array $params
    * @return array
    */
    abstract function outList(array $params): array;

    /**
    * @func outDetail
    * @description '출고정보 상세'
    * @param string $groupNo
    * @return array
    */
    abstract function outDetail(string $groupNo): array;

    /**
     * @func RequestBonaeraTokenCreate
     * @description '토큰 생성'
     * @param string $userId
     * @return array
    */
    public function RequestBonaeraTokenCreate(string $userId): array
    {
        $returnMsg = $this->returnMsg;
        try {            
            $result = $this->jwtPackage->tokenCreate($userId, WmsConstant::COMPANY_BONAERA);
            if( $result["isSuccess"] && isset($result["data"]["token"]) ){
                ApiUser::where([
                    'user_id'      => $userId,
                    'user_company' => WmsConstant::COMPANY_BONAERA,
                ])->update([
                    "updated_at" => Carbon::now()
                ]);
                $tokenResult = $result["data"];
                $returnMsg   = helpers_success_message($tokenResult);
            } else {
                throw new Exception($result["msg"]);
            }
        } catch (Exception $e) {
            $returnMsg = helpers_fail_message($e->getMessage());
        }

        return $returnMsg;
    }

    /**
    * @func bonaeraDeliveryBundle
    * @description '묶음 배송 처리'
    * @param int $id
    * @param string $changeGroupNo
    * @return void
    */
    abstract function bonaeraDeliveryBundle(int $id, string $changeGroupNo): void;

    /**
    * @func bonaeraStockModifyApiBindCall
    * @description '재고신청서 수정 처리'
    * @param string $orderId
    * @param array $bonaeraStockModifyApiDtos
    * @param string $code
    * @return array
    */
    public function bonaeraStockModifyApiBindCall(string $orderId, array $bonaeraStockModifyApiDtos, string $code = ""): array
    {
        return $this->bonaera->stockModifyApiBindCall($orderId, $bonaeraStockModifyApiDtos, $code);
    }

    /**
    * @func bonaeraStockModifyApiBindOT002
    * @description '재고신청서 수정(OT002)'
    * @param string $orderId
    * @param string $logisticsCode
    * @return array
    */
    public function bonaeraStockModifyApiBindOT002(string $orderId, string $logisticsCode): array
    {
        return $this->bonaera->stockModifyApiBindOT002($orderId, $logisticsCode);
    }

    /**
    * @func bonaeraStockModifyApiBindOS002
    * @description '재고신청서 수정(OS002)'
    * @param string $orderId
    * @return array
    */
    public function bonaeraStockModifyApiBindOS002(string $orderId): array
    {
        return $this->bonaera->stockModifyApiBindOS002($orderId);
    }
}
