@php
    use App\Constants\ProductConstant;
    use App\Constants\MallConstant;
    use App\Constants\WConstant;
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
                <li class="breadcrumb-item">온채널</li>
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
                                            <option value="prd_code" @if($search_cls == "prd_code") selected @endif>온채널 고유번호</option>
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
                                <option value=30 @if($pageSize == 30) selected @endif>30개 노출</option>
                                <option value=10 @if($pageSize == 10) selected @endif>10개 노출</option>
                            </select>
                        </div>
                        {{-- <button type="button" class="btn btn-md btn-outline-dark me-2" id="btn-select">상품전송</button> --}}
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
                                <th scope="col" style="width: 130px">W 공급가(원)</th>
                                <th scope="col" style="width: 130px">온채널 공급가(원)</th>
                                <th scope="col" style="width: 100px" class="text-center">
                                    온채널 코드<br>
                                    맵핑 코드
                                </th>
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
                                        @if( !empty($data->main_img->img_url_origin) )
                                            <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_origin }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->offer_id }}
                                        @if( $data->mapping_status != ProductConstant::MAPPING_STATUS_Y )
                                            <br><span class="text-danger">*카테고리 미맵핑 ({{ $data->category_id }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->prd_name_kr }}
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                            {{ number_format($option->option_price) }}
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                            {{ number_format(calcOnchannelSalePrice($option->option_price)) }}
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($data->regist_success == MallConstant::REGIST_SUCCESS)
                                            {{ $data->prd_code }} <br>
                                            @if ($data->w_mapping)
                                                <small>({{ $data->w_mapping->mapping_code }})</small>
                                            @endif
                                        @elseif($data->regist_success == MallConstant::REGIST_FAIL)
                                            <span class="text-danger">전송실패 사유: ({{ $data->message }})</span>
                                        @else
                                            <span class="text-danger">미등록</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-success btn-detail" offerid={{ $data->offer_id }}>상세보기</button>
                                        {{-- <button type="button" class="btn btn-sm btn-outline-primary btn-regist" offerid={{ $data->offer_id }} {{ $disabled }}>상품전송</button> --}}
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

    })
</script>

@endsection
