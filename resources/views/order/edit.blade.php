@php
    use App\Constants\ProductConstant;
    use App\Constants\WConstant;
    use App\Constants\ImageConstant;
    use App\Constants\GosiConstants;
    use App\Constants\OptionConstant;
    use App\Constants\OrderConstant;
    $exchangeRate = config('1688_EXCHANGE_RATE', 200);
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
                <li class="breadcrumb-item">W App</li>
                <li class="breadcrumb-item">주문 관리</li>
                <li class="breadcrumb-item active" aria-current="page">주문 정보 수정</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
                <div class="row mb-12">
                    @php
                        $total_channel_price = 0;
                        $delivery_price      = 0;
                        foreach ($data->channel_objs as $channel_obj) {
                            $total_channel_price += $channel_obj->total_channel_price;
                            $delivery_price      += $channel_obj->delivery_price;
                        }
                    @endphp

                    <div class="text-center">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="9">WApp 주문 정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td rowspan="4" attr="main_img" style="height: 100px;">
                                        <div class="d-flex align-items-center justify-content-center" style="height: 100%;">
                                            <img class="lazy-img preview-image" src="{{ $data->product->main_img->img_url_origin}}" style="width: 80px; height: 80px">
                                        </div>
                                    </td>
                                    <th scope="col" style="width: 10%">W 주문번호</th>
                                    <td attr="order_id" colspan="2">{{ $data->order_id }}</td>
                                    <th scope="col" style="width: 10%">상품명</th>
                                    <td colspan="4">
                                        {{ $data->product->prd_name_kr }}
                                        <div attr="prd_name_kr" style="max-width: 500px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 10%">주문금액(W)</th>
                                    <td attr="total_amount">{{ $data->total_amount }}</td>
                                    <th scope="col" style="width: 10%">결제금액(W)</th>
                                    <td attr="sum_product_payment">{{ $data->sum_product_payment }}</td>
                                    <th scope="col" style="width: 10%">환불금액(W)</th>
                                    <td attr="refund_payment">{{ $data->refund_payment }}</td>
                                    <th scope="col" style="width: 10%">배송비(W)</th>
                                    <td attr="total_channel_price">{{ $data->shipping_fee }}</td>
                                </tr>
                                <tr>
                                    <th>결제금액(채널)</th>
                                    <td attr="total_channel_price" colspan="3">
                                        {{ number_format($total_channel_price) }}
                                    </td>
                                    <th>배송비(채널)</th>
                                    <td attr="shipping_fee" colspan="3">
                                        {{ number_format($delivery_price) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>주문상태</th>
                                    <td colspan="3">
                                        {{ OrderConstant::STATUS[$data->status] }}
                                    </td>
                                    <th>환불상태</th>
                                    <td colspan="3">
                                        @if (isset($data->refund_status) && !empty($data->refund_status) )
                                            {{ OrderConstant::REFUND_STATUS[$data->refund_status] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr class="opt-tr">
                                    <th scope="col" style="width: 10%">옵션ID</th>
                                    <th scope="col" style="width: 10%">옵션 이미지</th>
                                    <th scope="col" style="width: *%">옵션명</th>
                                    <th scope="col" style="width: 10%">구매 수량</th>
                                    <th scope="col" style="width: 10%">상품금액(W)</th>
                                    <th scope="col" style="width: 10%">상품금액(채널)</th>
                                    <th scope="col" style="width: 10%">옵션상태</th>
                                    <th scope="col" style="width: 10%">배송상태</th>
                                    <th scope="col" style="width: 10%">환불상태</th>
                                </tr>
                                @php
                                    $total_quantity      = 0;
                                    $total_item_amount   = 0;
                                @endphp
                                @foreach ($data->w_options as $wOption)
                                    @php
                                        $sku_img_url = "/assets/img/no_img.png";
                                        if( isset($wOption->option->sku_img_url) && $wOption->option->sku_img_url ){
                                            $sku_img_url = $wOption->option->sku_img_url;
                                        }

                                        $channel_price = 0;
                                        foreach ($data->channel_objs as $channel_obj) {
                                            foreach ($channel_obj->details as $detail) {
                                                if ($detail->option_id === $wOption->option->id) {
                                                    $channel_price += $detail->channel_price;
                                                    break;
                                                }
                                            }
                                        }

                                        $total_quantity    += (int)$wOption->quantity;
                                        $total_item_amount += $wOption->item_amount;
                                    @endphp
                                    <tr class="opt-tr-child">
                                        <td>{{ $wOption->option->id }}</td>
                                        <td>
                                            <img class="lazy-img preview-image" width="50" height="50" src="{{ $sku_img_url }}">
                                        </td>
                                        <td>
                                            <div style="max-width: 500px;">
                                                {{ $wOption->option->option_name_kr }}
                                            </div>
                                        </td>
                                        <td>{{ $wOption->quantity }}</td>
                                        <td>{{ $wOption->item_amount }}</td>
                                        <td>{{ number_format($channel_price) }}</td>
                                        <td>{{ OrderConstant::STATUS[$wOption->status] }}</td>
                                        <td>{{ OrderConstant::LOGISTICS_STATUS[$wOption->logistics_status] }}</td>
                                        <td>
                                            @if (isset($wOption->refund_status) && !empty($wOption->refund_status) )
                                                @php
                                                    $lowerCase = convertCamelCase($wOption->refund_status)["lowerCase"];
                                                @endphp
                                                @if (isset( OrderConstant::REFUND_STATUS[$lowerCase] ) )
                                                    {{ OrderConstant::REFUND_STATUS[$lowerCase] }}
                                                @else
                                                    {{ $lowerCase }}
                                                @endif
                                            @else
                                                -
                                            @endif    
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="opt-tr-child">
                                    <th colspan=3>합계</th>
                                    <td>{{ number_format($total_quantity) }}</td>
                                    <td>{{ $total_item_amount }}</td>
                                    <td>{{ number_format($total_channel_price) }}</td>
                                    <th colspan=3></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="7">배송정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($data->logistics) > 0)
                                    @php
                                        $last_logistic = $data->logistics[count($data->logistics) - 1];
                                    @endphp
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
                                @else
                                    <tr>
                                        <td colspan="7">배송정보 없음</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center div-trace" style="display: none">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3">추적정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="trace-tr">
                                    <th scope="col" style="width: 10%">logisticsId</th>
                                    <th scope="col" style="width: 70%">내용</th>
                                    <th scope="col" style="width: 20%">처리시간</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @foreach ($data->channel_objs as $key => $channel_obj)
                        <div class="text-center mt-3 channelTableDiv">
                            <table class="table table-bordered channelTable" channel_order_id="{{ $channel_obj->channel_order_id }}">
                                <thead>
                                    <tr>
                                        <th colspan="14">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div style="width: 33.33%"></div>
                                                <div style="width: 33.33%" class="text-center">
                                                    채널 기본 주문 정보
                                                </div>
                                                <div style="width: 33.33%" class="text-end">
                                                    <button class="btn btn-danger text-white btn-channel-remove btn-md" channel_order_id="{{ $channel_obj->channel_order_id }}">채널 주문 삭제</button>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="col" style="">채널 주문번호</th>
                                        <td colspan="3">
                                            <input type="text" class="form-control required-inp" name="channel_order_id" placeholder="" value="{{ $channel_obj->channel_order_id }}">
                                        </td>
                                        <th scope="col" style="">배송비</th>
                                        <td>
                                            <input type="number" class="form-control required-inp" name="delivery_price" placeholder="" value="{{ (int)$channel_obj->delivery_price }}">
                                        </td>
                                        <th scope="col" style="">구매자</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="buyer_name" placeholder="" value="{{ $channel_obj->buyer_name }}">
                                        </td>
                                        <th scope="col" style="">통관부호</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="buyer_clearance_number" placeholder="" value="{{ $channel_obj->buyer_clearance_number }}">
                                        </td>
                                        <th scope="col" style="">연락처1</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="buyer_number" placeholder="" value="{{ $channel_obj->buyer_number }}">
                                        </td>
                                        <th scope="col" style="">연락처2</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="buyer_phone" placeholder="" value="{{ $channel_obj->buyer_phone }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="col" style="">우편번호</th>
                                        <td>
                                            <input type="text" class="form-control required-inp" name="buyer_zipcode" placeholder="" value="{{ $channel_obj->buyer_zipcode }}">
                                        </td>
                                        <th scope="col" style="">주소</th>
                                        <td colspan="5">
                                            <input type="text" class="form-control required-inp" name="buyer_address" placeholder="" value="{{ $channel_obj->buyer_address }}">
                                        </td>
                                        <th scope="col" style="">메모</th>
                                        <td colspan="5">
                                            <input type="text" class="form-control required-inp" name="buyer_memo" placeholder="" value="{{ $channel_obj->buyer_memo }}">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="col">저장여부</th>
                                        <th scope="col">옵션ID</th>
                                        <th scope="col">옵션 이미지</th>
                                        <th scope="col" colspan="8">옵션명</th>
                                        <th scope="col">수량</th>
                                        <th scope="col" colspan="2">채널 가격</th>
                                    </tr>
                                    @foreach ($data->w_options as $wOption)
                                        @foreach ($channel_obj->details as $detail)
                                            @php
                                                $key = 0;
                                            @endphp
                                            @if( $detail->option_id == $wOption->option->id )
                                                @php
                                                    $sku_img_url = "/assets/img/no_img.png";
                                                    if( isset($wOption->option->sku_img_url) && $wOption->option->sku_img_url ){
                                                        $sku_img_url = $wOption->option->sku_img_url;
                                                    }

                                                    $key++;
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <input class="form-check-input chk-inp" type="checkbox" checked channel_order_id={{ $channel_obj->channel_order_id}} value="{{ $detail->option_id }}">
                                                    </td>
                                                    <td>{{ $detail->option_id }}</td>
                                                    <td><img class="lazy-img preview-image" width="50" height="50" src="{{ $sku_img_url }}"></td>
                                                    <td colspan="8">{{ $wOption->option->option_name_kr }}</td>
                                                    <td>
                                                        <input type="number" class="form-control" name="quantity" placeholder="" value="{{ $detail->quantity }}">
                                                    </td>
                                                    <td colspan="2">
                                                        <input type="number" class="form-control" name="channel_price" placeholder="" value="{{ (int)$detail->channel_price }}">
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    @endforeach
                    
                    <div class="mt-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-success mx-2 btn-add text-white">주문 정보 추가</button>
                        <button type="button" class="btn btn-danger mx-2 btn-remove text-white">주문 정보 삭제</button>
                        <button type="button" class="btn btn-primary mx-2 btn-save">주문 정보 저장</button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">

        $(document).ready(function(){

            var options = '{!! json_encode($data->w_options) !!}';
            options = JSON.parse(options);

            var order_id = "{{ $data->order_id }}";

            $(document).on("click", '.chk-inp', function(){
                let option_id = $(this).val();
                let checked   = $(this).is(":checked");
                
                $(`.chk-inp[value=${option_id}]`).prop("checked", checked);
            });

            $('.btn-trace-delivery-cn').click(function(){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "GET",
                    "url"        : `/api/w/order/logistics/${order_id}`,
                    "data"       : {},
                    beforeSend: function () {
                        $('.trace-tr').nextAll().remove();
                        $("#loadingOverlay").show();
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        if( resp.data?.length > 0 ){
                            let logisticsId = resp.data[0].logisticsId;
                            resp.data.forEach(function(ele) {
                                if( logisticsId != ele.logisticsId ){
                                    $('.trace-tr').after(`
                                        <tr style="height: 60px;" class="text-center">
                                            <th colspan=3>
                                            </th>
                                        </tr>
                                    `);
                                }
                                ele.logisticsSteps.forEach(function(step) {
                                    $('.trace-tr').after(`
                                        <tr>
                                            <td>${ele.logisticsId}</td>
                                            <td>${step.remark}</td>
                                            <td>${step.acceptTime}</td>
                                        </tr>
                                    `);
                                });
                            });

                            $('.div-trace').show();
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

            $(".btn-channel-remove").click(function(){
                let channel_order_id = $(this).attr("channel_order_id");

                if( confirm("해당 채널 정보를 삭제하시겠습니까?") ){
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "delete",
                        "url"     : "{{ route('w.order.orderInfoChannelDelete') }}",
                        "data"    : {
                            order_id        : order_id,
                            channel_order_id: channel_order_id
                        },
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
            });

            $(".btn-save").click(function(){
                let validate = true;
                $(".required-inp").each(function(){
                    if($(this).val().trim() == ""){
                        alert("빈 값없이 입력해주세요.");
                        validate = false;
                        $(this).focus();
                        return false;
                    }
                });

                if( !validate ) return false;

                let payload = {
                    "order_id"       : order_id,
                    "change_channels": [],
                    "add_channels"   : []
                };
                let optValid = true;
                $(".channelTable").each(function(ele){
                    if (!optValid) return false;

                    let channelData = {
                        channel_order_id: $(this).attr("channel_order_id"),
                        options: []
                    };

                    $(this).find('input').each(function() {
                        if ($(this).hasClass('chk-inp') && this.checked) {
                            let tr_row            = $(this).closest("tr");
                            let quantityInput     = $(tr_row).find('input[name=quantity]');
                            let channelPriceInput = $(tr_row).find('input[name=channel_price]');
                            let quantity          = Number(quantityInput.val());
                            let channel_price     = Number(channelPriceInput.val());

                            if (quantity === "") {
                                alert("수량을 입력해주세요.");
                                quantityInput.focus();
                                optValid = false;
                                return false;
                            }

                            if ( quantity != 0 && channelPriceInput.val().trim() == "" ) {
                                alert("채널 가격을 입력해주세요.");
                                channelPriceInput.focus();
                                optValid = false;
                                return false;
                            }

                            channelData.options.push({
                                "opt_id"       : $(this).val(),
                                "quantity"     : quantity,
                                "channel_price": channel_price
                            });
                        } else if($(this).hasClass("required-inp")){
                            channelData[$(this).attr('name')] = $(this).val();
                        }
                    });

                    if (!optValid) {
                        return false;
                    }

                    payload.change_channels.push(channelData);
                });
                if( !optValid ) return false;

                let optValid2 = true;
                $(".channelAddTable").each(function(ele){
                    if (!optValid2) return false;

                    let channelData = {
                        channel_order_id: $(this).attr("channel_order_id"),
                        options: []
                    };

                    $(this).find('input').each(function() {
                        if ($(this).hasClass('chk-inp') && this.checked) {
                            let tr_row            = $(this).closest("tr");
                            let quantityInput     = $(tr_row).find('input[name=quantity]');
                            let channelPriceInput = $(tr_row).find('input[name=channel_price]');
                            let quantity          = Number(quantityInput.val());
                            let channel_price     = Number(channelPriceInput.val());

                            if ( quantity === "" ) {
                                alert("수량을 입력해주세요.");
                                quantityInput.focus();
                                optValid2 = false;
                                return false;
                            }

                            if ( quantity != 0 && channelPriceInput.val().trim() === "" ) {
                                alert("채널 가격을 입력해주세요.");
                                channelPriceInput.focus();
                                optValid2 = false;
                                return false;
                            }

                            if( quantity != 0 ){
                                channelData.options.push({
                                    "opt_id"       : $(this).val(),
                                    "quantity"     : quantity,
                                    "channel_price": channel_price
                                });
                            }
                        } else if($(this).hasClass("required-inp")){
                            channelData[$(this).attr('name')] = $(this).val();
                        }
                    });

                    if (!optValid2) {
                        return false;
                    }

                    payload.add_channels.push(channelData);
                });
                if( !optValid2 ) return false;

                if( confirm("주문을 저장하시겠습니까?") ){
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "patch",
                        "url"     : "{{ route('w.order.orderInfoChannelUpdate') }}",
                        "data"    : payload,
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
            });

            $(".btn-add").click(function(){
                let channelTableHtml = 
                `
                    <div class="text-center mt-3 channelAddTableDiv">
                        <table class="table table-bordered channelAddTable">
                            <thead>
                                <tr>
                                    <th colspan="14">
                                        채널 추가 주문 정보
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col" style="">채널 주문번호</th>
                                    <td colspan="3">
                                        <input type="text" class="form-control required-inp" name="channel_order_id" placeholder="" value="">
                                    </td>
                                    <th scope="col" style="">배송비</th>
                                    <td>
                                        <input type="number" class="form-control required-inp" name="delivery_price" placeholder="" value="">
                                    </td>
                                    <th scope="col" style="">구매자</th>
                                    <td>
                                        <input type="text" class="form-control required-inp" name="buyer_name" placeholder="" value="">
                                    </td>
                                    <th scope="col" style="">통관부호</th>
                                    <td>
                                        <input type="text" class="form-control required-inp" name="buyer_clearance_number" placeholder="" value="">
                                    </td>
                                    <th scope="col" style="">연락처1</th>
                                    <td>
                                        <input type="text" class="form-control required-inp" name="buyer_number" placeholder="" value="">
                                    </td>
                                    <th scope="col" style="">연락처2</th>
                                    <td>
                                        <input type="text" class="form-control required-inp" name="buyer_phone" placeholder="" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col" style="">우편번호</th>
                                    <td>
                                        <input type="text" class="form-control required-inp" name="buyer_zipcode" placeholder="" value="">
                                    </td>
                                    <th scope="col" style="">주소</th>
                                    <td colspan="5">
                                        <input type="text" class="form-control required-inp" name="buyer_address" placeholder="" value="">
                                    </td>
                                    <th scope="col" style="">메모</th>
                                    <td colspan="5">
                                        <input type="text" class="form-control required-inp" name="buyer_memo" placeholder="" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="col">저장여부</th>
                                    <th scope="col">옵션ID</th>
                                    <th scope="col">옵션 이미지</th>
                                    <th scope="col" colspan="8">옵션명</th>
                                    <th scope="col">수량</th>
                                    <th scope="col" colspan="2">채널 가격</th>
                                </tr>
                `;

                options.map(function(ele, key) {
                    let sku_img_url = "/assets/img/no_img.png";
                    if( ele.option.sku_img_url ){
                        sku_img_url = ele.option.sku_img_url;
                    }
                    let checked = $(`.chk-inp[value=${ele.option.id}]`).is(":checked") ? "checked" : "";

                    channelTableHtml += `
                        <tr>
                            <td class="text-center">
                                <input class="form-check-input chk-inp" ${checked} type="checkbox" value="${ele.option.id}">
                            </td>
                            <td>${ele.option.id}</td>
                            <td><img class="lazy-img preview-image" width="50" height="50" src="${sku_img_url}"></td>
                            <td colspan="8">${ele.option.option_name_kr}</td>
                            <td>
                                <input type="number" class="form-control" name="quantity" placeholder="" value="0">
                            </td>
                            <td colspan="2">
                                <input type="number" class="form-control" name="channel_price" placeholder="" value="">
                            </td>
                        </tr>
                    `;
                });

                channelTableHtml += 
                `
                        </tbody>
                    </table>
                </div>
                `;

                if( $('.channelAddTableDiv').length > 0 ){
                    $(".channelAddTableDiv").last().after(channelTableHtml);
                } else {
                    $(".channelTableDiv").last().after(channelTableHtml);
                }
            });

            $('.btn-remove').click(function(){
                if( $('.channelAddTableDiv').length > 0 ){
                    $(".channelAddTableDiv").last().remove();
                } else {
                    return alert("삭제할 채널 추가 주문 정보가 없습니다.");
                }
            });

        })
    </script>

@endsection
