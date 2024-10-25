<?php

namespace Tests\Feature;

use App\Constants\Constant1688;
use App\Models\OrderBaseData;
use App\Models\OrderChannelData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\OrderTradeData;
use App\Services\Order\OrderW1;
use App\Vo\Order\OrderDto;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Pagination\Paginator;
use Tests\TestCase;

class OrderTest extends TestCase
{

    # 주문 생성
    # php artisan test --filter testW1OrderCreate
    public function testW1OrderCreate()
    {
        $endPoint = "param2/1/com.alibaba.trade/alibaba.createOrder.preview/";
        $payload = [
            'access_token' => env("1688_ACCESS_TOKEN"),
            'addressParam' => [
                'addressId'    => 4073018184,
                'fullName'     => 'SELLERHUB',
                'mobile'       => '15684570398',
                'phone'        => '15684570398',
                'postCode'     => '264206',
                'cityText'     => '山东省',
                'provinceText' => '威海市',
                'areaText'     => '环翠区',
                'townText'     => '温泉镇',
                'address'      => '柳林惠友路3号鸿泉服装院西',
                'districtCode' => '371002',
            ],
            'cargoParamList' => [
                [
                    "offerId" => 654362860865,
                    "specId" => '556456e86881e3907df58b1815e9d440',
                    "quantity" => 3,
                ],
            ],
            'preSelectPayChannel' => "alipay"
        ];
        $returnMsg = curl_1688("post", $endPoint, $payload);
        dd($returnMsg);

        $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.createCrossOrder/";
        $payload = [
            'access_token' => env("1688_ACCESS_TOKEN"),
            'flow'         => "general",
            'addressParam' => [
                'addressId'    => 4073018184,
                'fullName'     => 'SELLERHUB',
                'mobile'       => '15684570398',
                'phone'        => '15684570398',
                'postCode'     => '264206',
                'cityText'     => '山东省',
                'provinceText' => '威海市',
                'areaText'     => '环翠区',
                'townText'     => '温泉镇',
                'address'      => '柳林惠友路3号鸿泉服装院西',
                'districtCode' => '371002',
            ],
            'cargoParamList' => [
                [
                    "offerId" => 774890948567,
                    "specId" => '08bd0ae34e2a4d64b53f827a0dce8189',
                    "quantity" => 3,
                ],
                [
                    "offerId" => 774890948567,
                    "specId" => '1c53f6ea884a5b48a7b95e5221041ab6',
                    "quantity" => 2,
                ],
            ],
            'preSelectPayChannel' => "alipay"
        ];
        $returnMsg = curl_1688("post", $endPoint, $payload);
        dd($returnMsg);
    }

    # 주문 미리보기
    # php artisan test --filter testW1OrderPreviewCreate
    public function testW1OrderPreviewCreate()
    {
        $orderId        = "2324260202467135493";
        $channelObj     = OrderChannelData::with(["details.option"])->where("order_id", $orderId)->first();
        $cargoParamList = [];

        if( !empty($channelObj->details) ){
            foreach ($channelObj->details as $detail) {
                if( !empty($detail->option) ){
                    $cargoParamList[] = [
                        "offerId"  => $detail->option->offer_id,
                        "specId"   => $detail->option->spec_id,
                        "quantity" => $detail->quantity,
                    ];
                }
            }
        }

        $endPoint = "param2/1/com.alibaba.trade/alibaba.createOrder.preview/";
        $payload = [
            'access_token' => env("1688_ACCESS_TOKEN"),
            'addressParam' => [
                'addressId'    => Constant1688::ADDRESSID,
                'fullName'     => Constant1688::FULLNAME,
                'mobile'       => Constant1688::MOBILE,
                'phone'        => Constant1688::PHONE,
                'postCode'     => Constant1688::POSTCODE,
                'cityText'     => Constant1688::CITYTEXT,
                'provinceText' => Constant1688::PROVINCETEXT,
                'areaText'     => Constant1688::AREATEXT,
                'townText'     => Constant1688::TOWNTEXT,
                'address'      => Constant1688::ADDRESS,
                'districtCode' => Constant1688::DISTRICTCODE,
            ],
            'cargoParamList' => $cargoParamList,
        ];
        $returnMsg = curl_1688("post", $endPoint, $payload);
        dd(json_encode($returnMsg["data"], JSON_UNESCAPED_UNICODE));
    }

    # 결제
    # php artisan test --filter testW1OrderPay
    public function testW1OrderPay()
    {
        $endPoint = "param2/1/com.alibaba.trade/alibaba.trade.createCrossOrder/";
        $payload = [
            'access_token' => env("1688_ACCESS_TOKEN"),
            'flow'         => "general",
            'addressParam' => [
                'addressId'    => 4073018184,
                'fullName'     => 'SELLERHUB',
                'mobile'       => '15684570398',
                'phone'        => '15684570398',
                'postCode'     => '264206',
                'cityText'     => '山东省',
                'provinceText' => '威海市',
                'areaText'     => '环翠区',
                'townText'     => '温泉镇',
                'address'      => '柳林惠友路3号鸿泉服装院西',
                'districtCode' => '371002',
            ],
            'cargoParamList' => [
                [
                    "offerId" => 774890948567,
                    "specId" => '08bd0ae34e2a4d64b53f827a0dce8189',
                    "quantity" => 3,
                ],
                [
                    "offerId" => 774890948567,
                    "specId" => '1c53f6ea884a5b48a7b95e5221041ab6',
                    "quantity" => 2,
                ],
            ],
            'preSelectPayChannel' => "alipay"
        ];
        $returnMsg = curl_1688("post", $endPoint, $payload);
        dd($returnMsg);
    }

    # dto 테스트
    # php artisan test --filter testOrderDto
    Public function testOrderDto()
    {
        $filePath = public_path('app/order/info3.json');
        try {
            if (File::exists($filePath)) {
                $jsonContent = File::get($filePath);
                $data        = json_decode($jsonContent, true);
                $result = $data["data"];
                
                $result["offerId"] = 596612162251;
                $orderDto = new OrderDto();
                $orderDto->bind($result);

                $baseInfo    = $orderDto->orderBaseDto;
                $upsertWhere = $baseInfo->getAllProperties();
                unset($upsertWhere["order_id"]);
                OrderBaseData::updateOrCreate(
                    [
                        "order_id" => $baseInfo->order_id,
                    ],
                    $upsertWhere
                );

                $orderTradeDtos = $orderDto->orderTradeDtos;
                foreach ($orderTradeDtos as $orderTradeDto) {
                    $upsertWhere = $orderTradeDto->getAllProperties();
                    unset($upsertWhere["order_id"]);
                    unset($upsertWhere["phase"]);
                    OrderTradeData::updateOrCreate(
                        [
                            "order_id" => $orderTradeDto->order_id,
                            "phase"    => $orderTradeDto->phase,
                        ],
                        $upsertWhere
                    );
                }

                $orderProductDtos = $orderDto->orderProductDtos;
                foreach ($orderProductDtos as $orderProductDto) {
                    $upsertWhere = $orderProductDto->getAllProperties();
                    unset($upsertWhere["order_id"]);
                    unset($upsertWhere["offer_id"]);
                    unset($upsertWhere["spec_id"]);
                    OrderProductData::updateOrCreate(
                        [
                            "order_id" => $orderProductDto->order_id,
                            "offer_id" => $orderProductDto->offer_id,
                            "spec_id"  => $orderProductDto->spec_id,
                        ],
                        $upsertWhere
                    );
                }

                $orderLogisticDtos = $orderDto->orderLogisticDtos;
                foreach ($orderLogisticDtos as $orderLogisticDto) {
                    $upsertWhere = $orderLogisticDto->getAllProperties();
                    unset($upsertWhere["logistics_id"]);
                    OrderLogisticsData::updateOrCreate(
                        [
                            "logistics_id" => $orderLogisticDto->logistics_id,
                        ],
                        $upsertWhere
                    );
                }

                dd("끝");

            } else {
                throw new Exception("파일이 존재하지 않습니다.");
            }
        } catch (Exception $e) {
            $msg = "======================== 에러 발생 ========================\r\n";
            $msg .= $e->getMessage();
            dd($msg);
        }
    }

    /** 예상 운임비 추출 */
    # php artisan test --filter testOrderFreight
    public function testOrderFreight()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $builder     = OrderBaseData::with(["w_options"]);
        $endPoint    = "param2/1/com.alibaba.fenxiao.crossborder/product.freight.estimate/";
        $accessToken = env("1688_ACCESS_TOKEN");

        $filePath = storage_path('logs/order/freight.txt');

        // 디렉토리가 존재하지 않으면 생성
        $directory = dirname($filePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $perPage    = 900;
        $totalCount = $builder->count();
        $totalPages = ceil($totalCount / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
            
            // paginate 메소드는 새 Paginator 인스턴스를 반환합니다.
            $pagedData = $builder->paginate($perPage);
            $results   = $pagedData->items();

            foreach ($results as $obj) {
                $orderId     = $obj->order_id;
                $offerId     = $obj->offer_id;
                $shippingFee = $obj->shipping_fee;

                $totalNum              = 0;
                $logisticsSkuNumModels = [];

                foreach ($obj->w_options as $w_option) {
                    $totalNum += $w_option->quantity;

                    $logisticsSkuNumModels[] = [
                        'skuId'  => $w_option->sku_id,
                        'number' => $w_option->quantity,
                    ];
                }

                $payload = [
                    'access_token'                 => $accessToken,
                    'productFreightQueryParamsNew' => [
                        'offerId'               => $offerId,
                        'toProvinceCode'        => 37,
                        'toCityCode'            => 3710,
                        'toCountryCode'         => "CN",
                        'totalNum'              => $totalNum,
                        'logisticsSkuNumModels' => $logisticsSkuNumModels
                    ]
                ];
                $apiResult = curl_1688("POST", $endPoint, $payload);

                $freight = 0;
                if( isset($apiResult["data"]["result"]["result"]["freight"]) ){
                    $freight = $apiResult["data"]["result"]["result"]["freight"];
                } else {
                    $freight = "freight 조회에러";
                }

                $logTxt = "{$orderId},{$offerId},{$totalNum},{$shippingFee},{$freight}";

                // 파일에 텍스트 추가
                File::append($filePath, $logTxt . PHP_EOL);
            }
        }
    }

    /** 중국 내륙 배송 시간 확인 */
    # php artisan test --filter testOrderChinaDeliveryData
    public function testOrderChinaDeliveryData()
    {
        $channelOrderIds = ["2229509460489135493", "2229584196685135493", "2251001028937135493", "2248748654445135493", "2248216824073135493", "2250917762092135493", "2251949162878135493", "2251947542517135493"];
        $orderW1         = app(OrderW1::class);

        $filePath = storage_path('logs/order/chinaDelivery.txt');

        // 디렉토리가 존재하지 않으면 생성
        $directory = dirname($filePath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        foreach ($channelOrderIds as $channelOrderId) {

            $channelObj = OrderChannelData::with([
                "order.logistics_first", "order.trade_first"
            ])->where("order_id", $channelOrderId)->first();

            try {
                if( $channelObj->order->logistics_first == null ){
                    throw new Exception("물류 정보 없음");
                }
                if( $channelObj->order->trade_first == null ){
                    throw new Exception("거래 정보 없음");
                }

                $result         = $orderW1->orderLogisticsInfo($channelObj->order_id);
                $logisticsFirst = $channelObj->order->logistics_first;
                $tradeFirst     = $channelObj->order->trade_first;
                
                $firstTime = "";
                $lastTime  = "";
                /** 전체 소요 시간 */
                $allTimeDiffInHours = "";
                /** 전체 배송 시간 */
                $allDeliveryTimeDiffInHours = "";
                /** 내륙 배송 시간 */
                $chinaDeliveryTimeDiffInHours = "";
    
                $logTxt = "{$channelOrderId}|{$channelObj->order_id}|{$logisticsFirst->logistics_company_name}|{$logisticsFirst->logistics_code}|{$logisticsFirst->logistics_bill_no}|{$tradeFirst->pay_time}|{$logisticsFirst->gmt_modified}";
    
                if( isset($result["data"][0]["logisticsSteps"]) && !empty($result["data"][0]["logisticsSteps"]) ){
                    $firstTime = $result["data"][0]["logisticsSteps"][0];
                    $lastTime  = $result["data"][0]["logisticsSteps"][count($result["data"][0]["logisticsSteps"])-1];
    
                    $logTxt .= "|{$firstTime['acceptTime']}|{$lastTime['acceptTime']}";
    
                    $acceptTime = Carbon::parse($lastTime['acceptTime']);
    
                    $payTime            = Carbon::parse($tradeFirst->pay_time);
                    $allTimeDiffInHours = round($payTime->floatDiffInHours($acceptTime), 1);
    
                    $gmtModified                = Carbon::parse($logisticsFirst->gmt_modified);
                    $allDeliveryTimeDiffInHours = round($gmtModified->floatDiffInHours($acceptTime), 1);
    
                    $firstAcceptTime              = Carbon::parse($firstTime['acceptTime']);
                    $acceptTime                   = Carbon::parse($lastTime['acceptTime']);
                    $chinaDeliveryTimeDiffInHours = round($firstAcceptTime->floatDiffInHours($acceptTime), 1);
    
                    $logTxt .= "|{$allTimeDiffInHours}|{$allDeliveryTimeDiffInHours}|{$chinaDeliveryTimeDiffInHours}";
                } else {
                    $logTxt .= "|error: 물류 조회 API";
                }
            } catch (Exception $e) {
                $logTxt = "{$channelOrderId}|error: {$e->getMessage()}";
            }

            // 파일에 텍스트 추가
            File::append($filePath, $logTxt . PHP_EOL);
        }

        dd("끝");
    }
}
