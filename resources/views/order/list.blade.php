@php
    use App\Constants\MallConstant;
    use App\Constants\OrderConstant;
    use App\Constants\OptionConstant;

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
                <li class="breadcrumb-item active" aria-current="page">WApp 주문 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="orderChannel" value={{ $orderChannel }}>
                    <input type="hidden" name="orderStatus" value={{ $orderStatus }}>
                    <input type="hidden" name="refundStatus" value={{ $refundStatus }}>

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
                                    <th style="width: 120px">배송상태 상태</th>
                                    <td colspan="2">
                                        <button type="button" name="deliveryStatus" class="btn-status btn btn-sm {{ $deliveryStatus == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        @foreach (OrderConstant::LOGISTICS_STATUS as $statusKey => $statusValue)    
                                            <button type="button" name="deliveryStatus" class="btn-status btn btn-sm {{ $deliveryStatus == $statusKey ? "btn-primary" : "btn-dark" }}"
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
                                            <option value=300 @if($pageSize == 300) selected @endif>300개 노출</option>
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
                                <th scope="col" style="width: 10%">W 주문번호<br>(채널 주문번호)</th>
                                <th scope="col" style="width: 5%">구매자<br>(주문 채널)</th>
                                <th scope="col" style="width: 5%">상품 이미지</th>
                                <th scope="col" style="width: 5%">주문정보</th>
                                <th scope="col" style="width: 5%">W 금액<br>(채널 금액)</th>
                                <th scope="col" style="width: 8%">주문상태</th>
                                <th scope="col" style="width: 8%">배송상태</th>
                                <th scope="col" style="width: 8%">환불상태</th>
                                <th scope="col" style="width: 8%">주문 생성일<br>주문 수정일</th>
                                <th scope="col" style="width: 6%">관리</th>
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
                                        <small>({{ $data->channel }})</small>
                                    </td>
                                    <td>
                                        @if (!empty($data->product->main_img))
                                            <img class="lazy-img preview-image" data-src="{{ $data->product->main_img->img_url_origin }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $data->product->prd_name_kr }}</small>
                                        <div class="mt-1"></div>
                                        @foreach ($data->w_options as $w_option)
                                            @if (!empty($w_option->option))
                                                <small class="d-block">
                                                    옵션: {{ $w_option->option->option_name_kr}} 수량: {{ $w_option->quantity }}
                                                </small>
                                            @else
                                                <small class="d-block">
                                                    WApp에 옵션이 없음 sku_id: {{ $w_option->sku_id }} 수량: {{ $w_option->quantity }}
                                                </small>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                           {{ number_format($data->phas_amount) }}
                                           <br>
                                           ({{ number_format($data->totla_channel_price) }})
                                    </td>
                                    <td>
                                        {{ OrderConstant::STATUS[$data->status] }}
                                    </td>
                                    <td>
                                        @if (isset($data->delivery_status))
                                            {{ OrderConstant::LOGISTICS_STATUS[$data->delivery_status] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($data->retund_status))
                                            {{ OrderConstant::REFUND_STATUS[$data->retund_status] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data["baseObj"] && $data["channelObj"])
                                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                                <button class="btn btn-sm btn-success btn-update text-white" orderid={{ $data->order_id }}>주문 업데이트</button>
                                                <button class="btn btn-sm btn-danger btn-cancel text-white" orderid={{ $data->order_id }}>취소/환불</button>
                                                @if( $data->status == OrderConstant::STATUS_WAITBUYERPAY )
                                                    <button class="btn btn-sm btn-dark btn-pay text-white" orderid={{ $data->order_id }}>결제하기</button>
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

            <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <form id="modalFrm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="htmlModalLabel2">주문 정보 입력</h5>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="orderId" />
        
                                <div>
                                    <div class="d-flex align-items-center">
                                        <label class="fs-4">1688 주문정보</label>
                                    </div>
                                    <div class="d-flex flex-column px-3 mt-3">
                                        <div class="row w-100 mb-2">
                                            <div class="col d-flex align-items-center">
                                                <label class="d-flex align-items-center w-100 ms-2">
                                                    <span class="fs-5 fw-bold" style="width: 150px;">주문번호</span>
                                                    <span class="ms-4 sp-orderid"></span>
                                                </label>
                                            </div>
                                            <div class="col d-flex align-items-center">
                                                <label class="d-flex align-items-center w-100 ms-2">
                                                    <span class="fs-5 fw-bold" style="width: 150px;">상품번호(offer_id)</span>
                                                    <span class="ms-4 sp-offerid"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-2 mt-1">
                                            <div class="col">
                                                <label class="d-flex align-items-center w-100 ms-2">
                                                    <span class="fs-5 fw-bold" style="width: 150px;">옵션정보</span>
                                                </label>
                                            </div>
                                            <div class="col mt-2">
                                                <table class="table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th scope="col" style="width: 1%">No</th>
                                                            <th scope="col" style="width: 3%">sku_id</th>
                                                            <th scope="col" style="width: 6%">spec_id</th>
                                                            <th scope="col" style="width: 6%">옵션명</th>
                                                            <th scope="col" style="width: 2%">수량</th>
                                                            <th scope="col" style="width: 3%">1688 가격(위안)</th>
                                                            <th scope="col" style="width: 3%">채널가격(원화)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="opt-tbody">
                                                    </tbody>
                                                </table>
                                                <p class="text-danger mt-2 small">* 채널가격: 채널의 판매가 입력(미입력 시, 1688의 가격(환율적용)이 입력됩니다.)</p>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
        
                                    <div class="d-flex align-items-center">
                                        <label class="fs-4">채널 주문정보</label>
                                    </div>
                                    <div class="d-flex flex-column px-3 mt-3">
                                        <div class="row w-100 mb-2">
                                            <div class="col d-flex align-items-center">
                                                <span class="fs-5 fw-bold" style="width: 150px;">주문채널</span>
                                                <div class="ms-3">
                                                    @foreach (MallConstant::MALL_LIST as $key => $mall)    
                                                        <input type="radio" id="option_{{ $mall }}" name="orderChannel" value="{{ $mall }}" @if($key == 0) checked @endif>
                                                        <label for="option_{{ $mall }}" class="me-4 cursor-pointer">{{ MallConstant::MALL_NAME[$mall] }}</label>
                                                    @endforeach
                                                </div>
                                                <span class="text-danger small ms-2">* 주문 채널을 선택하세요.</span>
                                            </div>
                                        </div>
                                        <div class="row w-100 mb-2">
                                            <div class="col d-flex align-items-center">
                                                <span class="fs-5 fw-bold" style="width: 150px;">채널 주문번호</span>
                                                <div class="ms-3">
                                                    <input type="text" class="form-control reqired-inp" name="channelOrderId" value="">
                                                </div>
                                                <span class="text-danger small ms-2">* WApp 주문의 경우 주문 사유를 입력하세요.</span>
                                            </div>
                                        </div>
                                        
                                        <div class="row w-100 mt-2">
                                            <div class="col d-flex align-items-center">
                                                <span class="fs-5 fw-bold">구매자정보</span>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">이름</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control reqired-inp" name="buyerName" value="">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">통관번호</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control reqired-inp" name="buyerClearanceNumber" value="">
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">전화번호</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control reqired-inp" name="buyerNumber" value="">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">휴대폰번호</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control reqired-inp" name="buyerPhone" value="">
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">상세주소</small>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control reqired-inp" name="buyerAddress" value="">
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">우편번호</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control reqired-inp" name="buyerZipcode" value="">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">채널 배송비(원화)</small>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="number" class="form-control reqired-inp" name="deliveryPrice" value=0>
                                                </div>
                                            </div>
                                            <div class="row mb-2">
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <small class="fw-bold text-center" style="width: 100px;">메모</small>
                                                </div>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control reqired-inp" name="buyerMemo" value="">
                                                </div>
                                            </div>
                                        </div>

                                        <span class="text-danger small mt-2">* 모든 정보 입력 필수</span>
                                    </div>
        
                                </div>
                            </div>
                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" class="btn btn-primary btn-order-save">저장</button>
                                <button type="button" class="btn btn-secondary htmlModalClose2">취소</button>
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

        $(".btn-regist").click(function(){
            let orderId = $(this).attr("orderid");
            $(".opt-tbody").html("");
            $('.reqired-inp').val("");

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/order/${orderId}`,
                "data"       : { },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    $('input[name="orderId"]').val(orderId);

                    let { baseInfo, productItems } = resp.data.result;
                    let offerId = productItems[0].productID;
                    $(".sp-orderid").text(baseInfo.idOfStr);
                    $(".sp-offerid").text(offerId);

                    productItems.map(function(ele, key) {
                        let skuId    = ele.skuID ?? offerId;
                        let specId   = ele.specId ?? offerId;
                        let optValue = ele?.skuInfos ? "" : optionValue;
                        ele?.skuInfos?.map(function(ele2, key2){
                            if( ele?.skuInfos?.length == key2+1 ){
                                optValue += ele2.value;
                            } else {
                                optValue += ele2.value + "_";
                            }
                        });

                        let channelPrice = Math.round( ( ele.price * exchangeRate) / 10) * 10;
                        $(".opt-tbody").append(`
                            <tr>
                                <td>${key+1}</td>
                                <td><input type="number" class="form-control" name="skuIds[]" value="${skuId}" disabled></td>
                                <td><input type="text" class="form-control" name="specIds[]" value="${specId}" disabled></td>
                                <td>${optValue}</td>
                                <td>${ele.quantity}</td>
                                <td>${ele.price}</td>
                                <td><input type="number" class="form-control" name="channelPrices[]" value="${channelPrice}"></td>
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

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
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
