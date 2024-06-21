@php
    use App\Constants\CategoryConstant;
    use App\Constants\ProductConstant;
    use App\Constants\MallConstant;
    use App\Constants\EasySellConstant;
    use App\Constants\InspectConstant;
    use App\Models\ProductWeightData;
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

input[name='channelCategory']{
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
                <li class="breadcrumb-item">채널 관리</li>
                <li class="breadcrumb-item">상품 전송 현황</li>
                <li class="breadcrumb-item active" aria-current="page">이지셀:{{ $send_type }} 상품현황</li>
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
                                                전송실패<br>
                                                {{ number_format($failCnt) }}건
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">등록 상태</th>
                                    <td colspan="3">
                                        <button type="button" name="registStatus" class="btn-status btn btn-sm {{ $registStatus == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="registStatus" class="btn-status btn btn-sm {{ $registStatus == MallConstant::REGISTED ? "btn-primary" : "btn-dark" }}"
                                        value="{{ MallConstant::REGISTED }}">완료</button>
                                        <button type="button" name="registStatus" class="btn-status btn btn-sm {{ $registStatus == MallConstant::UNREGIST ? "btn-primary" : "btn-dark" }}"
                                        value="{{ MallConstant::UNREGIST}}">미등록</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">W 카테고리</th>
                                    <td colspan="2">
                                        <div class="row">
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_first" level=1>
                                                    <option value="">1차 분류</option>
                                                    @foreach ($firstCateObjs as $firstCateObj)
                                                        <option value="{{ $firstCateObj->category_id }}" {{ $firstCateObj->category_id == $cate_first ? "selected" : "" }}>{{ $firstCateObj->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_second" level=2>
                                                    <option value="">2차 분류</option>
                                                    @foreach ($secondCateObjs as $secondCateObj)
                                                        <option value="{{ $secondCateObj->category_id }}" {{ $secondCateObj->category_id == $cate_second ? "selected" : "" }}>{{ $secondCateObj->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_third" level=3>
                                                    <option value="">3차 분류</option>
                                                    @foreach ($thirdCateObjs as $thirdCateObj)
                                                        <option value="{{ $thirdCateObj->category_id }}" {{ $thirdCateObj->category_id == $cate_third ? "selected" : "" }}>{{ $thirdCateObj->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 100px" class="text-center">이미지</th>
                                <th scope="col" style="width: 100px" class="text-center">제품ID</th>
                                <th scope="col" style="width: 200px" >상품명</th>
                                <th scope="col" style="width: 100px" class="text-center">상품상태</th>
                                <th scope="col" style="width: 150px" class="text-center">
                                    W 공급가(원)
                                </th>
                                <th scope="col" style="width: 120px" class="text-center">
                                    기준: 중량 (kg)<br>
                                    배송비 (원)
                                </th>
                                <th scope="col" style="width: 100px" class="text-center">
                                    더블유<br>
                                    판매가
                                </th>
                                <th scope="col" style="width: 150px" class="text-center">전송 카테고리</th>
                                <th scope="col" style="width: 100px" class="text-center">상품 맵핑 코드</th>
                                <th scope="col" style="width: 100px" class="text-center">상품 전송</th>
                                <th scope="col" style="width: 130px" class="text-center">전송일자</th>
                                <th scope="col" style="width: 100px" class="text-center">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                @php
                                    $disabled = "";
                                    if(count($data->options) == 0){
                                        $disabled = "disabled";
                                    }

                                    $wCateName = "";
                                    if( $data->w_mapping != null && $data->w_mapping->w_cate_name != null ){
                                        $wCateObj = $data->w_mapping->w_cate_name;
                                        if(!empty($wCateObj->cate_first)){
                                            $wCateName .= $wCateObj->cate_first;
                                        }
                                        if(!empty($wCateObj->cate_second)){
                                            $wCateName .= " > " . $wCateObj->cate_second;
                                        }
                                        if(!empty($wCateObj->cate_third)){
                                            $wCateName .= " > " . $wCateObj->cate_third;
                                        }
                                        if(!empty($wCateObj->cate_fourth)){
                                            $wCateName .= " > " . $wCateObj->cate_fourth;
                                        }
                                    }

                                    $cateName = "";
                                    if( $data->es_category != null ){
                                        $cateObj = $data->es_category;
                                        if(!empty($cateObj->cate_first)){
                                            $cateName .= $cateObj->cate_first;
                                        }
                                        if(!empty($cateObj->cate_second)){
                                            $cateName .= " > " . $cateObj->cate_second;
                                        }
                                        if(!empty($cateObj->cate_third)){
                                            $cateName .= " > " . $cateObj->cate_third;
                                        }
                                        if(!empty($cateObj->cate_fourth)){
                                            $cateName .= " > " . $cateObj->cate_fourth;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data->offer_id }}" class="form-check-input chk-inp" type="checkbox" value="{{ $data->offer_id }}" {{$disabled}}>
                                    </td>
                                    <td>
                                        <label for="checkbox-{{ $data->offer_id }}" class="cursor-pointer">
                                            {{ number_format(($datas->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            if($send_type == EasySellConstant::TYPE_W){
                                                $mainImg = $data->main_img->img_url_trans;
                                            }else if($send_type == EasySellConstant::TYPE_DROPHUB){
                                                $mainImg = $data->en_main_img->img_url_origin;
                                            }
                                        @endphp
                                        @if( !empty($mainImg) )
                                            <img class="lazy-img preview-image" data-src="{{ $mainImg }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->offer_id }}
                                    </td>
                                    <td>
                                        <small>{{ $data->prd_name_kr }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if ($data->trans_status == ProductConstant::IMG_TRANS_Y)
                                            <button class="btn btn-xs btn-dark text-white btn-trans-img" offerid={{ $data->offer_id }}>번역 {{ ProductConstant::IMG_TRANS_STATUS[$data->trans_status] }}</button>
                                        @else
                                            <button class="btn btn-xs btn-danger text-white btn-trans-img" offerid={{ $data->offer_id }}>번역 {{ ProductConstant::IMG_TRANS_STATUS[$data->trans_status] }}</button>
                                        @endif
                                        @if ($data->status == ProductConstant::PRD_STATUS_PUBLISH)
                                            <button class="btn btn-xs btn-dark text-white btn-prd-status mt-1" offerid={{ $data->offer_id }} prdstatus={{ $data->status }}>{{ ProductConstant::PRD_STATUS[$data->status] }}</button>
                                        @else
                                            <button class="btn btn-xs btn-danger text-white btn-prd-status mt-1" offerid={{ $data->offer_id }} prdstatus={{ $data->status }}>{{ ProductConstant::PRD_STATUS[$data->status] }}</button>
                                        @endif
                                        @php
                                            $img_inspect  = "Y";
                                            $prd_inspect  = "Y";
                                            $gosi_inspect = "Y";
                                            if($data->img_inspect == null || $data->img_inspect->is_inspect == InspectConstant::IS_INSPECT_N){
                                                $img_inspect = "N";
                                            }
                                            if($data->prd_inspect == null || $data->prd_inspect->is_inspect == InspectConstant::IS_INSPECT_N){
                                                $prd_inspect = "N";
                                            }
                                            if($data->gosi_inspect == null || $data->gosi_inspect->is_inspect == InspectConstant::IS_INSPECT_N){
                                                $gosi_inspect = "N";
                                            }
                                        @endphp
                                        @if ($data->inspect_status == InspectConstant::IS_INSPECT_Y)
                                            <button class="btn btn-xs btn-dark text-white btn-inspect-status mt-1" offerid={{ $data->offer_id }} img_inspect={{ $img_inspect }} prd_inspect={{ $prd_inspect }} gosi_inspect={{ $gosi_inspect }}>검수 완료</button>
                                        @else
                                            <button class="btn btn-xs btn-danger text-white btn-inspect-status mt-1" offerid={{ $data->offer_id }} img_inspect={{ $img_inspect }} prd_inspect={{ $prd_inspect }} gosi_inspect={{ $gosi_inspect }}>검수 미완료</button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];

                                                //배송비 설정
                                                $weights   = CategoryConstant::WEIGHTS;
                                                $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                                                if( $data->weight != null ){
                                                    $delivery_price = $weights[$data->weight];
                                                }

                                                if($send_type == EasySellConstant::TYPE_W){
                                                    $price = calcEasySellSalePrice($option->price_1688, $option->md_price, $delivery_price, "static", EasySellConstant::TYPE_W);
                                                }else if($send_type == EasySellConstant::TYPE_DROPHUB){
                                                    $price = calcEasySellSalePrice($option->price_1688_option, $option->md_price, $delivery_price, "static", EasySellConstant::TYPE_DROPHUB);
                                                }
                                            @endphp
                                            @if(!empty($option->price_1688))
                                            {{ number_format( $price['option_price'] ) }}
                                            @endif
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if( $data->weight_type == null )
                                            <button class="btn btn-sm btn-warning btn-weight-modi" offerid={{ $data->offer_id }} weight=0 price={{ ProductConstant::WEIGHT_STATUS_NONE_PRICE }} statusname='{{ ProductConstant::WEIGHT_STATUS[ProductConstant::WEIGHT_STATUS_NONE] }}'>
                                                {{ ProductConstant::WEIGHT_STATUS_SHORT[ProductConstant::WEIGHT_STATUS_NONE] }}: 0
                                            </button>
                                            <br>
                                            <span class="text-danger">
                                                {{ number_format(ProductConstant::WEIGHT_STATUS_NONE_PRICE) }}
                                            </span>
                                        @else
                                            <button class="btn btn-sm btn-warning btn-weight-modi" offerid={{ $data->offer_id }} weight={{ $data->weight }} price={{ $data->delivery_price }} statusname='{{ ProductConstant::WEIGHT_STATUS[$data->weight_type] }}'>
                                                {{ ProductConstant::WEIGHT_STATUS_SHORT[$data->weight_type] }}: {{ $data->weight }}
                                            </button>
                                            <br>
                                            <span class="text-danger">
                                                {{ number_format($data->delivery_price) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            {{ number_format($price['salePrice']) }}
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!empty($cateName))
                                            <small>
                                                {{$cateName}}
                                            </small>
                                        @else
                                            @if(isset($data->w_mapping))
                                                <button type="button" class="btn btn-sm btn-danger text-white btn-modal" cateid="{{ $data->category_id }}" catename="{{ $wCateName }}">카테고리 미맵핑</button>
                                            @else
                                                <span class="text-danger">W카테고리 미맵핑 ({{ $data->category_id }})</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($data->regist_success == MallConstant::REGIST_SUCCESS)
                                            {{ $data->itemno }} <br>
                                            <small>({{ EasySellConstant::CATEGORY_NAME[substr($data->es_fgn_mapping,0,6)] }})</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($data->regist_success == MallConstant::REGIST_SUCCESS)
                                            성공
                                        @else
                                            <small><span class="text-danger">전송실패 사유: ({{ $data->regist_message }})</span></small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(isset($data->easysell->detail_log))
                                            @php
                                                $detailLog = $data->easysell->detail_log
                                                    ->where("is_success",MallConstant::REGIST_SUCCESS)
                                                    ->sortByDesc('created_at')
                                                    ->first();
                                            @endphp
                                            @if($detailLog)
                                                <small>{{ $detailLog->created_at }}</small>
                                            @endif
                                            <button class="btn btn-sm btn-success text-white btn-log-modal" logid="{{ $data->log_id }}">전송로그</button>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-detail" offerid={{ $data->offer_id }}>국문 상세</button>
                                        @if ($data->prd_name_en)
                                            <button class="btn btn-sm btn-outline-success btn-en-detail mt-1" offerid={{ $data->offer_id }}>영문 상세</button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-regist mt-1" offerid={{ $data->offer_id }} {{ $disabled }}>상품전송</button>
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
                        <h5 class="modal-title" id="htmlModalLabel">WApp 맵핑 카테고리 : <span class="mapping-cate-nm"></span></h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="cateId" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">이지셀 카테고리 맵핑하기</label>
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
                                        <label class="fs-7">이지셀 카테고리</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_first" level="1">
                                            <option value="">1차 분류</option>
                                            @foreach($channelCateFirstList as $cate)
                                                <option value="{{ $cate }}">{{ $cate }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_second" level="2">
                                            <option value="">2차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_third" level="3">
                                            <option value="">3차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_fourth" level="4">
                                            <option value="">4차 분류</option>
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

        <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel2">상품 전송 로그</h5>
                    </div>
                    <div class="modal-body">
                        <table class="table table-white bg-white log-table">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 60px">No.</th>
                                    <th scope="col" style="width: 200px">전송타입</th>
                                    <th scope="col" style="width: 200px">성공여부</th>
                                    <th scope="col" style="width: 200px">에러내용</th>
                                    <th scope="col" style="width: 200px">전송일시</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary htmlModalClose2">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        var channelCateList = '{!! json_encode($channelCateList) !!}';
        channelCateList = JSON.parse(channelCateList);

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
                    "url"        : "/api/mall/easySell/product/regist",
                    "data"       : {
                        "offer_ids": offer_ids,
                        "type"     : ["{{ $send_type }}"]
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
                        "type"     : ["{{ $send_type }}"]
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

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $('.select-opt-channel').on('change', function() {
            let selectedLevel = parseInt($(this).attr('level'));

            if( selectedLevel < 4 ){
                let cate_name = "";
                $('.select-opt-channel').each(function(key, ele) {
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).html(`<option value="">${level}차 분류</option>`);
                    }
                    if (selectedLevel >= level) {
                        if( key == 0 ){
                            cate_name = $(this).val();
                        }else{
                            cate_name += "," + $(this).val();
                        }
                    }
                });

                if( cate_name != "" ){
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"     : "/api/mall/easySell/category/depth",
                        "data"    : {
                            level     : selectedLevel,
                            cate_name : cate_name,
                        },
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete  : function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $(`.select-opt-channel[level=${selectedLevel+1}]`).html(`<option value="">${selectedLevel+1}차 분류</option>`);
                            if( selectedLevel == 1 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_second ){
                                        $(`.select-opt-channel[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_second}">${obj.cate_second}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 2 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_third ){
                                        $(`.select-opt-channel[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_third}">${obj.cate_third}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 3 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_fourth ){
                                        $(`.select-opt-channel[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_fourth}">${obj.cate_fourth}</option>`)
                                    }
                                })
                            }
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
            let cateId   = $(this).attr("cateid");
            let cateName = $(this).attr("catename");

            $("#htmlModal").find("select").val("");
            $("input[name='cate_keyword']").val("");
            $(".cate-table tbody").html("");

            $("input[name='cateId']").val(cateId);
            $(`.mapping-cate-nm`).text(cateName);

            channelCateList.map(function(obj){
                $(`.cate-table tbody`).append(`
                    <tr>
                        <td>
                            <input type='radio' name='channelCategory' value='${obj.sellerhub_cate}'>
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
                    </tr>
                `);
            });

            $("#htmlModal").modal('show');
        });

        $('.cate-table').on('click', 'tr', function() {
            $(this).find('input[type="radio"]').prop('checked', true);

            $('.cate-table tr').removeClass('bg-secondary');
            $(this).addClass("bg-secondary");
        });

        $(".btn-cate-search").click(function(){
            var keyword     = $("input[name='cate_keyword']").val();
            var cate_first  = $("select[name='channel_cate_first']").val();
            var cate_second = $("select[name='channel_cate_second']").val();
            var cate_third  = $("select[name='channel_cate_third']").val();
            var cate_fourth = $("select[name='channel_cate_fourth']").val();

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "/api/mall/easySell/category/list",
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
                                    <input type='radio' name='channelCategory' value='${obj.sellerhub_cate}'>
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
                            </tr>
                        `);
                    });
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $(".btn-save").click(function(){
            var categoryId      = $("input[name='cateId']").val();
            var channelCateCode = $("input[name='channelCategory']:checked").val();

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "/api/mall/easySell/category/mapping",
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

        $('.btn-log-modal').click(function(){
            let logId = $(this).attr("logid");

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "GET",
                "url"    : "/api/mall/easySell/product/log/" + logId,
                "data"   : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $('.log-table tbody').html("");
                    resp.data.map(function(obj, key){
                        $('.log-table tbody').append(`<tr>
                            <td>${key+1}</td>
                            <td>${obj.sendType}</td>
                            <td>${obj.isSuccess}</td>
                            <td>${obj.message}</td>
                            <td>${obj.created_at}</td>
                        </tr>`);
                    });

                    $("#htmlModal2").modal('show');

                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });

        });
    })
</script>

@endsection
