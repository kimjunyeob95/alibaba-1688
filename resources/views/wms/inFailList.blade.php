@php
    use App\Constants\WmsConstant;
    use App\Constants\MallConstant;
    use App\Constants\BonaeraConstant;
    use App\Models\ProductOptionData;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
.modal-table tbody {
    display: block;
    max-height: 350px;
    overflow-y: auto;
}

.modal-table thead,
.modal-table tbody tr {
    display: table;
    width: 100%;
}

.modal-table thead tr td,
.modal-table tbody tr td{
    width: 20%;
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
                <li class="breadcrumb-item">입고관리</li>
                <li class="breadcrumb-item active" aria-current="page">입고 통신 실패 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <div class="card">
                        <div class="card-header">
                            <table class="table">
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
                                            @foreach (WmsConstant::IN_FAIL_SEARCH_TYPE as $key => $search)
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
                        <button class="btn btn-md btn-dark text-white me-2" id="btn-select">입고신청</button>
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
                                <th scope="col" style="width: 10%">
                                    W 주문번호<br>
                                    (채널 주문번호)
                                </th>
                                <th scope="col" style="width: 10%">
                                    구매자<br>
                                    (주문 채널)
                                </th>
                                <th scope="col" style="width: 20%">주문정보</th>
                                <th scope="col" style="width: 10%">HS code</th>
                                <th scope="col" style="width: 10%">배송정보</th>
                                <th scope="col" style="width: 20%">
                                    실패 사유<br>
                                    처리 시간
                                </th>
                                <th scope="col" style="width: 10%">관리</th>
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
                                        <small>
                                            (<a href="https://detail.1688.com/offer/{{ $data->order->offer_id }}.html" target="_blank">{{ $data->order->offer_id }}</a>)
                                            {{ $data->order->product->prd_name_kr }}
                                        </small>
                                        <div class="mt-3"></div>
                                        @foreach ($data->w_options as $w_option)
                                            <small class="d-block mt-1">
                                                @if ($w_option->option->sku_img_url)
                                                    <img class="lazy-img preview-image" data-src="{{ $w_option->option->sku_img_url }}" width=30 height=30/>
                                                @else
                                                    <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                @endif
                                                옵션: {{ $w_option->option->option_name_kr}}
                                            </small>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if( empty($data->hs_code) )
                                            <button class="btn btn-sm btn-success btn-code text-white" data-id={{ $data->id }}>HS code 등록</button>
                                        @else
                                            <span class="btn text-white bg-success">{{ $data->hs_code }}</span>
                                        @endif
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
                                        <small class="text-danger">{{ $data->msg }}</small>
                                        <br>
                                        <small>{{ $data->updated_at }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-sm btn-success btn-wapp-detail text-white" orderid={{ $data->order_id }}>주문 상세</button>
                                            <button class="btn btn-sm btn-dark btn-regist text-white" data-id={{ $data->id }}>입고신청</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="htmlModalLabel">HS code 설정</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="select_ids[]" />
                            <input type="hidden" name="hs_page" value=1 />
                            <input type="hidden" name="hs_last_page" value=1 />
    
                            <div>
                                <div class="d-flex justify-content-evenly px-3">
                                    <div class="row w-100">
                                        <div class="col-2">
                                            <label class="fs-7">검색</label>
                                        </div>
                                        <div class="col-2">
                                            <select class="form-select" name="hs_code_search_cls">
                                                @foreach (WmsConstant::HSCODE_SEARCH_TYPE as $key => $search)
                                                    <option value="{{ $key }}" @if($search_cls == $key) selected @endif>{{ $search }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="hs_code_keyword" placeholder="검색어를 입력하세요." value="">
                                        </div>
                                    </div>
                                </div>
                                <hr>

                                <div class="d-flex justify-content-evenly px-3">
                                    <div class="row w-100">
                                        <div class="col-2">
                                            <label class="fs-7">노출수</label>
                                        </div>
                                        <div class="col-2">
                                            <select class="form-select" name="hs_page_size">
                                                <option value="50">50개 노출</option>
                                                <option value="100">100개 노출</option>
                                                <option value="500">500개 노출</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
    
                                <div class="text-left">
                                    <button type="button" class="btn btn-primary btn-hs-search">검색</button>
                                </div>
                                <hr>
    
                                <table class="table table-white bg-white modal-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" >성질통합 분류코드명</th>
                                            <th scope="col" >HS code</th>
                                            <th scope="col" >한글 품목명</th>
                                            <th scope="col" >영문 품목명</th>
                                            <th scope="col" >관리</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="hs-code modal-footer d-block">
                            <div class="row align-items-center">
                                <div class="col-12 col-sm-8 offset-sm-2 mb-3 mb-sm-0">
                                    <div id="paginationContainer" class="d-flex justify-content-center"></div>
                                </div>
                                <div class="col-12 col-sm-2 text-sm-end">
                                    <button type="button" class="btn btn-dark htmlModalClose">닫기</button>
                                </div>
                            </div>
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

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $('.btn-wapp-detail').click(function(){
            let orderId = $(this).attr("orderid");
            var newTab  = window.open(`/wapp/order/edit/${orderId}`, '_blank');
            newTab.focus(); 
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });
        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $(".btn-code").click(function(){
            let ids = [$(this).data("id")];
            $('input[name="select_ids[]"]').val(ids);

            hsCodeListLoad(1, true);
        })

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

        $(".btn-regist").click(function(){
            let ids = [$(this).data("id")];

            if(confirm(`입고신청 하시겠습니까?`)){
                $("#loadingOverlay").show();
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraInFailCreate') }}",
                    "data"    : { ids: ids },
                    beforeSend: function () {},
                    complete  : function(xhr, status) {
                        $("#loadingOverlay").hide();
                    },
                    success : function (resp) {
                        alert(resp.msg);
                        location.reload();
                    },
                    error: function (request) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $(document).on("click", ".btn-apply", function(){
            let ids    = $('input[name="select_ids[]"]').val().split(",");
            let hsCode = $(this).data("code");

            if(confirm(`해당 HS code로 적용하시겠습니까?`)){
                $("#loadingOverlay").show();
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraInFailHscodeUpdate') }}",
                    "data"    : { ids: ids, hs_code: hsCode},
                    beforeSend: function () {},
                    complete  : function(xhr, status) {
                        $("#loadingOverlay").hide();
                    },
                    success : function (resp) {
                        alert(resp.msg);
                        location.reload();
                    },
                    error: function (request) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $('.btn-hs-search').click(function(){
            hsCodeListLoad(1);
        });

        function hsCodeListLoad(page = 1, modalShow = false) {
            let search_cls = $('select[name="hs_code_search_cls"]').val();
            let keyword    = $('input[name="hs_code_keyword"]').val();
            let page_size  = $('select[name="hs_page_size"]').val();
            
            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/wms/hscode`,
                "data"       : {
                    "begin_page": page,
                    "search_cls": search_cls,
                    "keyword"   : keyword,
                    "page_size" : page_size,
                },
                beforeSend: function () {
                    $('.modal-table tbody').html("");
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    let objs        = resp.result ?? [];
                    let lastPage    = resp.last_page;
                    let currentPage = resp.page;

                    objs?.map(function(ele, key) {
                        $('.modal-table tbody').append(`
                            <tr>
                                <td>${ele.property_code_name}</td>
                                <td>${ele.hs_code}</td>
                                <td>${ele.ko_name}</td>
                                <td>${ele.en_name}</td>
                                <td class="text-center"><button class="btn btn-primary btn-apply text-white" data-code="${ele.hs_code}">적용</button></td>
                            </tr>
                        `);
                    });

                    $('input[name="hs_page"]').val(currentPage);
                    $('input[name="hs_last_page"]').val(lastPage);
                    updatePagination(currentPage, page_size, lastPage);

                    if( modalShow === true ){
                        $("#htmlModal").modal('show');
                    }
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        }

        function createPagination(currentPage, pageSize, lastPage) {
            let paginationHtml = `
                <div class="d-flex justify-content-center align-items-center hs-pagination">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center align-items-center mb-0">
                            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                                <button class="page-link prev-page" ${currentPage === 1 ? 'disabled' : ''}>이전</button>
                            </li>
                            <li class="page-item">
                                <span class="page-link px-3 disabled">${currentPage} / ${lastPage}</span>
                            </li>
                            <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                                <button class="page-link next-page" ${currentPage === lastPage ? 'disabled' : ''}>다음</button>
                            </li>
                        </ul>
                    </nav>
                </div>

            `;

            return paginationHtml;
        }

        function updatePagination(currentPage, pageSize, lastPage) {
            // 기존 페이지네이션 제거
            $('.hs-pagination').remove();

            // 새 페이지네이션 추가
            $('.modal-footer.hs-code').prepend(createPagination(currentPage, pageSize, lastPage));
        }

        // 이벤트 핸들러 추가
        $(document).on('click', '.prev-page', function() {
            let currentPage = Number($('input[name="hs_page"]').val());
            if (currentPage > 1) {
                hsCodeListLoad(currentPage - 1);
            }
        });

        $(document).on('click', '.next-page', function() {
            let currentPage = Number($('input[name="hs_page"]').val());
            let lastPage    = Number($('input[name="hs_last_page"]').val());

            if (currentPage < lastPage) {
                hsCodeListLoad(currentPage + 1);
            }
        });
    });
</script>

@endsection
