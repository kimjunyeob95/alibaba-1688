@php
    use App\Constants\WmsConstant;
    use App\Constants\BonaeraConstant;
    use App\Constants\OrderConstant;
    use App\Constants\MallConstant;
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
                <li class="breadcrumb-item">출고 관리</li>
                <li class="breadcrumb-item active" aria-current="page">출고 신청 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="status" value={{ $status }}>
                    <input type="hidden" name="in_status" value={{ $inStatus }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">출고신청</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap m-n1">
                                            <button type="button" name="status" class="btn-status btn btn-sm m-1 {{ $status == '' ? 'btn-primary' : 'btn-dark' }}"
                                                value="">전체</button>
                                            @foreach (WmsConstant::OUT_SIGN_STATUS as $key => $value)    
                                                <button type="button" name="status" class="btn-status btn btn-sm m-1 {{ $status == $key ? 'btn-primary' : 'btn-dark' }}"
                                                    value="{{ $key }}">{{ $value }}</button>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">입고 상태</th>
                                    <td colspan="2">
                                        <button type="button" name="in_status" class="btn-status btn btn-sm {{ $inStatus == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        @foreach (BonaeraConstant::WAREHOUSE_STATUS as $key => $value)    
                                            <button type="button" name="in_status" class="btn-status btn btn-sm {{ $inStatus == $key ? "btn-primary" : "btn-dark" }}"
                                            value="{{ $key }}">{{ $value }}</button>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">기간</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="time_cls">
                                            <option value="create" @if($timeCls == "create") selected @endif>주문 생성일</option>
                                            <option value="modi" @if($timeCls == "modi") selected @endif>주문 수정일</option>
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
                                            @foreach (WmsConstant::OUT_SIGN_SEARCH_TYPE as $key => $search)
                                                <option value="{{ $key }}" @if($search_cls == $key) selected @endif>{{ $search }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="검색어를 입력해주세요. 다중 검색 시 ,(콤마)로 구분 지어주세요.">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정렬</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="sort">
                                            <option value="created_at|desc" @if($sort == "created_at|desc") selected @endif>주문 생성일 내림차순</option>
                                            <option value="created_at|asc" @if($sort == "created_at|asc") selected @endif>주문 생성일 오름차순</option>
                                            <option value="updated_at|desc" @if($sort == "updated_at|desc") selected @endif>주문 수정일 내림차순</option>
                                            <option value="updated_at|asc" @if($sort == "updated_at|asc") selected @endif>주문 수정일 오름차순</option>
                                        </select>
                                    </td>
                                    <td colspan="">
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
                        <button class="btn btn-md btn-dark text-white me-2" id="btn-select">출고 신청</button>
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
                                    W 주문번호<br>
                                    채널 주문번호
                                </th>
                                <th scope="col" style="width: 8%">
                                    구매자<br>
                                    (주문 채널)
                                </th>
                                <th scope="col" style="width: *%">주문정보</th>
                                <th scope="col" style="width: 8%">
                                    입고번호<br>
                                    출고번호<br>
                                    배송번호
                                </th>
                                <th scope="col" style="width: 5%">입고상태</th>
                                <th scope="col" style="width: 15%">출고신청</th>
                                <th scope="col" style="width: 8%">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator->items() as $index => $data)
                                @php
                                    $inStatus = BonaeraConstant::WAREHOUSE_STATUS_PENDING;
                                    if( isset($data->boneara_in_base->in_options) && !empty($data->boneara_in_base->in_options) ){
                                        $allReceived = true;
                                        foreach ($data->boneara_in_base->in_options as $inOpt) {
                                            if ($inOpt->status !== BonaeraConstant::WAREHOUSE_STATUS_RECEIVED) {
                                                $allReceived = false;
                                                break;
                                            }
                                        }
                                        $inStatus = $allReceived ? BonaeraConstant::WAREHOUSE_STATUS_RECEIVED : BonaeraConstant::WAREHOUSE_STATUS_PENDING;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data->id }}" class="form-check-input chk-inp" 
                                        type="checkbox" value="{{ $data->id }}">
                                    </td>
                                    <td>
                                        <small>{{ number_format(($paginator->total() - $offset) - $index) }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->order_id }}</small>
                                        <br>
                                        <small>{{ $data->channel_order_id }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->buyer_name }}</small>
                                        <br>
                                        <small>({{ MallConstant::MALL_NAME[$data->channel] }})</small>
                                    </td>
                                    <td>
                                        <small>
                                            (<a href="https://detail.1688.com/offer/{{ $data->order->offer_id }}.html" target="_blank">{{ $data->order->offer_id }}</a>)
                                            {{ $data->order->product->prd_name_kr }}
                                        </small>
                                        <div class="mt-3"></div>
                                        @foreach ($data->details as $opt)
                                            @php
                                                $w_option  = $opt->option;
                                                $wInStatus = "";
                                                if( isset($data->boneara_in_base->in_options) && !empty($data->boneara_in_base->in_options) ){
                                                    foreach ($data->boneara_in_base->in_options as $inOpt) {
                                                        if( $inOpt->option_id === $w_option->id ){
                                                            $wInStatus = $inOpt->status;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            @if (!empty($w_option))
                                                <small class="d-block mt-1">
                                                    @if ($w_option->sku_img_url)
                                                        <img class="lazy-img preview-image" data-src="{{ $w_option->sku_img_url }}" width=30 height=30/>
                                                    @else
                                                        <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                    @endif
                                                    옵션: {{ $w_option->option_name_kr}},
                                                    수량: {{ number_format($opt->quantity) }}
                                                    @if (!empty($wInStatus))
                                                        @if ($wInStatus === BonaeraConstant::WAREHOUSE_STATUS_RECEIVED )
                                                            <span class="bg-success rounded text-white px-1 py-0 fs-7 small">{{ BonaeraConstant::WAREHOUSE_STATUS[$wInStatus] }}</span>
                                                        @elseif ( $wInStatus == BonaeraConstant::WAREHOUSE_STATUS_PENDING )
                                                            <span class="bg-dark rounded text-white px-1 py-0 fs-7 small">{{ BonaeraConstant::WAREHOUSE_STATUS[$wInStatus] }}</span>
                                                        @else
                                                            <span class="bg-danger rounded text-white px-1 py-0 fs-7 small">{{ BonaeraConstant::WAREHOUSE_STATUS[$wInStatus] }}</span>
                                                        @endif
                                                    @endif
                                                </small>
                                            @else
                                                <small class="d-block mt-1">
                                                    WApp에 옵션이 없음 option_id: {{ $opt->option_id }}
                                                </small>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <small>
                                            @if (!empty($data->boneara_in_base))
                                                {{ $data->boneara_in_base->stock_no }}
                                            @else
                                                입고X
                                            @endif
                                        </small>
                                        <br>
                                        <small>
                                            @if ( !empty($data->sh_no) )
                                                {{ $data->sh_no }}
                                            @else
                                                출고번호X
                                            @endif
                                        </small>
                                        <br>
                                        <small>
                                            @if ( !empty($data->group_no) )
                                                {{ $data->group_no }}
                                            @else
                                                배송번호X
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        {{ BonaeraConstant::WAREHOUSE_STATUS[$inStatus]}}
                                    </td>
                                    <td>
                                        @if (!empty($data->sh_no))
                                            <small>완료</small>
                                            <br>
                                            <small>
                                                {{ $data->out_created_at }}
                                            </small>
                                        @elseif(empty($data->sh_no) && !empty($data->msg))
                                            <small class="text-danger">실패사유: {{ $data->msg }}</small>
                                            <br>
                                            <small>
                                                {{ $data->fail_updated_at }}
                                            </small>
                                        @elseif(empty($data->sh_no) && empty($data->msg))
                                            <small>대기</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-sm btn-success btn-wapp-detail text-white" orderid={{ $data->order_id }}>주문 상세</button>
                                            @if ($data->boneara_in_base && isset($data->boneara_in_base->stock_no))
                                                <button class="btn btn-sm btn-primary btn-in-detail text-white" data-stockno={{ $data->boneara_in_base->stock_no }}>입고상세</button>
                                            @endif
                                            @if (!empty($data->sh_no))
                                                <button class="btn btn-sm btn-outline-primary btn-out-detail" data-groupno={{ $data->group_no }}>출고상세</button>
                                            @endif
                                            @if (empty($data->sh_no) && !empty($data->boneara_in_base))
                                                <button class="btn btn-sm btn-dark btn-out text-white" data-id={{ $data->id }}>출고신청</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
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

        $(".btn-in-detail").click(function(){
            let stockno = $(this).data("stockno");
            var newTab  = window.open(`/wapp/wms/in/${stockno}`, '_blank');
            newTab.focus(); 
        })

        $('.btn-wapp-detail').click(function(){
            let orderId = $(this).attr("orderid");
            var newTab  = window.open(`/wapp/order/edit/${orderId}`, '_blank');
            newTab.focus(); 
        });

        $(".btn-out-detail").click(function(){
            let groupno = $(this).data("groupno");
            var newTab  = window.open(`/wapp/wms/out/${groupno}`, '_blank');
            newTab.focus(); 
        })

        $("#btn-select").click(function(){
            let ids = [];

            $(".chk-inp:checked").each(function(index, element){
                ids.push($(this).val());
            });

            if(ids.length < 1){
                return alert("선택 된 정보가 없습니다.");
            }

            if(confirm(`${ids.length}건을 출고신청 하시겠습니까?`)){
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraOutCreate') }}",
                    "data"    : { ids: ids },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
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

        $(".btn-out").click(function(){
            let ids = [$(this).data("id")];

            if(confirm(`출고신청 하시겠습니까?`)){
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraOutCreate') }}",
                    "data"    : { ids: ids },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
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
