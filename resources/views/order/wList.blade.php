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
                <li class="breadcrumb-item active" aria-current="page">W 주문 리스트</li>
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
                                        @foreach (OrderConstant::STATUS_FILTER as $statusKey => $statusValue)    
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
                                <th scope="col" style="width: 8%">옵션명<br>sku_ID<br>(제품ID)</th>
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
                                @php
                                    $itemOrderStatus = "";
                                    if( $data["baseObj"] && $data["channelObj"] ){
                                        $itemOrderStatus = $data["baseInfo"]["status"];
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data["baseInfo"]['idOfStr'] }}" class="form-check-input chk-inp" 
                                        type="checkbox" value="{{ $data["baseInfo"]['idOfStr'] }}" orderStatus={{ $itemOrderStatus }}>
                                    </td>
                                    <td>
                                        <label for="checkbox-{{ $data["baseInfo"]['idOfStr'] }}" class="cursor-pointer">
                                            {{ number_format(($paginator->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td>
                                        <small>{{ $data["baseInfo"]['idOfStr'] }}</small>
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
                                            <table style="height: 150px;">
                                                <tr>
                                                    <td>
                                                        <div>
                                                            @if (isset($prdItem["skuInfos"]))
                                                                @php
                                                                    $optionValue = "";        
                                                                @endphp
                                                                @foreach ($prdItem["skuInfos"] as $skuInfo)
                                                                    @php
                                                                        $optionValue .= $skuInfo["value"] .  "_";
                                                                    @endphp
                                                                @endforeach
                                                                @php
                                                                    $optionValue = rtrim($optionValue, "_");
                                                                @endphp
                                                                <small>{{ $optionValue }}</small>
                                                            @else
                                                                <small>{{ OptionConstant::NAME_EN }}</small>
                                                            @endif
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
                                            <button class="btn btn-sm btn-primary btn-regist text-white" orderid={{ $data["baseInfo"]['idOfStr'] }}>정보 입력</button>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data["baseObj"] && $data["channelObj"])
                                            <div style="display: flex; flex-direction: column; gap: 5px;">
                                                <button class="btn btn-sm btn-success btn-update text-white" orderid={{ $data["baseInfo"]['idOfStr'] }}>주문 업데이트</button>
                                                @if ($data["baseInfo"]["status"] == OrderConstant::STATUS_WAITBUYERPAY)
                                                    <button class="btn btn-sm btn-danger btn-cancel text-white" orderid={{ $data["baseInfo"]['idOfStr'] }}>주문취소</button>
                                                    <button class="btn btn-sm btn-dark btn-pay text-white" orderid={{ $data["baseInfo"]['idOfStr'] }}>결제하기</button>
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
