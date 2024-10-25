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
        $channelOrderIds = ["GO_1013359982", "GO_1013365636", "GO_1013362548", "GO_1013362660", "GO_1013362670", "GO_1013371996", "GO_1013372728", "GO_1013374522", "GO_1013374598", "GO_1013374784", "GO_1013378398", "GO_1013378887", "GO_1013381615", "GO_1013381942", "GO_1013381963", "GO_1013382172", "GO_1013390690", "GO_1013391809", "GO_1013392064", "GO_1013392075", "GO_1013392335", "GO_1013392081", "GO_1013392333", "GO_1013382446", "GO_1013397347", "GO_1013397414", "GO_1013397427", "GO_1013397951", "GO_1013398925", "GO_1013399485", "GO_1013403217", "GO_1013405358", "GO_1013405524", "GO_1013405770", "GO_1013405271", "GO_1013407935", "GO_1013416498", "GO_1013416945", "GO_1013416948", "GO_1013429565", "GO_1013431984", "GO_1013397890", "GO_1013438398", "GO_1013438684", "GO_1013442521", "GO_1013443023", "GO_1013444505", "GO_1013447734", "GO_1013448103", "GO_1013452407", "GO_1013455976", "GO_1013456706", "GO_1013458410", "GO_1013458511", "GO_1013461687", "GO_1013462653", "GO_1013462728", "GO_1013462829", "GO_1013464229", "GO_1013464297", "GO_1013464355", "GO_1013464485", "GO_1013465046", "GO_1013390528", "GO_1013467883", "GO_1013484290", "GO_1013484435", "GO_1013484529", "GO_1013485569", "GO_1013494150", "GO_1013494158", "GO_1013494211", "GO_1013494227", "GO_1013495034", "GO_1013495322", "GO_1013497677", "GO_1013499032", "GO_1013501442", "GO_1013501700", "GO_1013499909", "GO_1013509700", "GO_1013510699", "GO_1013510800", "GO_1013510888", "GO_1013510949", "GO_1013514354", "GO_1013535179", "GO_1013538610", "GO_1013542017", "GO_1013542026", "GO_1013542562", "GO_1013546943", "GO_1013546978", "GO_1013546981", "GO_1013547553", "GO_1013555140", "GO_1013559702", "GO_1013559929", "GO_1013566761", "GO_1013567172", "GO_1013557581", "GO_1013564877", "GO_1013571218", "GO_1013573189", "GO_1013577686", "GO_1013578960", "GO_1013579888", "GO_1013582319", "GO_1013574706", "GO_1013578302", "GO_1013579479", "GO_1013587469", "GO_1013588706", "GO_1013571213", "GO_1013594812", "GO_1013594838", "GO_1013594765", "GO_1013594789", "GO_1013597090", "GO_1013597414", "GO_1013598049", "GO_1013598196", "GO_1013601479", "GO_1013601572", "GO_1013602134", "GO_1013602281", "GO_1013602533", "GO_1013602631", "GO_1013602644", "GO_1013607921", "GO_1013616405", "GO_1013617140", "GO_1013617213", "GO_1013618666", "GO_1013619272", "GO_1013623312", "GO_1013623400", "GO_1013623687", "GO_1013623978", "GO_1013623990", "GO_1013624235", "GO_1013624250", "GO_1013624273", "GO_1013624314", "GO_1013624466", "GO_1013624564", "GO_1013624672", "GO_1013627456", "GO_1013627852", "GO_1013637378", "GO_1013638473", "GO_1013638667", "GO_1013638770", "GO_1013645008", "GO_1013645310", "GO_1013645565", "GO_1013647893", "GO_1013652473", "GO_1013652671", "GO_1013653123", "GO_1013653444", "GO_1013654830", "GO_1013655389", "GO_1013655421", "GO_1013655453", "GO_1013655711", "GO_1013655721", "GO_1013656244", "GO_1013656828", "GO_1013659049", "GO_1013661085", "GO_1013662134", "GO_1013664304", "GO_1013664340", "GO_1013664353", "GO_1013664354", "GO_1013664784", "GO_1013664861", "GO_1013671672", "GO_1013671678", "GO_1013674529", "GO_1013674531", "GO_1013674601", "GO_1013674723", "GO_1013674810", "GO_1013674960", "GO_1013678272", "GO_1013678274", "GO_1013678909", "GO_1013681030", "GO_1013686535", "GO_1013686685", "GO_1013688892", "GO_1013689678", "GO_1013690574", "GO_1013691038", "GO_1013695152", "GO_1013697162", "GO_1013698560", "GO_1013698600", "GO_1013698632", "GO_1013698677", "GO_1013703016", "GO_1013703195", "GO_1013703695", "GO_1013703701", "GO_1013704407", "GO_1013704807", "GO_1013704982", "GO_1013715886", "GO_1013716941", "GO_1013716962", "GO_1013717794", "GO_1013718210", "GO_1013718217", "GO_1013718353", "GO_1013719007", "GO_1013719008", "GO_1013722937", "GO_1013726526", "GO_1013735365", "GO_1013736017", "GO_1013736467", "GO_1013736742", "GO_1013737739", "GO_1013737784", "GO_1013737852", "GO_1013739500", "GO_1013739784", "GO_1013739794", "GO_1013739799", "GO_1013739885", "GO_1013739929", "GO_1013739953", "GO_1013740092", "GO_1013740434", "GO_1013743231", "GO_1013747920", "GO_1013754071", "GO_1013756143", "GO_1013760333", "GO_1013763474", "GO_1013763716", "GO_1013763847", "GO_1013765087", "GO_1013765548", "GO_1013768554", "GO_1013768624", "GO_1013768694", "GO_1013768927", "GO_1013770228", "GO_1013770236", "GO_1013771082", "GO_1013771352", "GO_1013771687", "GO_1013771690", "GO_1013771694", "GO_1013771762", "GO_1013771792", "GO_1013771822", "GO_1013771823", "GO_1013771876", "GO_1013771893", "GO_1013771936", "GO_1013771980", "GO_1013774498", "GO_1013774568", "GO_1013774571", "GO_1013774600", "GO_1013774607", "GO_1013776511", "GO_1013780013", "GO_1013780018", "GO_1013780056", "GO_1013781074", "GO_1013781196", "GO_1013792969", "GO_1013798127", "GO_1013799936", "GO_1013800203", "GO_1013800212", "GO_1013801186", "GO_1013801232", "GO_1013801545", "GO_1013805444", "GO_1013805636", "GO_1013806534", "GO_1013806544", "GO_1013806839", "GO_1013808292", "GO_1013809023", "GO_1013810569", "GO_1013810961", "GO_1013811075", "GO_1013812155", "GO_1013812236", "GO_1013816029", "GO_1013816049", "GO_1013816323", "GO_1013816760", "GO_1013818134", "GO_1013818254", "GO_1013818312", "GO_1013818443", "GO_1013818578", "GO_1013821399", "GO_1013821448", "GO_1013821499", "GO_1013821501", "GO_1013821505", "GO_1013821509", "GO_1013821511", "GO_1013821513", "GO_1013825590", "GO_1013828145", "GO_1013828827", "GO_1013830618", "GO_1013830633", "GO_1013831948", "GO_1013832456", "GO_1013840903", "GO_1013841596", "GO_1013841656", "GO_1013842910", "GO_1013843445", "GO_1013843918", "GO_1013845005", "GO_1013852552", "GO_1013852555", "GO_1013852559", "GO_1013852628", "GO_1013853984", "GO_1013854738", "GO_1013860756", "GO_1013860959", "GO_1013863643", "GO_1013868136", "GO_1013868206", "GO_1013868394", "GO_1013870519", "GO_1013875598", "GO_1013877188", "GO_1013877320", "GO_1013880137", "GO_1013885985", "GO_1013893016", "GO_1013901367", "GO_1013901929", "GO_1013903987", "GO_1013904037", "GO_1013913876", "GO_1013913882", "GO_1013921109", "GO_1013925746", "GO_1013932441"];
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
            ])->where("channel_order_id", $channelOrderId)->first();

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
