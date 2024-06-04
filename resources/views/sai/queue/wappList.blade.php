@php
    use App\Constants\GenuioConstant;
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
                <li class="breadcrumb-item">SAI</li>
                <li class="breadcrumb-item">큐 관리</li>
                <li class="breadcrumb-item active" aria-current="page">WApp 큐 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="send_type" value={{ $send_type }}>
                    <input type="hidden" name="callback_status" value={{ $callback_status }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">큐 현황</th>
                                    <td colspan="3">
                                        <ul class="list-group list-group-horizontal-sm">
                                            <li class="list-group-item text-center small" style="width: 100%;">
                                                요청<br>
                                                {{ number_format($requestCnt) }}건
                                            </li>
                                            <li class="list-group-item text-center small" style="width: 100%;">
                                                응답<br>
                                                {{ number_format($responseCnt) }}건
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">큐 종류</th>
                                    <td colspan="2">
                                        <button type="button" name="send_type" class="btn-status btn btn-sm {{ $send_type == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="send_type" class="btn-status btn btn-sm {{ $send_type == GenuioConstant::IMG_TRANS ? "btn-primary" : "btn-dark" }}"
                                        value="{{ GenuioConstant::IMG_TRANS }}">{{ GenuioConstant::IMG_TRANS_TYPE[GenuioConstant::IMG_TRANS] }}</button>
                                        <button type="button" name="send_type" class="btn-status btn btn-sm {{ $send_type == GenuioConstant::IMG_TRANS_AGAIN ? "btn-primary" : "btn-dark" }}"
                                        value="{{ GenuioConstant::IMG_TRANS_AGAIN }}">{{ GenuioConstant::IMG_TRANS_TYPE[GenuioConstant::IMG_TRANS_AGAIN] }}</button>
                                        <button type="button" name="send_type" class="btn-status btn btn-sm {{ $send_type == GenuioConstant::IMG_Ai_TRANS ? "btn-primary" : "btn-dark" }}"
                                        value="{{ GenuioConstant::IMG_Ai_TRANS }}">{{ GenuioConstant::IMG_TRANS_TYPE[GenuioConstant::IMG_Ai_TRANS] }}</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">큐 통신</th>
                                    <td colspan="2">
                                        <button type="button" name="callback_status" class="btn-status btn btn-sm {{ $callback_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="callback_status" class="btn-status btn btn-sm {{ $callback_status == GenuioConstant::CALLBACK_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ GenuioConstant::CALLBACK_Y }}">{{ GenuioConstant::CALLBACK_TYPE[GenuioConstant::CALLBACK_Y] }}</button>
                                        <button type="button" name="callback_status" class="btn-status btn btn-sm {{ $callback_status == GenuioConstant::CALLBACK_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ GenuioConstant::CALLBACK_N }}">{{ GenuioConstant::CALLBACK_TYPE[GenuioConstant::CALLBACK_N] }}</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="offer_id" @if($search_cls == "offer_id") selected @endif>제품 ID</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="3">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <a href="/onchannel/product/list" class="btn btn-md btn-light btn-reset" role="button">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <div>
                            <select id="selectPageSize" class="form-select" name="pageSize">
                                <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                <option value=300 @if($pageSize == 300) selected @endif>300개 노출</option>
                                <option value=500 @if($pageSize == 500) selected @endif>500개 노출</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-md btn-outline-dark me-2" id="btn-select">큐 삭제</button>
                    </div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 50px">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 100px" class="text-center">제품ID</th>
                                <th scope="col" style="width: 100px" >전송 타입</th>
                                <th scope="col" style="width: 100px" class="text-center">요청 payload</th>
                                <th scope="col" style="width: 100px" class="text-center">응답 response</th>
                                <th scope="col" style="width: 130px" class="text-center">요청일</th>
                                <th scope="col" style="width: 130px" class="text-center">응답일</th>
                                <th scope="col" style="width: 100px" class="text-center">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data->id }}" class="form-check-input chk-inp" type="checkbox" value="{{ $data->id }}">
                                    </td>
                                    <td>
                                        <label for="checkbox-{{ $data->id }}" class="cursor-pointer">
                                            {{ number_format(($datas->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td>
                                        {{ $data->offer_id }}
                                    </td>
                                    <td>
                                        {{ GenuioConstant::IMG_TRANS_TYPE[$data->send_type] }}
                                    </td>
                                    <td>
                                        <small>{!! $data->payload_json !!}</small>
                                    </td>
                                    <td>
                                        @if ($data->child_id)
                                            <small>{!! $data->child_response_json !!}</small>
                                        @else
                                            <label class="text-danger">{{ GenuioConstant::CALLBACK_TYPE[GenuioConstant::CALLBACK_N] }}</label>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <small>{!! $data->registed_at !!}</small>
                                    </td>
                                    <td class="text-center">
                                        @if ($data->child_id)
                                            <small>{!! $data->child_created_at !!}</small>
                                        @else
                                            <label class="text-danger">{{ GenuioConstant::CALLBACK_TYPE[GenuioConstant::CALLBACK_N] }}</label>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-danger btn-remove" queueid={{ $data->id }}>큐 삭제</button>
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

    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $(".btn-cate-search").click(function(){
            var keyword     = $("input[name='cate_keyword']").val();
            var cate_first  = $("select[name='channel_cate_first']").val();
            var cate_second = $("select[name='channel_cate_second']").val();
            var cate_third  = $("select[name='channel_cate_third']").val();
            var cate_fourth = $("select[name='channel_cate_fourth']").val();

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "/api/mall/onchannel/category/list",
                "data"   : {
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
                    resp.data.map(function(obj){
                        $(`.cate-table tbody`).append(`
                            <tr>
                                <td>
                                    <input type='radio' name='channelCategory' value='${obj.codenum}'>
                                    ${obj.fir_cate}
                                </td>
                                <td>
                                    ${obj.se_cate}
                                </td>
                                <td>
                                    ${obj.th_cate}
                                </td>
                                <td>
                                    ${obj.last_cate}
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
            var categoryId      = $("input[name='cateId']").val();
            var channelCateCode = $("input[name='channelCategory']:checked").val();

            if( !channelCateCode ){
                return alert("채널 카테고리를 선택하세요.");
            }

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "/api/mall/onchannel/category/mapping",
                "data"   : {
                    "categoryId"     : categoryId,
                    "channelCateCode": channelCateCode,
                },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
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
        })

        $('.select-opt').change(function(){
            let selectedLevel = parseInt($(this).attr('level'));
            let category_id   = $(this).val();

            if( selectedLevel < 3 ){
                $('.select-opt').each(function() {
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).html(`<option value="">${level}차 분류</option>`);
                    }
                });

                if( category_id != "" ){
                    $("#loadingOverlay").show();
                    category_id = parseInt($(this).val());
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type" : "GET",
                        "url" : `/api/w/category/depth/${category_id}`,
                        beforeSend : function () {},
                        complete: function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $(`.select-opt[level=${selectedLevel+1}]`).html(`<option value="">${selectedLevel+1}차 분류</option>`);
                            resp.data.map(function(obj){
                                $(`.select-opt[level=${selectedLevel+1}]`).append(`<option value="${obj.category_id}">${obj.category_name}</option>`)
                            })
                        },
                        error: function (request) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        });

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });
        
        $(".btn-detail").click(function(){
            let offer_id = $(this).attr("offerid");
            location.href = `/product/${offer_id}`;
        });

        $(".btn-en-detail").click(function(){
            let offer_id = $(this).attr("offerid");
            location.href = `/product/en/${offer_id}`;
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
                    "url"        : "/api/mall/onchannel/product/regist",
                    "data"       : {
                        "offer_ids"   : offer_ids,
                        "sendTypeList": sendTypeList
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
                    "url"        : "/api/mall/onchannel/product/regist",
                    "data"       : {
                        "offer_ids"   : offer_ids,
                        "sendTypeList": sendTypeList
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

    })
</script>

@endsection
