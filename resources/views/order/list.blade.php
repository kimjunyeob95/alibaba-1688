@php
    use App\Constants\MallConstant;
    use App\Constants\OrderConstant;
    use App\Constants\OptionConstant;

    $exchangeRate = config('1688_EXCHANGE_RATE', 200);
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .div-trace .table th {
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
                <li class="breadcrumb-item">W App</li>
                <li class="breadcrumb-item">주문 관리</li>
                <li class="breadcrumb-item active" aria-current="page">WApp 주문 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="orderChannel" value={{ $orderChannel }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">주문 채널</th>
                                    <td colspan="2">
                                        <button type="button" name="orderChannel" class="btn-status btn btn-sm {{ $orderChannel == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        @foreach (MallConstant::MALL_NAME as $mallKey => $mallValue)    
                                            <button type="button" name="orderChannel" class="btn-status btn btn-sm {{ $orderChannel == $mallKey ? "btn-primary" : "btn-dark" }}"
                                            value="{{ $mallKey }}">{{ $mallValue }}</button>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">주문상태</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="orderStatusAll" name="orderStatus[]" value="" {{ $orderStatus == "" ? "checked" : "" }}>
                                                <label class="form-check-label" for="orderStatusAll">전체</label>
                                            </div>
                                            @foreach (OrderConstant::STATUS as $statusKey => $statusValue)
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="orderStatus{{ $statusKey }}" name="orderStatus[]" value="{{ $statusKey }}" {{ in_array($statusKey, explode(",", $orderStatus)) ? "checked" : "" }}>
                                                <label class="form-check-label" for="orderStatus{{ $statusKey }}">{{ $statusValue }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">배송상태</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="deliveryStatusAll" name="deliveryStatus[]" value="" {{ $deliveryStatus == "" ? "checked" : "" }}>
                                                <label class="form-check-label" for="deliveryStatusAll">전체</label>
                                            </div>
                                            @foreach (OrderConstant::LOGISTICS_STATUS as $statusKey => $statusValue)
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="deliveryStatus{{ $statusKey }}" name="deliveryStatus[]" value="{{ $statusKey }}" {{ in_array($statusKey, explode(",", $deliveryStatus)) ? "checked" : "" }}>
                                                <label class="form-check-label" for="deliveryStatus{{ $statusKey }}">{{ $statusValue }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">환불상태</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="refundStatusAll" name="refundStatus[]" value="" {{ $refundStatus == "" ? "checked" : "" }}>
                                                <label class="form-check-label" for="refundStatusAll">전체</label>
                                            </div>
                                            @foreach (OrderConstant::REFUND_STATUS as $statusKey => $statusValue)
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="refundStatus{{ $statusKey }}" name="refundStatus[]" value="{{ $statusKey }}" {{ in_array($statusKey, explode(",", $refundStatus)) ? "checked" : "" }}>
                                                <label class="form-check-label" for="refundStatus{{ $statusKey }}">{{ $statusValue }}</label>
                                            </div>
                                            @endforeach
                                        </div>
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
                                    <th style="width: 120px">검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="order_id" @if($search_cls == "order_id") selected @endif>W 주문번호</option>
                                            <option value="channel_order_id" @if($search_cls == "channel_order_id") selected @endif>채널 주문번호</option>
                                            <option value="offer_id" @if($search_cls == "offer_id") selected @endif>제품ID</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정렬</th>
                                    <td colspan="2">
                                        <select class="form-select" name="sort" style="width: 200px">
                                            <option value="created_at|desc" @if($sort == "created_at|desc") selected @endif>주문 생성일 내림차순</option>
                                            <option value="created_at|asc" @if($sort == "created_at|asc") selected @endif>주문 생성일 오름차순</option>
                                            <option value="updated_at|desc" @if($sort == "updated_at|desc") selected @endif>주문 수정일 내림차순</option>
                                            <option value="updated_at|asc" @if($sort == "updated_at|asc") selected @endif>주문 수정일 오름차순</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=50 @if($pageSize == 50) selected @endif>50개 노출</option>
                                            <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                            <option value=500 @if($pageSize == 500) selected @endif>500개 노출</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="3">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='{{ url()->current() }}'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>

                <div class="mt-3 d-flex justify-content-between">
                    <div class="d-flex">
                        <a class="btn btn-md btn-dark text-white me-2" href="https://www.notion.so/sellerhub/WAPP-2f34d2670b4d44c298611dd8c5439603?pvs=4" target="_blank">WApp 상태 정보 안내</a>
                    </div>
                    <div class="d-flex">
                        <button class="btn btn-md btn-success text-white me-2" id="btn-update-select">주문 업데이트</button>
                        <button class="btn btn-md btn-dark text-white me-2" id="btn-pay-select">결제하기</button>
                    </div>
                </div>

                <div class="table-responsive mt-3 overflow-auto" id="scrollTable" style="max-height: 800px; overflow-y: auto;">
                    <table class="table table-white bg-white" style="min-width: 1920px;">
                        <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                            <tr>
                                <th scope="col" class="text-center" style="width: 3%">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <br>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 1%">No</th>
                                <th scope="col" style="width: 3%">
                                    W 주문번호<br>
                                    (채널 주문번호)
                                </th>
                                <th scope="col" style="width: 8%">구매자<br>(주문 채널)</th>
                                <th scope="col" style="width: 5%" class="text-center">상품이미지</th>
                                <th scope="col" style="width: *%" class="text-center">주문정보</th>
                                <th scope="col" style="width: 10%">W 금액(위안)<br>(채널 금액(원화)))</th>
                                <th scope="col" style="width: 5%" class="text-center">주문상태</th>
                                <th scope="col" style="width: 5%" class="text-center">환불상태</th>
                                <th scope="col" style="width: 10%">주문 생성일<br>주문 수정일</th>
                                <th scope="col" style="width: 8%">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator->items() as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data->order_id }}" class="form-check-input chk-inp" 
                                        type="checkbox" value="{{ $data->order_id }}" orderStatus={{ $data->status }}>
                                    </td>
                                    <td>
                                        <label for="checkbox-{{ $data->order_id }}" class="cursor-pointer">
                                            {{ number_format(($paginator->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td>
                                        <small>{{ $data->order_id }}</small>
                                        <br>
                                        <small>({{ $data->channel_order_id }})</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->buyer_name }}</small>
                                        <br>
                                        <small>({{ MallConstant::MALL_NAME[$data->channel] }})</small>
                                    </td>
                                    <td>
                                        @if (!empty($data->product->main_img))
                                            <img class="lazy-img preview-image" data-src="{{ $data->product->main_img->img_url_origin }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            (<a href="https://detail.1688.com/offer/{{ $data->offer_id }}.html" target="_blank">{{ $data->offer_id }}</a>)
                                            <br>
                                            {{ $data->product->prd_name_kr }}
                                        </small>
                                        <div class="mt-3"></div>
                                        @foreach ($data->w_options as $w_option)
                                            @php
                                                $deliveryStatus = OrderConstant::LOGISTICS_STATUS[$w_option->logistics_status]
                                            @endphp
                                            @if (!empty($w_option->option))
                                                <small class="d-block mt-1">
                                                    @if ($w_option->option->sku_img_url)
                                                        <img class="lazy-img preview-image" data-src="{{ $w_option->option->sku_img_url }}" width=30 height=30/>
                                                    @else
                                                        <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                    @endif
                                                    옵션: {{ $w_option->option->option_name_kr}} 수량: {{ $w_option->quantity }} ({{ $deliveryStatus }})
                                                </small>
                                            @else
                                                <small class="d-block mt-1">
                                                    WApp에 옵션이 없음 sku_id: {{ $w_option->sku_id }} 수량: {{ $w_option->quantity }} ({{ $deliveryStatus }})
                                                </small>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($data->phas_amount)
                                            {{ $data->phas_amount }}
                                        @else
                                            X
                                        @endif
                                        <br>
                                        ({{ number_format($data->total_channel_price) }})
                                    </td>
                                    <td class="text-center">
                                        @if (isset(OrderConstant::STATUS[$data->status]))
                                            {{ OrderConstant::STATUS[$data->status] }}
                                        @else
                                            {{ $data->status }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (isset($data->refund_status) && !empty($data->refund_status) )
                                            {{ OrderConstant::REFUND_STATUS[$data->refund_status] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->created_at }}
                                        {{ $data->updated_at }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            {{-- <button class="btn btn-sm btn-success btn-update text-white" orderid={{ $data->order_id }}>주문 업데이트</button> --}}
                                            {{-- <button class="btn btn-sm btn-success btn-detail text-white" orderid={{ $data->order_id }}>주문 상세정보(W api)</button> --}}
                                            <button class="btn btn-sm btn-success btn-wapp-detail text-white" orderid={{ $data->order_id }}>주문 상세/수정</button>
                                            @if ($data->status == OrderConstant::STATUS_WAITBUYERPAY)
                                                <button class="btn btn-sm btn-danger btn-cancel text-white" orderid={{ $data->order_id }}>주문취소</button>
                                                <button class="btn btn-sm btn-dark btn-pay text-white" orderid={{ $data->order_id }}>결제하기</button>
                                            @endif
                                            @if( count($data->logistics) > 0 )
                                                <button class="btn btn-sm btn-primary btn-delivery text-white" orderid={{ $data->order_id }}>배송정보조회</button>
                                            @endif
                                        </div>
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
                            
                            <h4 class="mb-4 font-weight-bold">결제 방법</h4>
                            <div class="d-flex justify-content-center mb-4">
                                <div class="me-4 d-flex align-items-center">
                                    <input type="radio" id="payWay1" name="payWay" value="{{ OrderConstant::PAY_CROSS_BORDER }}" checked>
                                    <label for="payWay1" class="ms-2 fs-5">Cross-border Pay</label>
                                </div>
                                <div class="d-flex align-items-center d-none">
                                    <input type="radio" id="payWay2" name="payWay" value="{{ OrderConstant::PAY_ALIPAY }}">
                                    <label for="payWay2" class="ms-2 fs-5">Alipay</label>
                                </div>
                            </div>
                            <p class="text-danger mt-2 small">결제대기(미 결제) 주문만 결제가 진행됩니다.</p>
                            <a class="btn btn-outline-primary btn-md mt-3 fs-4 d-none btn-pay-link" target="_blank">결제하러 가기</a>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-secondary htmlModalClose">취소</button>
                            <button type="button" class="btn btn-primary btn-pay-check">결제 확인 완료</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <form id="modalFrm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="htmlModalLabel2">배송정보</h5>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="orderId" />
        
                                <div>
                                    <div class="d-flex flex-column px-3 mt-3">
                                        <div class="mb-2">
                                            <div class="col">
                                                <label class="d-flex align-items-center w-100 ms-2">
                                                    <span class="fs-5 fw-bold" style="width: 150px;">배송정보</span>
                                                </label>
                                            </div>
                                            <div class="col mt-2">
                                                <table class="table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th scope="col" style="width: 33%">물류코드</th>
                                                            <th scope="col" style="width: 33%">배송사</th>
                                                            <th scope="col" style="width: 33%">운송장</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="delivery-tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer d-flex justify-content-center">
                                        <button type="button" class="btn btn-primary btn-trace-delivery">배송추적</button>
                                        <button type="button" class="btn btn-secondary htmlModalClose2">닫기</button>
                                    </div>
                                    
                                    <div class="div-trace">
                                        <div class="d-flex flex-column px-3 mt-3">
                                            <div class="mb-2">
                                                <div class="col">
                                                    <label class="d-flex align-items-center w-100 ms-2">
                                                        <span class="fs-5 fw-bold" style="width: 150px;">추적정보</span>
                                                    </label>
                                                </div>
                                                <div class="col mt-2">
                                                    <table class="table">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th scope="col" style="width: 10%">logisticsId</th>
                                                                <th scope="col" style="width: 70%">내용</th>
                                                                <th scope="col" style="width: 20%">처리시간</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="trace-tbody">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $paginator->links("vendor.pagination.bootstrap-4") }}
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function() {
        var waitStatus  = "{{ OrderConstant::STATUS_WAITBUYERPAY }}";
        var optionValue = "{{ OptionConstant::NAME_EN }}";
        var exchangeRate = {{ $exchangeRate }};

        var allImgLoad       = false;
        var $scrollableTable = $("#scrollTable");
        $scrollableTable.on("scroll", function() {
            if( allImgLoad == false ){
                $(".lazy-img").each(function() {
                    var img = $(this);  // 현재 이미지 jQuery 객체
                    if (img.data('src')) { // src 속성이 없는 경우에만 실행
                        img.attr('src', img.data('src'));  // data-src 값을 src로 설정
                    }
                });

                allImgLoad = true;
            }
        });

        var channels = '{!! json_encode(MallConstant::MALL_NAME) !!}';
        channels = JSON.parse(channels);

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
            let orderId = $(this).attr("orderid");

            if(confirm(`결제 대기 중인 주문만 취소가 가능합니다.\r\n주문을 취소 하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : `/api/w/order/cancel/${orderId}`,
                    "data"       : {},
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                        location.reload();
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);

                        location.reload();
                    }
                });
            }
        });

        $('.btn-wapp-detail').click(function(){
            let orderId = $(this).attr("orderid");
            var newTab  = window.open(`/wapp/order/edit/${orderId}`, '_blank');
            newTab.focus();
            
        });

        $('.btn-trace-delivery-cn').click(function(){
            let orderId = $(this).attr("orderid");
            $('.trace-tbody').html("");

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/order/logistics/${orderId}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    if( resp.data?.length > 0 ){
                        $('.logstics-body .div-trace .trace-tbody').html("");

                        let logisticsId = resp.data[0].logisticsId;
                        resp.data.map(function(ele, key) {
                            if( logisticsId != ele.logisticsId ){
                                $('.logstics-body .div-trace .trace-tbody').append(`
                                    <tr style="height: 60px;" class="text-center">
                                        <th colspan=3>
                                        </th>
                                    </tr>
                                `);
                            }
                            let logisticsSteps = ele.logisticsSteps;
                            logisticsSteps.map(function(ele2, key2) {
                                $('.logstics-body .div-trace .trace-tbody').append(`
                                    <tr>
                                        <td>${ele.logisticsId}</td>
                                        <td>${ele2.remark}</td>
                                        <td>${ele2.acceptTime}</td>
                                    </tr>
                                `);
                            });
                        });

                        $('.logstics-body .div-trace').show();
                    } else {
                        return alert("추적 데이터가 없습니다.");
                    }
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });

        });

        $('.btn-detail').click(function(){
            let orderId = $(this).attr("orderid");

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/order/${orderId}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    $('.jsonDisplay').text(JSON.stringify(resp.data.result, null, 2));
                    $("#htmlModal3").modal('show');
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $('.btn-delivery').click(function(){
            let orderId = $(this).attr("orderid");

            $(".delivery-tbody").html("");
            $(".trace-tbody").html("");
            $("#htmlModal2 .div-trace").hide();
            $('input[name="orderId"]').val(orderId);

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/order/wapp/${orderId}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    let { logistics } = resp.data ?? [];
                    logistics?.map(function(ele, key) {
                        $('.delivery-tbody').append(`
                            <tr>
                                <td>${ele.logistics_code}</td>
                                <td>${ele.logistics_company_name}</td>
                                <td>${ele.logistics_bill_no}</td>
                            </tr>
                        `);
                    });

                    $("#htmlModal2").modal('show');
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $('.btn-trace-delivery').click(function(){
            let orderId = $('input[name="orderId"]').val();
            $('.trace-tbody').html("");

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/order/logistics/${orderId}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    if( resp.data?.length > 0 ){
                        $('#htmlModal2 .div-trace .trace-tbody').html("");
                        let logisticsId = resp.data[0].logisticsId;

                        resp.data.map(function(ele, key) {
                            let logisticsSteps = ele.logisticsSteps;

                            if( logisticsId != ele.logisticsId ){
                                $('.trace-tbody').append(`
                                    <tr style="height: 60px;" class="text-center">
                                        <th colspan=3>
                                        </th>
                                    </tr>
                                `);
                            }
                            logisticsSteps.map(function(ele2, key2) {
                                $('.trace-tbody').append(`
                                    <tr>
                                        <td>${ele.logisticsId}</td>
                                        <td>${ele2.remark}</td>
                                        <td>${ele2.acceptTime}</td>
                                    </tr>
                                `);
                            });
                        });

                        $("#htmlModal2 .div-trace").show();
                    } else {
                        return alert("추적 데이터가 없습니다.");
                    }
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });

        });

        $(".btn-order-save").click(function(){
            let formData = $("#modalFrm").serializeArray();

            let validate = true;
            $('.reqired-inp').each(function(key, ele){
                if( $(ele).val().trim() == "" ){
                    validate = false;
                    $(ele).focus();
                    alert("빈 값이 없이 모두 입력해주세요.");
                    return false;
                } else {
                    $(ele).val($(ele).val().trim());
                }
            });

            if( validate === true ){  
                if(confirm("작성하신 주문정보를 저장하시겠습니까?")){
                    $.ajax({
                        "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"       : "POST",
                        "url"        : "{{ route('w.order.orderInfoUpdate') }}",
                        "data"       : formData,
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete: function () {
                            $("#loadingOverlay").hide();
                        },
                        success: function (resp) {
                            alert(resp.msg);
                            location.reload();
                        },
                        error: function error(request, status, _error) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        });

        $('#btn-pay-select').click(function(){
            let orderIds = [];
            let payWay   = $("input[name=payWay]:checked").val();

            $(".chk-inp:checked").each(function(index, element){
                let orderStatus = $(this).attr("orderStatus");
                if( orderStatus == waitStatus ){
                    orderIds.push($(this).val());
                }
            });

            if(orderIds.length < 1){
                return alert("선택 된 주문이 없거나 결제대기 상태의 주문이 없습니다.");
            }

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
                        $('input[name="orderIds[]"]').val(orderIds);
                        $('.btn-pay-link').attr("href", resp.data.payUrl);
                        $('.btn-pay-link').removeClass("d-none");
                        $("#htmlModal").modal('show');
                    }
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $('.btn-pay').click(function(){
            let orderIds = $(this).attr("orderid");
            let payWay   = $("input[name=payWay]:checked").val();

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "POST",
                "url"        : "{{ route('w.order.orderPayLinkCreate') }}",
                "data"       : { orderIds: [orderIds], payWay },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                    $('.btn-pay-link').addClass("d-none");
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    if( resp.data.payUrl ){
                        $('input[name="orderIds[]"]').val(orderIds);
                        $('.btn-pay-link').attr("href", resp.data.payUrl);
                        $('.btn-pay-link').removeClass("d-none");
                        $("#htmlModal").modal('show');
                    }
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $(".btn-pay-check").click(function(){
            let orderIds = $('input[name="orderIds[]"]').val().split(",");
            let payWay   = $("input[name=payWay]:checked").val();

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "POST",
                "url"        : "{{ route('w.order.update') }}",
                "data"       : { orderIds },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    alert(resp.msg);
                    location.reload();
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
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
