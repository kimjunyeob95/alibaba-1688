@php
    use App\Constants\WmsConstant;
    use App\Constants\BonaeraConstant;
    use App\Constants\OrderConstant;
    use App\Models\BonaeraOutBaseData;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .has-child td{
        border-bottom: none !important;
    }

    tr:not(.has-child) {
        border-bottom-width: 1px !important;
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
                <li class="breadcrumb-item active" aria-current="page">출고 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="status" value={{ $status }}>
                    <input type="hidden" name="clearance_type" value={{ $clearanceType }}>
                    <input type="hidden" name="shipping_type" value={{ $shippingType }}>
                    <input type="hidden" name="unipass_type" value={{ $unipassType }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">출고 상태</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap m-n1">
                                            <button type="button" name="status" class="btn-status btn btn-sm m-1 {{ $status == '' ? 'btn-primary' : 'btn-dark' }}"
                                                value="">전체</button>
                                            @foreach (WmsConstant::OUT_LIST_STATUS as $key => $value)    
                                                <button type="button" name="status" class="btn-status btn btn-sm m-1 {{ $status == $key ? 'btn-primary' : 'btn-dark' }}"
                                                    value="{{ $key }}">{{ $value }}</button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">타입</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap m-n1">
                                            <button type="button" name="clearance_type" class="btn-status btn btn-sm m-1 {{ $clearanceType == '' ? 'btn-primary' : 'btn-dark' }}"
                                                value="">전체</button>
                                            @foreach (OrderConstant::CLEARANCE_TYPE as $key => $value)    
                                                <button type="button" name="clearance_type" class="btn-status btn btn-sm m-1 {{ $clearanceType == $key ? 'btn-primary' : 'btn-dark' }}"
                                                    value="{{ $key }}">{{ $value }}</button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">운송방법</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap m-n1">
                                            <button type="button" name="shipping_type" class="btn-status btn btn-sm m-1 {{ $shippingType == '' ? 'btn-primary' : 'btn-dark' }}"
                                                value="">전체</button>
                                            @foreach (BonaeraConstant::CTR_NUM as $key => $value)    
                                                <button type="button" name="shipping_type" class="btn-status btn btn-sm m-1 {{ $shippingType == $key ? 'btn-primary' : 'btn-dark' }}"
                                                    value="{{ $key }}">{{ $value }}</button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">통관부호</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap m-n1">
                                            @foreach (BonaeraConstant::UNIPASS_RESULT as $key => $value)    
                                                <button type="button" name="unipass_type" class="btn-status btn btn-sm m-1 {{ $unipassType == $key ? 'btn-primary' : 'btn-dark' }}"
                                                    value="{{ $key }}">{{ $value }}</button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">기간</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="time_cls">
                                            <option value="order" @if($timeCls == "order") selected @endif>출고 지시일</option>
                                            <option value="complete" @if($timeCls == "complete") selected @endif>출고 완료일</option>
                                        </select>
                                    </td>
                                    <td>
                                        <div class="input-group" style="width: 600px;">
                                            <input type="text" class="form-control calendar" autocomplete="off" name="start_time" placeholder="시작일" value="{{ $startTime }}">
                                            <span class="input-group-text">~</span>
                                            <input type="text" class="form-control calendar" autocomplete="off" name="end_time" placeholder="종료일" value="{{ $endTime }}">
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            @foreach (WmsConstant::OUT_SEARCH_TYPE as $key => $search)
                                                <option value="{{ $key }}" @if($search_cls == $key) selected @endif>{{ $search }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="검색어를 입력해주세요. 다중 검색 시 ,(콤마)로 구분 지어주세요.">{!! $keyword !!}</textarea>
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

                <div class="mt-3 d-flex justify-content-end">
                    <div class="d-flex">
                        <button class="btn btn-md btn-dark text-white me-2" id="btn-select">출고정보 업데이트</button>
                    </div>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 3%">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <br>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 1%">No</th>
                                <th scope="col" style="width: 5%">
                                    배송번호<br>
                                    출고상태
                                </th>
                                <th scope="col" style="width: 5%">
                                    출고번호<br>
                                    채널 주문번호<br>
                                    W 주문번호
                                </th>
                                <th scope="col" style="width: 20%">출고정보</th>
                                <th scope="col" style="width: 5%">수령인</th>
                                <th scope="col" style="width: 5%">운송정보</th>
                                <th scope="col" style="width: 5%">배송금액</th>
                                <th scope="col" style="width: 8%">
                                    출고 지시일<br>
                                    출고 완료일
                                </th>
                                <th scope="col" style="width: 5%">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator->items() as $index => $data)
                                @php
                                    $rowSpan        = 1;
                                    $subTrClassName = "";
                                    if( count($data->otherObjs) > 0 ){
                                        $rowSpan = count($data->otherObjs) + 1;
                                        $subTrClassName = "has-child";
                                    }
                                @endphp
                                <tr class="{{ $subTrClassName }}">
                                    <td class="text-center" rowspan={{ $rowSpan }}>
                                        <input id="checkbox-{{ $data->id }}" class="form-check-input chk-inp" 
                                        type="checkbox" value="{{ $data->id }}">
                                    </td>
                                    <td rowspan={{ $rowSpan }}>
                                        <small>{{ number_format(($paginator->total() - $offset) - $index) }}</small>
                                    </td>
                                    <td rowspan={{ $rowSpan }}>
                                        <small>{{ $data->group_no }}</small>
                                        <br>
                                        @if ($data->delivery_state)
                                            <small>({{ BonaeraConstant::GROUP_STATUS[$data->delivery_state] }})</small>
                                        @else
                                            <small>(출고상태X)</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $data->sh_no }}</small>
                                        <br>
                                        <small>{{ $data->channel_order_id }}</small>
                                        <br>
                                        <small>{{ $data->order_id }}</small>
                                        <br>
                                        <button class="btn btn-sm btn-primary btn-in-detail text-white" data-stockno={{ $data->stock_no }}>입고상세</button>
                                    </td>
                                    <td>
                                        <small>
                                            (<a href="https://detail.1688.com/offer/{{ $data->order->offer_id }}.html" target="_blank">{{ $data->order->offer_id }}</a>)
                                            {{ $data->order->product->prd_name_kr }}
                                        </small>
                                        <div class="mt-3"></div>
                                        @foreach ($data->out_options as $out_option)
                                            @php
                                                $w_option = $out_option->w_option;
                                            @endphp
                                            @if (!empty($w_option))
                                                <small class="d-block mt-1">
                                                    @if ($w_option->sku_img_url)
                                                        <img class="lazy-img preview-image" data-src="{{ $w_option->sku_img_url }}" width=30 height=30/>
                                                    @else
                                                        <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                    @endif
                                                    옵션: {{ $w_option->option_name_kr}},
                                                    신청수량: {{ number_format($out_option->quantity) }},
                                                    출고수량: {{ number_format($out_option->shipped_qty) }}
                                                </small>
                                            @else
                                                <small class="d-block mt-1">
                                                    WApp에 옵션이 없음 option_id: {{ $out_option->option_id }}
                                                </small>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td rowspan={{ $rowSpan }}>
                                        {{ $data->receiver_name }}
                                        <br>
                                        @if (!empty($data->unipass_reason))   
                                            <small class="text-danger">{{ $data->unipass_reason }}</small>
                                        @else
                                            <small>{{ $data->personal_num }}</small>
                                        @endif
                                        <br>
                                        ({{ OrderConstant::CLEARANCE_TYPE[$data->clearance_type]}})
                                    </td>
                                    <td rowspan={{ $rowSpan }}>
                                        <small>
                                            @if (empty($data->invoice))
                                                운송장없음
                                            @else
                                                {{ $data->invoice }}
                                            @endif
                                        </small>
                                        <br>
                                        <small>
                                            ({{ BonaeraConstant::CTR_NUM[$data->ctr_num] }})
                                        </small>
                                    </td>
                                    <td rowspan={{ $rowSpan }}>
                                        <small>
                                            @if ($data->out_weight)
                                                {{ number_format($data->out_weight->total_money) }}
                                            @else
                                                0                                                
                                            @endif
                                        </small>
                                        @if ($data->out_weight && $data->delivery_state === BonaeraConstant::GROUP_STATUS_304)
                                            <button class="btn btn-sm btn-danger btn-pay text-white" data-id={{ $data->id }}>결제하기</button>
                                            @if ($data->pay_fail_log)
                                                <br>
                                                <small class="text-danger">통신실패사유: {{ $data->pay_fail_log->message }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $data->out_ordered_at }}
                                        </small>
                                        <br>
                                        <small>
                                            {{ $data->out_completed_at }}
                                        </small>
                                    </td>
                                    <td rowspan={{ $rowSpan }}>
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-sm btn-dark btn-update text-white" data-id={{ $data->id }}>출고정보<br>업데이트</button>
                                            <button class="btn btn-sm btn-primary btn-out-detail text-white" data-groupno={{ $data->group_no }}>출고상세</button>
                                            <button class="btn btn-sm btn-success btn-wapp-detail text-white" orderid={{ $data->order_id }}>주문 상세</button>
                                        </div>
                                    </td>
                                </tr>
                                @foreach ($data->otherObjs as $otherObj)
                                    <tr class="{{ $subTrClassName }}">
                                        <td>
                                            <small>{{ $otherObj->sh_no }}</small>
                                            <br>
                                            <small>{{ $otherObj->channel_order_id }}</small>
                                            <br>
                                            <small>{{ $otherObj->order_id }}</small>
                                            <br>
                                            <button class="btn btn-sm btn-primary btn-in-detail text-white" data-stockno={{ $otherObj->stock_no }}>입고상세</button>
                                        </td>
                                        <td>
                                            <small>
                                                (<a href="https://detail.1688.com/offer/{{ $otherObj->order->offer_id }}.html" target="_blank">{{ $otherObj->order->offer_id }}</a>)
                                                {{ $otherObj->order->product->prd_name_kr }}
                                            </small>
                                            <div class="mt-3"></div>
                                            @foreach ($otherObj->out_options as $out_option)
                                                @php
                                                    $w_option = $out_option->w_option;
                                                @endphp
                                                @if (!empty($w_option))
                                                    <small class="d-block mt-1">
                                                        @if ($w_option->sku_img_url)
                                                            <img class="lazy-img preview-image" data-src="{{ $w_option->sku_img_url }}" width=30 height=30/>
                                                        @else
                                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                        @endif
                                                        옵션: {{ $w_option->option_name_kr}},
                                                        신청수량: {{ number_format($out_option->quantity) }},
                                                        출고수량: {{ number_format($out_option->shipped_qty) }}
                                                    </small>
                                                @else
                                                    <small class="d-block mt-1">
                                                        WApp에 옵션이 없음 option_id: {{ $out_option->option_id }}
                                                    </small>
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            <small>
                                                {{ $otherObj->out_ordered_at }}
                                            </small>
                                            <br>
                                            <small>
                                                {{ $otherObj->out_completed_at }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $paginator->links("vendor.pagination.bootstrap-4") }}
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $(".btn-status").click(function(){
            let name  = $(this).attr("name");
            let value = $(this).val();

            $(`input[name=${name}]`).val(value);
            $("#searchFrm").submit();
        });

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $('.btn-wapp-detail').click(function(){
            let orderId = $(this).attr("orderid");
            var newTab  = window.open(`/wapp/order/edit/${orderId}`, '_blank');
            newTab.focus(); 
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

        $("#btn-select").click(function(){
            let ids = [];

            $(".chk-inp:checked").each(function(index, element){
                ids.push($(this).val());
            });

            if(ids.length < 1){
                return alert("선택 된 정보가 없습니다.");
            }

            if(confirm(`${ids.length}건의 출고정보를 업데이트 하시겠습니까?`)){
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

        $(".btn-in-detail").click(function(){
            let stockno = $(this).data("stockno");
            var newTab  = window.open(`/wapp/wms/in/${stockno}`, '_blank');
            newTab.focus(); 
        })

        $(".btn-out-detail").click(function(){
            let groupno = $(this).data("groupno");
            var newTab  = window.open(`/wapp/wms/out/${groupno}`, '_blank');
            newTab.focus(); 
        })

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
    });
</script>

@endsection
