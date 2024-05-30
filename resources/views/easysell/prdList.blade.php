@php
    use App\Constants\ProductConstant;
    use App\Constants\MallConstant;
    use App\Constants\EasySellConstant;
    use App\Constants\WConstant;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
.cate-table tbody {
  display: block;
  max-height: 350px;
  overflow-y: auto;
}

.cate-table thead,
.cate-table tbody tr {
  display: table;
  width: 100%;
}

.cate-table thead tr td,
.cate-table tbody tr td{
    width: 25%;
}

.cate-table tbody tr{
    cursor: pointer;
}

input[name='easySellCategory']{
    visibility: hidden;
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
                <li class="breadcrumb-item">이지셀</li>
                <li class="breadcrumb-item">상품관리</li>
                <li class="breadcrumb-item active" aria-current="page">상품현황</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="registStatus" value={{ $registStatus }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 현황</th>
                                    <td colspan="3">
                                        <ul class="list-group list-group-horizontal-sm">
                                            <li class="list-group-item text-center small" style="width: 100%;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </li>
                                            <li class="list-group-item text-center small" style="width: 100%;">
                                                등록완료<br>
                                                {{ number_format($successCnt) }}건
                                            </li>
                                            <li class="list-group-item text-center small" style="width: 100%;">
                                                미등록<br>
                                                {{ number_format($failCnt) }}건
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">등록 상태</th>
                                    <td colspan="3">
                                        <button type="button" name="registStatus" class="btn-status btn btn-md {{ $registStatus == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="registStatus" class="btn-status btn btn-md {{ $registStatus == MallConstant::REGISTED ? "btn-primary" : "btn-dark" }}"
                                        value="{{ MallConstant::REGISTED }}">완료</button>
                                        <button type="button" name="registStatus" class="btn-status btn btn-md {{ $registStatus == MallConstant::UNREGIST ? "btn-primary" : "btn-dark" }}"
                                        value="{{ MallConstant::UNREGIST}}">미등록</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="offer_id" @if($search_cls == "offer_id") selected @endif>제품 ID</option>
                                            <option value="itemno" @if($search_cls == "itemno") selected @endif>이지셀 고유번호</option>
                                            <option value="prd_name_kr" @if($search_cls == "prd_name_kr") selected @endif>상품명</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="3">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <a href="/easySell/product/list" class="btn btn-md btn-light btn-reset" role="button">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <div>
                            <select id="selectPageSize" class="form-select" name="pageSize">
                                <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                <option value=30 @if($pageSize == 30) selected @endif>30개 노출</option>
                                <option value=10 @if($pageSize == 10) selected @endif>10개 노출</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-md btn-outline-dark me-2" id="btn-select">상품전송</button>
                    </div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 50px">
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 100px" class="text-center">이미지</th>
                                <th scope="col" style="width: 150px" class="text-center">제품ID</th>
                                <th scope="col">상품명</th>
                                <th scope="col" style="width: 130px">일반 판매가(원)</th>
                                <th scope="col" style="width: 130px">MD 판매가(원)</th>
                                <th scope="col" style="width: 100px" class="text-center">이지셀 전송</th>
                                <th scope="col" style="width: 130px" class="text-center">등록일</th>
                                <th scope="col" style="width: 200px" class="text-center">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                @php
                                    $disabled = "";
                                    if(count($data->options) == 0){
                                        $disabled = "disabled";
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->offer_id }}" {{$disabled}}>
                                    </td>
                                    <td>
                                        {{ number_format(($datas->total() - $offset) - $index) }}
                                    </td>
                                    <td class="text-center">
                                        @if( !empty($data->main_img->img_url_trans) )
                                            <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_trans }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->offer_id }}
                                        @if(empty($data->es_fgn_mapping))
                                            <br>
                                            @if(isset($data->w_mapping))
                                            <button type="button" class="btn btn-danger btn-modal" cateid="{{ $data->w_mapping->mapping_code }}">카테고리 미맵핑</button>
                                            @else
                                            <span class="text-danger">W카테고리 미맵핑 ({{ $data->category_id }})</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $data->prd_name_kr }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                            {{ number_format(calcEasySellSalePrice($option->option_price, $option->md_price, "static")) }}
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                            @if(!empty($option->md_price))
                                            {{ number_format( $option->md_price ) }}
                                            @endif
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($data->regist_success == MallConstant::REGIST_SUCCESS)
                                            {{ $data->itemno }} <br>
                                            <small>({{ EasySellConstant::CATEGORY_NAME[substr($data->es_fgn_mapping->mapping_code,0,6)] }})</small>
                                        @else
                                            <span class="text-danger">미등록</span>
                                        @endisset
                                    </td>
                                    <td class="text-center">
                                        @if($data->regist_success == MallConstant::REGIST_SUCCESS)
                                            <small>{{ $data->registed_at }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-success btn-detail" offerid={{ $data->offer_id }}>상세보기</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-regist" offerid={{ $data->offer_id }} {{ $disabled }}>상품전송</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $datas->links("vendor.pagination.bootstrap-4") }}
            </div>
        </div>

        <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel">매칭 카테고리 : <span class="mapping-cate-nm"></span></h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="cateId" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">카테고리 매칭하기</label>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">키워드</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="cate_keyword" placeholder="검색어를 입력하세요." value="">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col">
                                        <label class="fs-7">카테고리</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="es_cate_first" level="1">
                                            <option value="" hidden>1차 분류</option>
                                            @foreach($esCateFirstList as $cate)
                                            <option value="{{ $cate }}">{{ $cate }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="es_cate_second" level="2">
                                            <option value="" hidden>2차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="es_cate_third" level="3">
                                            <option value="" hidden>3차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="es_cate_fourth" level="4">
                                            <option value="" hidden>4차 분류</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="text-left">
                                <button type="button" class="btn btn-primary btn-cate-search">검색</button>
                            </div>
                            <hr>

                            <table class="table table-white bg-white cate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 200px">1차 카테고리</th>
                                        <th scope="col" style="width: 200px">2차 카테고리</th>
                                        <th scope="col" style="width: 200px">3차 카테고리</th>
                                        <th scope="col" style="width: 200px">4차 카테고리</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save">확인</button>
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();
        });

        $(".btn-detail").click(function(){
            let offer_id = $(this).attr("offerid");
            location.href = `/product/${offer_id}`;
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $("#selectPageSize").change(function(){
            $("#searchFrm").submit();
        });

        $(".btn-regist").click(function(){
            let offer_ids = [$(this).attr("offerid")];

            if(confirm('상품을 전송하시겠습니까?')){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "/api/mall/easySell/product/regist",
                    "data"       : {
                        "offer_ids": offer_ids,
                        "type"     : "{{ WConstant::WAPP_W1 }}"
                    },
                    beforeSend: function () {
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        var alertMessage = "전송 요청이 완료되었습니다.\n이미 전송 된 상품은 수정 반영 됩니다.";
                        if(resp.data.fail.length > 0){
                            var fail = resp.data.fail;
                            var groupedMessages = {};

                            alertMessage += "\n";
                            $.each(fail, function(index, item) {
                                if (!groupedMessages[item.msg]) {
                                    groupedMessages[item.msg] = [];
                                }
                                groupedMessages[item.msg].push(item.offer_id);
                            });

                            $.each(groupedMessages, function(msg, offer_ids) {
                                alertMessage += "\n"+ msg + " [" + offer_ids.join(", ") + "]";
                            });
                        }

                        alert(alertMessage);
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });

                $("#loadingOverlay").show();
            }
        });

        $("#btn-select").click(function(){
            let offer_ids = [];

            $(".chk-inp:checked").each(function(index, element){
                offer_ids.push($(this).val());
            });

            if(offer_ids.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            if(confirm('선택하신 상품을 전송하시겠습니까?')){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "/api/mall/easySell/product/regist",
                    "data"       : {
                        "offer_ids": offer_ids,
                        "type"     : "{{ WConstant::WAPP_W1 }}"
                    },
                    beforeSend: function () {
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        var alertMessage = "전송 요청이 완료되었습니다.\n이미 전송 된 상품은 수정 반영 됩니다.";
                        if(resp.data.fail.length > 0){
                            var fail = resp.data.fail;
                            var groupedMessages = {};

                            alertMessage += "\n";
                            $.each(fail, function(index, item) {
                                if (!groupedMessages[item.msg]) {
                                    groupedMessages[item.msg] = [];
                                }
                                groupedMessages[item.msg].push(item.offer_id);
                            });

                            $.each(groupedMessages, function(msg, offer_ids) {
                                alertMessage += "\n"+ msg + " [" + offer_ids.join(", ") + "]";
                            });
                        }

                        alert(alertMessage);
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });

                $("#loadingOverlay").show();
            }
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $('.select-opt-es').on('change', function() {
            let selectedLevel = parseInt($(this).attr('level'));
            let categoryNm   = $(this).val();

            if( selectedLevel < 4 ){
                var cateFirst = "";
                $('.select-opt-es').each(function(idx) {
                    if(idx == 0){
                        cateFirst = $(this).val();
                    }
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).html(`<option value="" hidden>${level}차 분류</option>`);
                    }
                });

                if( categoryNm != "" ){
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"        : "{{ route('easySell.category.depth') }}",
                        "data"       : {
                            "cateType"  : 'select-opt-es',
                            "cateFirst" : cateFirst,
                            "categoryNm": categoryNm,
                            "level"     : selectedLevel
                        },
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete  : function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $.each(resp.categoryList, function(idx,value) {
                                $(`.select-opt-es[level=${selectedLevel+1}]`).append(`<option value="${value}">${value}</option>`)
                            });
                        },
                        error: function (request) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        });

        $(".btn-modal").click(function(){
            let cateId = $(this).attr("cateid");
            $("#loadingOverlay").show();

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('easySell.category.info') }}",
                "data"    : { "categoryCode" : cateId },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(`.mapping-cate-nm`).text(resp.cateNm);
                    $("input[name='cateId']").val(cateId);
                    $(".cate-table tbody").html("");
                    resp.cateList.map(function(obj){
                        $(`.cate-table tbody`).append(`
                            <tr>
                                <td>
                                    <input type='radio' name='easySellCategory' value='${obj.sellerhub_cate}'>
                                    ${obj.cate_first}
                                </td>
                                <td>
                                    ${obj.cate_second}
                                </td>
                                <td>
                                    ${obj.cate_third}
                                </td>
                                <td>
                                    ${obj.cate_fourth}
                                </td>
                            </tr
                        `);
                    });

                    $("#htmlModal").modal('show');
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $('.cate-table').on('click', 'tr', function() {
            $(this).find('input[type="radio"]').prop('checked', true);

            $('.cate-table tr').removeClass('bg-secondary');
            $(this).addClass("bg-secondary");
        });

        $(".btn-cate-search").click(function(){
            var keyword = $("input[name='cate_keyword']").val();
            var cate_first  = $("select[name='es_cate_first']").val();
            var cate_second = $("select[name='es_cate_second']").val();
            var cate_third  = $("select[name='es_cate_third']").val();
            var cate_fourth = $("select[name='es_cate_fourth']").val();

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('easySell.category.info') }}",
                "data"    : {
                    "keyword"    : keyword,
                    "cate_first" : cate_first,
                    "cate_second": cate_second,
                    "cate_third" : cate_third,
                    "cate_fourth": cate_fourth,
                },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(".cate-table tbody").html("");
                    resp.cateList.map(function(obj){
                        $(`.cate-table tbody`).append(`
                            <tr>
                                <td>
                                    <input type='radio' name='easySellCategory' value='${obj.sellerhub_cate}'>
                                    ${obj.cate_first}
                                </td>
                                <td>
                                    ${obj.cate_second}
                                </td>
                                <td>
                                    ${obj.cate_third}
                                </td>
                                <td>
                                    ${obj.cate_fourth}
                                </td>
                            </tr
                        `);
                    });
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        })

        $(".btn-save").click(function(){
            var selectedCate = $("input[name='easySellCategory']:checked").val();
            var cateId       = $("input[name='cateId']").val();

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('easySell.category.mapping') }}",
                "data"       : {
                    "cateId"      : cateId,
                    "selectedCate": selectedCate,
                },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    alert(resp.msg);
                    if(resp.isSuccess == true){
                        location.reload();
                    }
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        })
    })
</script>

@endsection
