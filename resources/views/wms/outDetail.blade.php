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
                        <form id="searchFrm">
                            <input type="hidden" name="group_no" value="{{ $data->group_no}}">
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
                                            <select class="form-select" name="ctr_num">
                                                @foreach (BonaeraConstant::CTR_NUM as $key => $value)
                                                    @if ($data->out_delivery && $key == $data->out_delivery->ctr_num)
                                                        <option value="{{ $key }}" selected>{{ $value }}</option>
                                                    @else
                                                        <option value="{{ $key }}" >{{ $value }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <th scope="col" style="width: 10%">운송장 번호</th>
                                        <td>{{ $data->out_delivery->invoice }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="col" style="width: 10%">수령인</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="receiver_name" value="{{ $data->out_delivery->receiver_name }}">
                                        </td>
                                        <th scope="col" style="width: 10%">통관부호</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="personal_num" value="{{ $data->out_delivery->personal_num }}">
                                            @if (!empty($data->out_delivery->unipass_reason))   
                                                <br>
                                                <span class="text-danger">{{ $data->out_delivery->unipass_reason }}</span>
                                            @endif
                                        </td>
                                        <th scope="col" style="width: 10%">타입</th>
                                        <td>
                                            <select class="form-select" name="personal_type">
                                                @foreach (OrderConstant::CLEARANCE_TYPE as $key => $value)
                                                    @if ($data->order->channel_obj && $key == $data->order->channel_obj->clearance_type)
                                                        <option value="{{ $key }}" selected>{{ $value }}</option>
                                                    @else
                                                        <option value="{{ $key }}" >{{ $value }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </td>
                                        <th scope="col" style="width: 10%">연락처</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="receiver_phone" value="{{ $data->out_delivery->receiver_phone }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="col" style="width: 10%">주소</th>
                                        <td colspan="3">
                                            <div class="row g-2">
                                                <div class="col-auto d-flex align-items-center">
                                                    <span>우편번호:</span>
                                                </div>
                                                <div class="col-auto">
                                                    <input type="text" class="form-control required-inp" name="zip_code" value="{{ $data->out_delivery->zip_code }}">
                                                </div>
                                            </div>
                                            <div class="row g-2 mt-2">
                                                <div class="col-auto d-flex align-items-center">
                                                    <span>주소:</span>
                                                </div>
                                                <div class="col">
                                                    <input type="text" class="form-control required-inp" name="addr1" value="{{ $data->out_delivery->addr1 }}">
                                                </div>
                                            </div>
                                        </td>
                                        <th scope="col" style="width: 10%">메모</th>
                                        <td colspan="3">
                                            <input type="text" class="form-control" name="ship_memo" value="{{ $data->out_delivery->ship_memo }}">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </form>
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
                                    <th scope="col" style="width: 12%">박스수</th>
                                    <td>
                                        {{ number_format($data->out_weight->box_cnt) }}
                                        <button type="button" class="btn btn-sm btn-success btn-box-detail btn-dark text-white" data-groupno={{ $data->group_no }}>상세정보</button>
                                    </td>
                                    <th scope="col" style="width: 12%">적용무게(kg)</th>
                                    <td>{{ $data->out_weight->weight }}</td>
                                    <th scope="col" style="width: 12%">부피할증료</th>
                                    <td>{{ number_format($data->out_weight->volume_fee) }}</td>
                                    <th scope="col" style="width: 12%">무게할증료</th>
                                    <td>{{ number_format($data->out_weight->weight_fee) }}</td>
                                </tr>
                                <tr>
                                    <th scope="col">부가서비스:입고(원)</th>
                                    <td>{{ number_format($data->out_weight->svc_money1) }}</td>
                                    <th scope="col">부가서비스:출고(원)</th>
                                    <td>{{ number_format($data->out_weight->svc_money2) }}</td>
                                    <th scope="col">추가 요금#1 (원) / 메모</th>
                                    <td>{{ number_format($data->out_weight->plus_money) }} / {{ $data->out_weight->plus_money_memo }}</td>
                                    <th scope="col">추가 할인#2 (원) / 메모</th>
                                    <td>{{ number_format($data->out_weight->minus_money) }} / {{ $data->out_weight->minus_money_memo }}</td>
                                </tr>
                                <tr>
                                    <th scope="col">기본배송비(원)</th>
                                    <td>{{ number_format($data->out_weight->ship_money) }}</td>
                                    <th scope="col">도서산간(원)</th>
                                    <td>{{ number_format($data->out_weight->islands) }}</td>
                                    <th scope="col">수수료(원)</th>
                                    <td>{{ number_format($data->out_weight->commission) }}</td>
                                    <th scope="col">총 배송금액(원)</th>
                                    <td>
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
                                    <th colspan="4">출고정보</th>
                                    <th>W결제금액(배송비) 합계</th>
                                    <td>
                                        @php
                                            $totalAmount      = $data->order->total_amount ?? 0;
                                            $totalShippingFee = $data->order->shipping_fee ?? 0;

                                            foreach ($data->otherObjs as $otherObj) {
                                                $totalAmount      += $otherObj->order->total_amount ?? 0;
                                                $totalShippingFee += $otherObj->order->shipping_fee ?? 0;
                                            }
                                        @endphp
                                        {{ number_format($totalAmount, 2) }}({{ number_format($totalShippingFee, 2) }})
                                    </td>
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
                                    <td>{{ number_format($data->order->total_amount, 2) }} ({{ number_format($data->order->shipping_fee, 2) }})</td>
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
                                        <td colspan="3">(<a href="https://detail.1688.com/offer/{{ $otherObj->order->product->offer_id }}.html" target="_blank">{{ $otherObj->order->product->offer_id }}</a>) {{ $otherObj->order->product->prd_name_kr }}</td>
                                        <th scope="col" style="width: 15%">W결제금액(배송비)</th>
                                        <td>{{ number_format($otherObj->order->total_amount, 2) }} ({{ number_format($otherObj->order->shipping_fee, 2) }})</td>
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
                        <button type="button" class="btn btn-dark btn-update text-white me-3" data-id={{ $data->id }}>출고정보 업데이트</button>
                        <button type="button" class="btn btn-success btn-modi text-white" data-id={{ $data->id }}>배송정보 수정</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel"></h5>
                    </div>
                    <div class="modal-body">
                        <table class="table table-white bg-white box-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 200px;" class="text-center">No</th>
                                    <th scope="col" style="width: 200px;" class="text-center">실무게(kg)</th>
                                    <th scope="col" style="width: 200px;" class="text-center">크기(cm)</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
<script type="text/javascript">
    $(document).ready(function() {

        $('.btn-modi').click(function(){
            let validate = true;
            $(".required-inp").each(function(){
                if($(this).val().trim() == ""){
                    alert("빈 값없이 입력해주세요.");
                    validate = false;
                    return $(this).focus();
                }
            });

            if( validate == true ){
                let formData = $("#searchFrm").serialize();
                if( confirm("배송정보를 수정하시겠습니까?\n수정 시 보내라로 업데이트 됩니다.") ){
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"     : "{{ route('w.wms.bonaeraOutDeliveryUpdate') }}",
                        "data"    : formData,
                        beforeSend: function () {
                            // $("#loadingOverlay").show();
                        },
                        complete  : function(xhr, status) {
                            // $("#loadingOverlay").hide();
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
            }
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".btn-box-detail").click(function(){
            let groupNo = [$(this).data("groupno")];

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "GET",
                "url"     : `/api/w/wms/bonaeraOut/box/${groupNo}`,
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $("#htmlModalLabel").text(`박스 정보 : ${groupNo}`);

                    $(".box-table tbody").html("");
                    resp.data?.map(function(obj, key){
                        $(".box-table tbody").append(`
                            <tr class="text-center">
                                <td>${key+1}</td>
                                <td>${obj.real_weight.toLocaleString('ko-KR')}</td>
                                <td>${obj.width.toLocaleString('ko-KR')} x ${obj.height.toLocaleString('ko-KR')} x ${obj.length.toLocaleString('ko-KR')}</td>
                            </tr>
                        `);
                    });

                    $("#htmlModal").modal('show');
                    console.log(resp);
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

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
