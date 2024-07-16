@php
    use App\Constants\MallConstant;
    use App\Constants\OrderConstant;

    $exchangeRate = config('1688_EXCHANGE_RATE', 200);
@endphp
@extends('dashboard.base')

@section('styles')
<style>

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
                <li class="breadcrumb-item active" aria-current="page">주문 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="orderStatus" value={{ $orderStatus }}>
                    <input type="hidden" name="refundStatus" value={{ $refundStatus }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">주문 상태</th>
                                    <td colspan="2">
                                        <button type="button" name="orderStatus" class="btn-status btn btn-sm {{ $orderStatus == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        @foreach (OrderConstant::STATUS as $statusKey => $statusValue)    
                                            <button type="button" name="orderStatus" class="btn-status btn btn-sm {{ $orderStatus == $statusKey ? "btn-primary" : "btn-dark" }}"
                                            value="{{ $statusKey }}">{{ $statusValue }}</button>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">환불 상태</th>
                                    <td colspan="2">
                                        <button type="button" name="refundStatus" class="btn-status btn btn-sm {{ $refundStatus == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        @foreach (OrderConstant::REFUND_STATUS as $statusKey => $statusValue)    
                                            <button type="button" name="refundStatus" class="btn-status btn btn-sm {{ $refundStatus == $statusKey ? "btn-primary" : "btn-dark" }}"
                                            value="{{ $statusKey }}">{{ $statusValue }}</button>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">기간</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="timeCls">
                                            <option value="createOrder" @if($timeCls == "createOrder") selected @endif>주문 생성일</option>
                                            <option value="modiOrder" @if($timeCls == "modiOrder") selected @endif>주문 수정일</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group" style="width: 600px;">
                                            <input type="text" class="form-control calendar" autocomplete="off" name="startTime" placeholder="시작일" value="{{ $startTime }}">
                                            <span class="input-group-text">~</span>
                                            <input type="text" class="form-control calendar" autocomplete="off" name="endTime" placeholder="종료일" value="{{ $endTime }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=20 @if($pageSize == 20) selected @endif>20개 노출</option>
                                            <option value=10 @if($pageSize == 10) selected @endif>10개 노출</option>
                                            <option value=5 @if($pageSize == 5) selected @endif>5개 노출</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="3">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/wapp/order/list'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>

                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-success text-white me-2" id="btn-update-select">주문 업데이트</button>
                    <button class="btn btn-md btn-dark text-white me-2" id="btn-pay-select">결제하기</button>
                </div>

                <div class="table-responsive mt-3 overflow-auto" style="max-height: 800px; overflow-y: auto;">
                    <table class="table table-white bg-white" style="min-width: 1920px; max-height: 600px;">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th scope="col" class="text-center" style="width: 2%">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 1%">No</th>
                                <th scope="col" style="width: 10%">주문번호<br>(주문상태)</th>
                                <th scope="col" style="width: 5%">총 금액<br>(이벤트할인)</th>
                                <th scope="col" style="width: 5%">상품금액<br>(배송비)</th>
                                <th scope="col" style="width: 5%">환불상태<br>(금액)</th>
                                <th scope="col" style="width: 5%">옵션이미지</th>
                                <th scope="col" style="width: 8%">sku_ID<br>(제품ID)</th>
                                <th scope="col" style="width: 8%">구매수량<br>(가격)</th>
                                <th scope="col" style="width: 5%">옵션상태</th>
                                <th scope="col" style="width: 5%">옵션<br>환불금액</th>
                                <th scope="col" style="width: 8%">주문 생성일<br>주문 수정일</th>
                                <th scope="col" style="width: 8%">
                                    주문채널
                                    <br>
                                    채널 주문번호
                                    <br>
                                    주문자
                                </th>
                                <th scope="col" style="width: 6%">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator->items() as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data["baseInfo"]['id'] }}" class="form-check-input chk-inp" 
                                        type="checkbox" value="{{ $data["baseInfo"]['id'] }}" orderStatus={{ $data["baseInfo"]["status"] }}>
                                    </td>
                                    <td>
                                        <label for="checkbox-{{ $data["baseInfo"]['id'] }}" class="cursor-pointer">
                                            {{ number_format(($paginator->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td>
                                        <small>{{ $data["baseInfo"]['id'] }}</small>
                                        <br>
                                        ({{ OrderConstant::STATUS[$data["baseInfo"]["status"]] }})
                                    </td>
                                    <td>
                                        {{ $data["baseInfo"]["totalAmount"] }}
                                    </td>
                                    <td>
                                        {{ $data["baseInfo"]["sumProductPayment"] }}
                                        <br>
                                        ({{ $data["baseInfo"]["shippingFee"] }})
                                    </td>
                                    <td>
                                        @if (isset( $data["baseInfo"]["refundStatus"] ))    
                                            {{ OrderConstant::REFUND_STATUS[$data["baseInfo"]["refundStatus"]] }}
                                        @else
                                            X
                                        @endif
                                        <br>
                                        ({{ $data["baseInfo"]["refund"] }})
                                    </td>
                                    <td>
                                        @foreach ($data["productItems"] as $prdItem)
                                            <table style="height: 150px;">
                                                <tr>
                                                    <td>
                                                        <div>
                                                            @if (!empty($prdItem["productImgUrl"][0]))
                                                                <img class="lazy-img preview-image" data-src="{{ $prdItem["productImgUrl"][0] }}" width=60 height=60/>
                                                            @else
                                                                <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($data["productItems"] as $prdItem)
                                            <table class="w-100 d-flex align-items-center justify-content-center" style="height: 150px;">
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <small>{{ $prdItem["name"] }}</small>
                                                            @if ( isset($prdItem["skuID"]) )
                                                                <br>
                                                                <small>{{ $prdItem["skuID"] }}</small>
                                                            @endif
                                                            <br>
                                                            <small>({{ $prdItem["productID"] }})</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($data["productItems"] as $prdItem)
                                            <table class="w-100 d-flex align-items-center justify-content-center" style="height: 150px;">
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <small>{{ $prdItem["quantity"] }}</small>
                                                            <br>
                                                            <small>({{ $prdItem["price"] }})</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($data["productItems"] as $prdItem)
                                            <table style="height: 150px;">
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <small>{{ OrderConstant::STATUS[$prdItem["status"]] }}</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($data["productItems"] as $prdItem)
                                            <table style="height: 150px;">
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <small>{{ $prdItem["refund"] }}</small>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        @endforeach
                                    </td>
                                    <td>
                                        <small>{{ formatToKST($data["baseInfo"]["createTime"]) }}</small>
                                        <br>
                                        <small>{{ formatToKST($data["baseInfo"]["modifyTime"]) }}</small>
                                    </td>
                                    <td>
                                        @if ($data["baseObj"] && $data["channelObj"])
                                            <small>{{ MallConstant::MALL_NAME[$data["baseObj"]->channel] }}</small>
                                            <br>
                                            <small>{{ $data["channelObj"]->channel_order_id }}</small>
                                            <br>
                                            <small>{{ $data["channelObj"]->buyer_name }}</small>
                                        @else
                                            <p>주문정보 없음</p>
                                            <button class="btn btn-sm btn-primary btn-regist text-white" orderid={{ $data["baseInfo"]['id'] }}>정보 입력</button>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data["baseObj"] && $data["channelObj"])
                                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                                <button class="btn btn-sm btn-success btn-update text-white" orderid={{ $data["baseInfo"]['id'] }}>주문 업데이트</button>
                                                <button class="btn btn-sm btn-danger btn-cancel text-white" orderid={{ $data["baseInfo"]['id'] }}>취소/환불</button>
                                                @if( $data["baseInfo"]["status"] == OrderConstant::STATUS_WAITBUYERPAY )
                                                    <button class="btn btn-sm btn-dark btn-pay text-white" orderid={{ $data["baseInfo"]['id'] }}>결제하기</button>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="htmlModalLabel">결제하기</h5>
                        </div>
                        <div class="modal-body text-center">
                            <input type="hidden" name="orderIds[]" />
                            <h4 class="mb-4 font-weight-bold">결제 방법을 선택 하세요.</h4>
                            <div class="d-flex justify-content-center mb-4">
                                <div class="me-4 d-flex align-items-center">
                                    <input type="radio" id="payWay1" name="payWay" value="{{ OrderConstant::PAY_ALIPAY }}" checked>
                                    <label for="payWay1" class="ms-2 fs-5">Alipay</label>
                                </div>
                                <div class="d-flex align-items-center">
                                    <input type="radio" id="payWay2" name="payWay" value="{{ OrderConstant::PAY_CROSS_BORDER }}">
                                    <label for="payWay2" class="ms-2 fs-5">Cross-border Pay</label>
                                </div>
                            </div>
                            <p class="text-danger mt-2 small">결제대기(미 결제) 주문만 결제가 진행됩니다.</p>
                            <a class="btn btn-outline-primary btn-md mt-3 fs-4 d-none btn-pay-link" target="_blank">결제하러 가기</a>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-primary btn-pay-call">결제</button>
                            <button type="button" class="btn btn-secondary htmlModalClose">취소</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $paginator->links("vendor.pagination.bootstrap-4") }}
            </div>
        </div>

    </div>
<script type="text/javascript">
    $(document).ready(function() {
        var waitStatus = "{{ OrderConstant::STATUS_WAITBUYERPAY }}";
        $(".btn-status").click(function(){
            let name  = $(this).attr("name");
            let value = $(this).val();

            $(`input[name=${name}]`).val(value);
            $("#searchFrm").submit();
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });
        
        $(".btn-cancel").click(function(){
            return alert("작업 예정...");
        });

        $('#btn-pay-select').click(function(){
            let orderIds = [];

            $(".chk-inp:checked").each(function(index, element){
                let orderStatus = $(this).attr("orderStatus");
                if( orderStatus == waitStatus ){
                    orderIds.push($(this).val());
                }
            });

            if(orderIds.length < 1){
                return alert("선택 된 주문이 없거나 결제대기 상태의 주문이 없습니다.");
            }

            $('input[name="orderIds[]"]').val(orderIds);
            $("input[name=payWay]").prop("checked", false);
            $("input[name=payWay]").eq(0).prop("checked", true);
            $('.btn-pay-link').addClass("d-none");

            $("#htmlModal").modal('show');
        });

        $('.btn-pay').click(function(){
            let orderIds = $(this).attr("orderid");
            $('input[name="orderIds[]"]').val(orderIds);
            $("input[name=payWay]").prop("checked", false);
            $("input[name=payWay]").eq(0).prop("checked", true);
            $('.btn-pay-link').addClass("d-none");

            $("#htmlModal").modal('show');
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".btn-pay-call").click(function(){
            let orderIds = $('input[name="orderIds[]"]').val().split(",");
            let payWay   = $("input[name=payWay]:checked").val();

            if(confirm(`결제링크를 생성하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.order.orderPayLinkCreate') }}",
                    "data"       : { orderIds, payWay },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                        $('.btn-pay-link').addClass("d-none");
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        if( resp.data.payUrl ){
                            $('.btn-pay-link').attr("href", resp.data.payUrl);
                            $('.btn-pay-link').removeClass("d-none");
                        }
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $("#btn-update-select").click(function(){
            let orderIds = [];

            $(".chk-inp:checked").each(function(index, element){
                orderIds.push($(this).val());
            });

            if(orderIds.length < 1){
                return alert("선택 된 주문이 없습니다.");
            }

            if(confirm(`${orderIds.length}건의 주문을 WApp으로 업데이트 하시겠습니까?`)){
                $("#loadingOverlay").show();

                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.order.update') }}",
                    "data"       : { orderIds },
                    beforeSend: function () {
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $(".btn-update").click(function(){
            let orderIds = [$(this).attr("orderid")];

            if(confirm(`해당 주문을 WApp으로 업데이트 하시겠습니까?`)){
                $("#loadingOverlay").show();

                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.order.update') }}",
                    "data"       : { orderIds },
                    beforeSend: function () {
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });
    });
</script>

@endsection
