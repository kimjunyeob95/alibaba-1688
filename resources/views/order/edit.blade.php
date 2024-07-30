@php
    use App\Constants\ProductConstant;
    use App\Constants\WConstant;
    use App\Constants\ImageConstant;
    use App\Constants\GosiConstants;
    use App\Constants\OptionConstant;
    use App\Constants\InspectConstant;
    $exchangeRate = config('1688_EXCHANGE_RATE', 200);
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        color: #343a40;
    }
</style>
@endsection

@section('scripts')
@endsection

@section('content')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="/">
                        <span>Home</span>
                    </a>
                </li>
                <li class="breadcrumb-item">W App</li>
                <li class="breadcrumb-item">주문 관리</li>
                <li class="breadcrumb-item active" aria-current="page">주문 정보 수정</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
                <div class="row mb-12">

                    <div class="text-center">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="7">WApp 주문 정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="3" attr="main_img" style="height: 100px;">
                                        <div class="d-flex align-items-center justify-content-center" style="height: 100%;">
                                            <img class="lazy-img preview-image" src="{{ $data->product->main_img->img_url_origin}}" style="width: 80px; height: 80px">
                                        </div>
                                    </td>
                                    <th scope="col" style="width: 10%">W 주문번호</th>
                                    <td attr="order_id">
                                        {{ $data->order_id }}
                                    </td>
                                    <th scope="col" style="width: 10%">W 결제금액</th>
                                    <td attr="sum_product_payment">
                                        {{ $data->sum_product_payment }}
                                    </td>
                                    <th scope="col" style="width: 10%">W 환불금액</th>
                                    <td attr="refund_payment">
                                        {{ $data->refund_payment }}
                                    </td>
                                </tr>
                                <tr>
                                    <th rowspan="2">상품명</th>
                                    <td rowspan="2">
                                        <div attr="prd_name_kr" style="max-width: 400px;">
                                            {{ $data->product->prd_name_kr }}
                                        </div>
                                    </td>
                                    <th>W 주문금액</th>
                                    <td attr="total_amount">
                                        {{ $data->total_amount }}
                                    </td>
                                    <th>W 배송비</th>
                                    <td attr="shipping_fee">
                                        {{ $data->shipping_fee }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>채널 금액</th>
                                    <td attr="total_channel_price">
                                        {{ number_format($data->channel_obj->total_channel_price) }}
                                    </td>
                                    <th>채널 배송비</th>
                                    <td attr="delivery_price">
                                        {{ number_format($data->channel_obj->delivery_price) }}
                                    </td>
                                </tr>
                                <tr class="opt-tr">
                                    <th scope="col" style="width: 10%">구분</th>
                                    <th scope="col" style="width: 10%">옵션 이미지</th>
                                    <th scope="col" style="width: 50%" colspan="2">옵션명</th>
                                    <th scope="col" style="width: 10%">수량</th>
                                    <th scope="col" style="width: 10%">W 금액</th>
                                    <th scope="col" style="width: 10%">채널 금액</th>
                                </tr>
                                @php
                                    $total_quantity      = 0;
                                    $total_item_amount   = 0;
                                    $total_channel_price = 0;
                                @endphp
                                @foreach ($data->w_options as $key => $wOption)
                                    @php
                                        $sku_img_url = "/assets/img/no_img.png";
                                        if( isset($wOption->option->sku_img_url) && $wOption->option->sku_img_url ){
                                            $sku_img_url = $wOption->option->sku_img_url;
                                        }

                                        $channel_price = 0;
                                        foreach ($data->channel_obj->details as $detail) {
                                            if ($detail->option_id === $wOption->option->id) {
                                                $channel_price = $detail->channel_price;
                                                break;
                                            }
                                        }

                                        $total_quantity      += (int)$wOption->quantity;
                                        $total_item_amount   += $wOption->item_amount;
                                        $total_channel_price += (int)$channel_price;
                                    @endphp
                                    <tr class="opt-tr-child">
                                        <td>{{ $key+1 }}</td>
                                        <td>
                                            <img class="lazy-img preview-image" width="50" height="50" src="{{ $sku_img_url }}">
                                        </td>
                                        <td colspan="2">
                                            <div style="max-width: 500px;">
                                                {{ $wOption->option->option_name_kr }}
                                            </div>
                                        </td>
                                        <td>{{ $wOption->quantity }}</td>
                                        <td>{{ $wOption->item_amount }}</td>
                                        <td>{{ number_format($channel_price) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="opt-tr-child">
                                    <th colspan=2></th>
                                    <th colspan=2>합계</th>
                                    <td>{{ number_format($total_quantity) }}</td>
                                    <td>{{ $total_item_amount }}</td>
                                    <td>{{ number_format($total_channel_price) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if (count($data->logistics) > 0)
                        @php
                            $last_logistic = $data->logistics[count($data->logistics) - 1];
                        @endphp
                        <div class="text-center mt-3">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th colspan="7">배송정보</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>배송정보(CN)</th>
                                        <th>배송사</th>
                                        <td attr="logistics_company_name">
                                            {{ $last_logistic->logistics_company_name }}
                                        </td>
                                        <th>운송장 번호</th>
                                        <td attr="logistics_bill_no">
                                            {{ $last_logistic->logistics_bill_no }}
                                        </td>
                                        <td colspan="2">
                                            <button type="button" class="btn btn-md btn-primary btn-trace-delivery-cn btn">배송정보 조회</button>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <th>배송정보(KO)</th>
                                        <th>배송사</th>
                                        <td></td>
                                        <th>운송장 번호</th>
                                        <td></td>
                                        <td colspan="2">
                                            <button type="button" class="btn btn-md btn-ko-delivery btn-primary">배송정보 조회</button>
                                        </td>
                                    </tr> --}}
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="text-center">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="12">채널 주문 정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->w_options as $wOption)
                                    <tr>
                                        <th scope="col" style="">채널 주문번호</th>
                                        <td>
                                            {{ $data->channel_obj->channel_order_id }}
                                        </td>
                                        <th scope="col" style="">옵션명</th>
                                        <td>
                                            {{ $wOption->option->option_name_kr }}
                                        </td>
                                        <th scope="col" style="">수량</th>
                                        <td>
                                            {{ $wOption->quantity }}
                                        </td>
                                        <th scope="col" style="">구매자</th>
                                        <td>
                                            {{ $data->channel_obj->buyer_name }}
                                        </td>
                                        <th scope="col" style="">통관부호</th>
                                        <td>
                                            {{ $data->sum_product_payment }}
                                        </td>
                                        <th scope="col" style="">연락처1</th>
                                        <td>
                                            {{ $data->sum_product_payment }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="col" style="">연락처2</th>
                                        <td>
                                            {{ $data->sum_product_payment }}
                                        </td>
                                        <th scope="col" style="">우편번호</th>
                                        <td>
                                            {{ $data->sum_product_payment }}
                                        </td>
                                        <th scope="col" style="">주소</th>
                                        <td>
                                            {{ $data->sum_product_payment }}
                                        </td>
                                        <th scope="col" style="">메모</th>
                                        <td colspan="3">
                                            {{ $data->sum_product_payment }}
                                        </td>
                                        <th scope="col" style="">삭제</th>
                                        <td>
                                            불가
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>
        </div>

    </div>
<script type="text/javascript">

    $(document).ready(function(){

        
    })
</script>

@endsection
