<?php

namespace Tests\Feature;

use App\Constants\BonaeraConstant;
use App\Models\BonaeraInProductData;
use App\Models\BonaeraOutBaseData;
use App\Models\BonaeraOutDeliveryData;
use App\Models\BonaeraOutWeightData;
use App\Models\HsCodeData;
use App\Models\OrderBaseData;
use App\Packages\Bonaera;
use Exception;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BonaeraTest extends TestCase
{
    # php artisan test --filter testCreateWarehouse
    /** 입고신청 */
    public function testCreateWarehouse()
    {
        $bonaera = new Bonaera();
        $orderId = "2237938826932135493";

        $bonaera->createStockApi($orderId);
    }

    # php artisan test --filter testCustomCreateWarehouse
    /** 특정 조건 입고신청 */
    public function testCustomCreateWarehouse()
    {
        $bonaera = new Bonaera();
        
        $objs = OrderBaseData::join("order_logistics_datas as a", "order_base_datas.order_id", "=", "a.order_id")
        ->where("order_base_datas.created_at", ">=", "2024-11-01")
        ->where("a.logistics_code", "!=", "")
        ->groupBy("order_base_datas.order_id")
        ->get();

        foreach ($objs as $obj) {
            $bonaera->createStockApi($obj->order_id);
        }

        dd("끝");
    }

    # php artisan test --filter testCreateShipping
    /** 출고신청 */
    public function testCreateShipping()
    {
        $bonaera = new Bonaera();
        $orderId = "2237938826932135493";

        $bonaera->createApplicationApi($orderId);
    }

    # php artisan test --filter testStockModifyApi
    /** 재고신청서 수정 */
    public function testStockModifyApi()
    {
        $bonaera = new Bonaera();

        $result = $bonaera->stockModifyApi("ST241121002656");
        $this->assertTrue($result['isSuccess']);
    }

    # php artisan test --filter testStockModifyApiBindOT001
    /** 재고신청서 수정(OT001) */
    public function testStockModifyApiBindOT001()
    {
        $bonaera = new Bonaera();

        $result = $bonaera->stockModifyApiBindOT002("2360257214955135493", "LP00691146849651");
        dd($result);
        
    }

    # php artisan test --filter testCreateDelivery
    /** 신청서 조회 */
    public function testCreateDelivery(BonaeraOutBaseData $outBaseObj)
    {
        $endPoint = "https://bonaera.com/elpisapi/applicationList_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $payload  = [
            "userId"  => env("BONAERA_USER_ID" , "sellerhub"),
            "grCode"  => $outBaseObj->group_no,
        ];

        $result = helpers_curl("GET", $endPoint, $header, $payload);
        
        $groupNo = $outBaseObj->group_no;
        if( isset($result["data"]["grCode"]) && isset($result["data"]["ReciverInfo"][0]) ){
            $res         = $result["data"];
            $reciverInfo = $res["ReciverInfo"][0];

            BonaeraOutDeliveryData::updateOrCreate(
                [
                    "group_no" => $groupNo,
                ],
                [
                    "invoice"        => $res["invoice"] ?? "",
                    "state"          => $res["state"],
                    "outday"         => $res["outday"] ?? null,
                    "receiver_name"  => $reciverInfo["receiverName"],
                    "zip_code"       => $reciverInfo["zipCode"],
                    "addr1"          => $reciverInfo["addr1"],
                    "addr2"          => $reciverInfo["addr2"],
                    "receiver_phone" => $reciverInfo["receiverPhone"],
                    "personal_type"  => $reciverInfo["personalType"],
                    "personal_num"   => $reciverInfo["personalNum"],
                    "unipass_result" => $reciverInfo["unipassResult"],
                    "unipass_reason" => $reciverInfo["unipassReason"],
                    "ship_memo"      => $reciverInfo["shipMemo"],
                ]
            );

            if( isset($res["weightList"][0]) ){
                $weight = $res["weightList"][0];
                BonaeraOutWeightData::updateOrCreate(
                    [
                        "group_no" => $groupNo,
                    ],
                    [
                        "box_cnt"          => $weight["boxCnt"] ?? 0,
                        "real_weight"      => $weight["realWeight"] ?? 0,
                        "width"            => $weight["width"] ?? 0,
                        "length"           => $weight["length"] ?? 0,
                        "height"           => $weight["height"] ?? 0,
                        "weight"           => $weight["weight"] ?? 0,
                        "ship_money"       => $weight["shipMoney"] ?? 0,
                        "weight_fee"       => $weight["weightFee"] ?? 0,
                        "volume_fee"       => $weight["volumeFee"] ?? 0,
                        "svc_money1"       => $weight["svcMoney1"] ?? 0,
                        "svc_money2"       => $weight["svcMoney2"] ?? 0,
                        "plus_money"       => $weight["plusMoney"] ?? 0,
                        "plus_money_memo"  => $weight["plusMoneyMemo"] ?? "",
                        "minus_money"      => $weight["minusMoney"] ?? 0,
                        "minus_money_memo" => $weight["minusMoneyMemo"] ?? "",
                        "commission"       => $weight["commission"] ?? 0,
                        "islands"          => $weight["islands"] ?? 0,
                        "total_money"      => $weight["totalMoney"] ?? 0,
                    ]
                );
            }
        }
    }

    # php artisan test --filter testGetStockList
    /** 재고현황 조회 */
    public function testGetStockList()
    {
        $endPoint = "https://bonaera.com/elpisapi/stockList_api.php";
        $header   = [
            'userKey: ' . env("BONAERA_TOKEN" , "3dI7uzN1dERvCBM1wt9wp1CglC7hcBB0jFkLZAFjZDC7SP56TIfwcJhfpTbLCIjg"),
            'Content-Type: application/json'
        ];
        $payload  = [
            "userId"  => env("BONAERA_USER_ID" , "sellerhub"),
            "stCode" => "ST240930000146",
        ];

        $result = helpers_curl("GET", $endPoint, $header, $payload);
        
       dd($result);
    }

    # php artisan test --filter testMappingHsCode
    public function testMappingHsCode()
    {
        $filePath = public_path('app/hs_code_mapping.txt');
        if (!File::exists($filePath)) {
            throw new Exception("파일이 존재하지 않습니다.");
        }

        $lines = File::lines($filePath);
        foreach($lines as $key => $line){
            $data    = explode(',', $line);
            $hs_code = $data[0];
            $sh_no   = $data[1];
            
            HsCodeData::updateOrCreate([
                "hs_code" => $hs_code,
            ],[
                'sh_no'   => $sh_no,
            ]);
        }

        dd("끝");
    }

    # php artisan test --filter testBoanearaEtcMethod
    public function testBoanearaEtcMethod()
    {
        $objs = BonaeraInProductData::get();
        foreach ($objs as $obj) {
            $code  = $obj->hs_code;
            $hsObj = HsCodeData::where(function($qry) use($code) {
                $qry->where("hs_code", $code)
                ->orWhere("sh_no", $code);
            })->first();

            if( $hsObj != null ){
                $obj->update([
                    "hs_code" => $hsObj->hs_code
                ]);
            }
        }
        dd("끝");
    }
}
