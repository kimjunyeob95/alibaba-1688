<?php

namespace Tests\Feature;

use App\Models\OrderBaseData;
use App\Models\OrderLogisticsData;
use App\Models\OrderProductData;
use App\Models\OrderTradeData;
use App\Vo\Order\OrderDto;
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
}
