@php
    use App\Constants\WmsConstant;
    use App\Constants\BonaeraConstant;
    use App\Constants\OrderConstant;
    use App\Models\ProductOptionData;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .table th {
        background-color: #ebedef;
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
                <li class="breadcrumb-item">WMS</li>
                <li class="breadcrumb-item">출고 관리</li>
                <li class="breadcrumb-item active" aria-current="page">출고 상세</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
                <div class="row mb-12">
                    <div class="text-center">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="8">배송정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col" style="width: 10%">배송번호</th>
                                    <td>{{ $data->group_no }}</td>
                                    <th scope="col" style="width: 10%">출고상태</th>
                                    <td>{{ BonaeraConstant::GROUP_STATUS[$data->out_delivery->state] }}</td>
                                    <th scope="col" style="width: 10%">운송방법</th>
                                    <td>
                                        @if ($data->out_delivery)
                                            {{ BonaeraConstant::CTR_NUM[$data->out_delivery->ctr_num] }}
                                        @endif
                                    </td>
                                    <th scope="col" style="width: 10%">운송장 번호</th>
                                    <td>{{ $data->out_delivery->invoice }}</td>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 10%">수령인</th>
                                    <td>{{ $data->out_delivery->receiver_name }}</td>
                                    <th scope="col" style="width: 10%">통관부호</th>
                                    <td>
                                        @if (!empty($data->out_delivery->unipass_reason))   
                                            <span class="text-danger">{{ $data->out_delivery->unipass_reason }}</span>
                                        @else
                                            {{ $data->out_delivery->personal_num }}
                                        @endif
                                    </td>
                                    <th scope="col" style="width: 10%">타입</th>
                                    <td>{{ OrderConstant::CLEARANCE_TYPE[$data->order->channel_obj->clearance_type] }}</td>
                                    <th scope="col" style="width: 10%">연락처</th>
                                    <td>{{ $data->out_delivery->receiver_phone }}</td>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 10%">주소</th>
                                    <td colspan="3">
                                        ({{ $data->out_delivery->zip_code }}) {{ $data->out_delivery->addr1 }} {{ $data->out_delivery->addr2 }}
                                    </td>
                                    <th scope="col" style="width: 10%">메모</th>
                                    <td colspan="3">{{ $data->out_delivery->ship_memo }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="8">비용정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col" style="width: 12%">실무게(kg)</th>
                                    <td>{{ $data->out_weight->real_weight }}</td>
                                    <th scope="col" style="width: 12%">적용무게(kg)</th>
                                    <td>{{ $data->out_weight->weight }}</td>
                                    <th scope="col" style="width: 12%">크기(cm)</th>
                                    <td>{{ $data->out_weight->width }} x {{ $data->out_weight->length }} x {{ $data->out_weight->height }}</td>
                                    <th scope="col" style="width: 12%">박스수</th>
                                    <td>{{ number_format($data->out_weight->box_cnt) }}</td>
                                </tr>
                                <tr>
                                    <th scope="col">부피할증료</th>
                                    <td>{{ number_format($data->out_weight->volume_fee) }}</td>
                                    <th scope="col">무게할증료</th>
                                    <td>{{ number_format($data->out_weight->weight_fee) }}</td>
                                    <th scope="col">추가 요금#1 (원) / 메모</th>
                                    <td>{{ number_format($data->out_weight->plus_money) }} / {{ $data->out_weight->plus_money_memo }}</td>
                                    <th scope="col">추가 할인#2 (원) / 메모</th>
                                    <td>{{ number_format($data->out_weight->minus_money) }} / {{ $data->out_weight->minus_money_memo }}</td>
                                </tr>
                                <tr>
                                    <th scope="col">부가서비스:입고(원)</th>
                                    <td>{{ number_format($data->out_weight->svc_money1) }}</td>
                                    <th scope="col">부가서비스:출고(원)</th>
                                    <td>{{ number_format($data->out_weight->svc_money2) }}</td>
                                    <th scope="col">기본배송비(원)</th>
                                    <td>{{ number_format($data->out_weight->ship_money) }}</td>
                                    <th scope="col">도서산간(원)</th>
                                    <td>{{ number_format($data->out_weight->islands) }}</td>
                                </tr>
                                <tr>
                                    <th scope="col">수수료(원)</th>
                                    <td colspan="3">{{ number_format($data->out_weight->commission) }}</td>
                                    <th scope="col">총 배송금액(원)</th>
                                    <td colspan="3">
                                        {{ number_format($data->out_weight->total_money) }}
                                        @if ($data->out_weight && $data->out_delivery && $data->out_delivery->state === BonaeraConstant::GROUP_STATUS_304)
                                            <button class="btn btn-sm btn-danger btn-pay text-white" data-id={{ $data->id }}>결제하기</button>
                                            @if ($data->pay_fail_log)
                                                <br>
                                                <small class="text-danger">통신실패사유: {{ $data->pay_fail_log->message }}</small>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="6">부가서비스 내역</th>
                                </tr>
                                <tr>
                                    <th style="width: 2%">NO</th>
                                    <th style="width: 5%">작업</th>
                                    <th style="width: 12%">관련 번호</th>
                                    <th style="width: *">부가서비스 명</th>
                                    <th style="width: 10%">단가</th>
                                    <th style="width: 10%">수량</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if( count($data->out_extras) < 1 && count($data->out_delivery_extras) < 1 )
                                    <tr>
                                        <td colspan="6">데이터가 없습니다.</td>
                                    </tr>
                                @else
                                    @php
                                        $forKey = 1;
                                    @endphp
                                    @foreach ($data->out_extras as $outExtra)
                                        <tr>
                                            <td>{{ $forKey }}</td>
                                            <td>{{ WmsConstant::OUT_EXTRA_NAME }}</td>
                                            <td>{{ $outExtra->sh_no }}</td>
                                            <td>{{ $outExtra->extra_name }}</td>
                                            <td>{{ number_format($outExtra->extra_money) }}</td>
                                            <td>{{ number_format($outExtra->extra_cnt) }}</td>
                                        </tr>
                                        @php
                                            $forKey += 1;
                                        @endphp
                                    @endforeach
                                    @foreach ($data->out_delivery_extras as $outDeliveryExtra)
                                        <tr>
                                            <td>{{ $forKey }}</td>
                                            <td>{{ WmsConstant::OUT_DELIVERY_EXTRA_NAME }}</td>
                                            <td>{{ $outDeliveryExtra->group_no }}</td>
                                            <td>{{ $outDeliveryExtra->extra_name }}</td>
                                            <td>{{ number_format($outDeliveryExtra->extra_money) }}</td>
                                            <td>{{ number_format($outDeliveryExtra->extra_cnt) }}</td>
                                        </tr>
                                        @php
                                            $forKey += 1;
                                        @endphp
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="6">출고정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col" style="width: 10%">출고번호</th>
                                    <td>{{ $data->sh_no }} <button class="btn btn-sm btn-primary btn-in-detail text-white" data-stockno={{ $data->stock_no }}>입고상세</button></td>
                                    <th scope="col" style="width: 10%">주문번호</th>
                                    <td>{{ $data->order_id }}</td>
                                    <th scope="col" style="width: 10%">관련 입고번호</th>
                                    <td>{{ $data->stock_no }}</td>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 10%">상품명</th>
                                    <td colspan="3">(<a href="https://detail.1688.com/offer/{{ $data->order->product->offer_id }}.html" target="_blank">{{ $data->order->product->offer_id }}</a>) {{ $data->order->product->prd_name_kr }}</td>
                                    <th scope="col" style="width: 15%">W결제금액(배송비)</th>
                                    <td>{{ number_format($data->order->total_amount) }} ({{ number_format($data->order->shipping_fee) }})</td>
                                </tr>
                                <tr>
                                    <th colspan="4">옵션정보(이미지/재고번호/옵션명)</th>
                                    <th>요청</th>
                                    <th>출고</th>
                                </tr>
                                @foreach ($data->out_options as $out_option)
                                    @php
                                        $w_option = $out_option->w_option;
                                    @endphp
                                    <tr>
                                        <td colspan="4">
                                            @if (!empty($w_option))
                                                <small class="d-block mt-1">
                                                    @if ($w_option->sku_img_url)
                                                        <img class="lazy-img preview-image" data-src="{{ $w_option->sku_img_url }}" width=30 height=30/>
                                                    @else
                                                        <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                    @endif
                                                    / {{ $out_option->it_code }} / 
                                                    {{ $w_option->option_name_kr}}
                                                </small>
                                            @else
                                                <small class="d-block mt-1">
                                                    WApp에 옵션이 없음 option_id: {{ $out_option->option_id }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($out_option->quantity) }}</td>
                                        <td>{{ $data->out_delivery->state === BonaeraConstant::GROUP_STATUS_307 ? number_format($out_option->quantity) : 0 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @foreach ($data->otherObjs as $otherObj)
                            <table class="table table-bordered mt-3">
                                <tbody>
                                    <tr>
                                        <th scope="col" style="width: 10%">출고번호</th>
                                        <td>{{ $otherObj->sh_no }} <button class="btn btn-sm btn-primary btn-in-detail text-white" data-stockno={{ $otherObj->stock_no }}>입고상세</button></td>
                                        <th scope="col" style="width: 10%">주문번호</th>
                                        <td>{{ $otherObj->order_id }}</td>
                                        <th scope="col" style="width: 10%">관련 입고번호</th>
                                        <td>{{ $otherObj->stock_no }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="col" style="width: 10%">상품명</th>
                                        <td colspan="5">(<a href="https://detail.1688.com/offer/{{ $otherObj->order->product->offer_id }}.html" target="_blank">{{ $otherObj->order->product->offer_id }}</a>) {{ $otherObj->order->product->prd_name_kr }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="4">옵션정보(이미지/재고번호/옵션명)</th>
                                        <th>요청</th>
                                        <th>출고</th>
                                    </tr>
                                    @foreach ($otherObj->out_options as $out_option)
                                        @php
                                            $w_option = $out_option->w_option;
                                        @endphp
                                        <tr>
                                            <td colspan="4">
                                                @if (!empty($w_option))
                                                    <small class="d-block mt-1">
                                                        @if ($w_option->sku_img_url)
                                                            <img class="lazy-img preview-image" data-src="{{ $w_option->sku_img_url }}" width=30 height=30/>
                                                        @else
                                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                        @endif
                                                        / {{ $out_option->it_code }} / 
                                                        {{ $w_option->option_name_kr}}
                                                    </small>
                                                @else
                                                    <small class="d-block mt-1">
                                                        WApp에 옵션이 없음 option_id: {{ $out_option->option_id }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $out_option->quantity }}</td>
                                            <td>{{ $data->out_delivery->state === BonaeraConstant::GROUP_STATUS_307 ? $out_option->quantity : 0 }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    </div>

                    <div class="mt-3 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-dark btn-update text-white" data-id={{ $data->id }}>출고정보 업데이트</button>
                    </div>

                </div>
            </div>
        </div>

    </div>
<script type="text/javascript">
    $(document).ready(function() {
        $(".btn-update").click(function(){
            let ids = [$(this).data("id")];

            if(confirm(`출고정보를 업데이트 하시겠습니까?`)){
                $("#loadingOverlay").show();
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraOutUpdate') }}",
                    "data"    : { ids: ids },
                    beforeSend: function () {},
                    complete  : function(xhr, status) {
                        $("#loadingOverlay").hide();
                    },
                    success : function (resp) {
                        alert(resp.msg);
                    },
                    error: function (request) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $(".btn-pay").click(function(){
            let ids = [$(this).data("id")];

            if(confirm(`배송비 금액을 결제하시겠습니까?`)){
                $("#loadingOverlay").show();
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraOutPay') }}",
                    "data"    : { ids: ids },
                    beforeSend: function () {},
                    complete  : function(xhr, status) {
                        $("#loadingOverlay").hide();
                    },
                    success : function (resp) {
                        alert(resp.msg);
                    },
                    error: function (request) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $(".btn-in-detail").click(function(){
            let stockno = $(this).data("stockno");
            var newTab  = window.open(`/wapp/wms/in/${stockno}`, '_blank');
            newTab.focus(); 
        })
    });
</script>

@endsection
