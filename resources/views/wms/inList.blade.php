@php
    use App\Constants\WmsConstant;
    use App\Constants\BonaeraConstant;
    use App\Models\ProductOptionData;
@endphp
@extends('dashboard.base')

@section('styles')
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
                <li class="breadcrumb-item active" aria-current="page">입고 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="status" value={{ $status }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">입고 상태</th>
                                    <td colspan="2">
                                        <button type="button" name="status" class="btn-status btn btn-sm {{ $status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        @foreach (BonaeraConstant::WAREHOUSE_STATUS as $key => $value)    
                                            <button type="button" name="status" class="btn-status btn btn-sm {{ $status == $key ? "btn-primary" : "btn-dark" }}"
                                            value="{{ $key }}">{{ $value }}</button>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">기간</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="timeCls">
                                            <option value="create" @if($timeCls == "create") selected @endif>입고 신청일</option>
                                            <option value="complete" @if($timeCls == "complete") selected @endif>입고 완료일</option>
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
                                            @foreach (WmsConstant::IN_SEARCH_TYPE as $key => $search)
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
                        <button class="btn btn-md btn-dark text-white me-2" id="btn-select">입고정보 업데이트</button>
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
                                    입고번호<br>
                                    (W 주문번호)
                                </th>
                                <th scope="col" style="width: 5%">채널 주문번호</th>
                                <th scope="col" style="width: 20%">입고정보</th>
                                <th scope="col" style="width: 5%">배송정보</th>
                                <th scope="col" style="width: 8%">
                                    입고 신청일<br>
                                    입고 완료일
                                </th>
                                <th scope="col" style="width: 5%">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator->items() as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data->id }}" class="form-check-input chk-inp" 
                                        type="checkbox" value="{{ $data->id }}">
                                    </td>
                                    <td>
                                        <small>{{ number_format(($paginator->total() - $offset) - $index) }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->stock_no }}</small>
                                        <br>
                                        <small>({{ $data->order_id }})</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->channel_order_id }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            (<a href="https://detail.1688.com/offer/{{ $data->offer_id }}.html" target="_blank">{{ $data->offer_id }}</a>)
                                            {{ $data->product->prd_name_kr }}
                                        </small>
                                        <div class="mt-3"></div>
                                        @foreach ($data->in_options as $in_option)
                                            @php
                                                $w_option = ProductOptionData::where("id", $in_option->option_id)->first();
                                            @endphp
                                            @if (!empty($w_option))
                                                <small class="d-block mt-1">
                                                    @if ($w_option->sku_img_url)
                                                        <img class="lazy-img preview-image" data-src="{{ $w_option->sku_img_url }}" width=30 height=30/>
                                                    @else
                                                        <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                    @endif
                                                    옵션: {{ $w_option->option_name_kr}},
                                                    @if ( $in_option->status == BonaeraConstant::WAREHOUSE_STATUS_PENDING )
                                                        상태: <span class="bg-dark rounded text-white px-2 py-1 fs-6">{{ BonaeraConstant::WAREHOUSE_STATUS[$in_option->status] }}</span>,
                                                    @elseif ( $in_option->status == BonaeraConstant::WAREHOUSE_STATUS_RECEIVED )
                                                        상태: <span class="bg-success rounded text-white px-2 py-1 fs-6">{{ BonaeraConstant::WAREHOUSE_STATUS[$in_option->status] }}</span>,
                                                    @elseif ( $in_option->status == BonaeraConstant::WAREHOUSE_STATUS_DISPOSED )
                                                        상태:<span class="bg-danger rounded text-white px-2 py-1 fs-6">{{ BonaeraConstant::WAREHOUSE_STATUS[$in_option->status] }}</span>,
                                                    @endif
                                                    입고: {{ $in_option->quantity }}, 재고: {{ $in_option->lack_status }}
                                                </small>
                                            @else
                                                <small class="d-block mt-1">
                                                    WApp에 옵션이 없음 option_id: {{ $in_option->option_id }}
                                                </small>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if(!empty($data->logistics_last))
                                            <small>
                                                {{ $data->logistics_last->logistics_bill_no }}
                                                <br>
                                                {{ $data->logistics_last->logistics_company_name }}
                                            </small>
                                            <button class="btn btn-sm btn-primary btn-delivery text-white" orderid={{ $data->order_id }}>배송정보조회</button>
                                        @else
                                            배송정보없음
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            {{ $data->created_at }}
                                            <br>
                                            {{ $data->completed_at }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-sm btn-dark btn-update text-white" data-id={{ $data->id }}>입고정보<br>업데이트</button>
                                            <button class="btn btn-sm btn-primary btn-in-detail text-white" data-stockno={{ $data->stock_no }}>입고상세</button>
                                            <button class="btn btn-sm btn-success btn-wapp-detail text-white" orderid={{ $data->order_id }}>주문 상세/수정</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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

            if(confirm(`${ids.length}건의 입고정보를 업데이트 하시겠습니까?`)){
                $("#loadingOverlay").show();
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraInUpdate') }}",
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
            let stockno     = $(this).data("stockno");
            var newTab = window.open(`/wapp/wms/in/${stockno}`, '_blank');
            newTab.focus(); 
        })

        $(".btn-update").click(function(){
            let ids = [$(this).data("id")];

            if(confirm(`입고정보를 업데이트 하시겠습니까?`)){
                $("#loadingOverlay").show();
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraInUpdate') }}",
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
