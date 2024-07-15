@php
    use App\Constants\ProductConstant;
    use App\Constants\WConstant;
    use App\Constants\InspectConstant;
    use App\Constants\CategoryConstant;
    use App\Constants\EasySellConstant;
    use App\Constants\MallConstant;
    use App\Constants\OnchannelConstant;

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
                <li class="breadcrumb-item">상품 관리</li>
                <li class="breadcrumb-item active" aria-current="page">전체 상품</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="w_type" value={{ $w_type }}>
                    <input type="hidden" name="collect_status" value={{ $collect_status }}>
                    <input type="hidden" name="trans_status" value={{ $trans_status }}>
                    <input type="hidden" name="mapping_status" value={{ $mapping_status }}>
                    <input type="hidden" name="prd_status" value={{ $prd_status }}>
                    <input type="hidden" name="mdPrice_status" value={{ $mdPrice_status }}>
                    <input type="hidden" name="inspect_status" value={{ $inspect_status }}>
                    <input type="hidden" name="inspect_img_status" value={{ $inspect_img_status }}>
                    <input type="hidden" name="inspect_prd_status" value={{ $inspect_prd_status }}>
                    <input type="hidden" name="inspect_gosi_status" value={{ $inspect_gosi_status }}>
                    <input type="hidden" name="weight_status" value={{ $weight_status }}>
                    <input type="hidden" name="cate_first" value={{ $cate_first }}>
                    <input type="hidden" name="cate_second" value={{ $cate_second }}>
                    <input type="hidden" name="cate_third" value={{ $cate_third }}>
                    <input type="hidden" name="no_send_channel" value='{{ $no_send_channel }}'>
                    <input type="hidden" name="quantity_count" value='{{ $quantity_count }}'>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">수집 정보</th>
                                    <td colspan="2">
                                        <button type="button" name="collect_status" class="btn-status btn btn-sm {{ $collect_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="collect_status" class="btn-status btn btn-sm {{ $collect_status == ProductConstant::COLLECT_KR ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::COLLECT_KR }}">국문</button>
                                        <button type="button" name="collect_status" class="btn-status btn btn-sm {{ $collect_status == ProductConstant::COLLECT_EN ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::COLLECT_EN }}">영문</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 번역</th>
                                    <td>
                                        <button type="button" name="trans_status" class="btn-status btn btn-sm {{ $trans_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="trans_status" class="btn-status btn btn-sm {{ $trans_status == ProductConstant::TRANS_STATUS_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::TRANS_STATUS_Y }}">완료</button>
                                        <button type="button" name="trans_status" class="btn-status btn btn-sm {{ $trans_status == ProductConstant::TRANS_STATUS_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::TRANS_STATUS_N }}">미완료</button>
                                    </td>
                                    <td>
                                        <div class="d-flex text-center">
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                번역완료<br>
                                                {{ number_format($transYCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                번역 미완료<br>
                                                {{ number_format($transNCnt) }}건
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">검수 상태</th>
                                    <td>
                                        <button type="button" name="inspect_status" class="btn-status btn btn-sm {{ $inspect_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="inspect_status" class="btn-status btn btn-sm {{ $inspect_status == InspectConstant::IS_INSPECT_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_Y }}">완료</button>
                                        <button type="button" name="inspect_status" class="btn-status btn btn-sm {{ $inspect_status == InspectConstant::IS_INSPECT_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_N }}">미완료</button>
                                    </td>
                                    <td>
                                        <div class="d-flex text-center">
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                완료<br>
                                                {{ number_format($inspectYCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                미완료<br>
                                                {{ number_format($inspectNCnt) }}건
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">이미지 검수</th>
                                    <td>
                                        <button type="button" name="inspect_img_status" class="btn-status btn btn-sm {{ $inspect_img_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="inspect_img_status" class="btn-status btn btn-sm {{ $inspect_img_status == InspectConstant::IS_INSPECT_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_Y }}">완료</button>
                                        <button type="button" name="inspect_img_status" class="btn-status btn btn-sm {{ $inspect_img_status == InspectConstant::IS_INSPECT_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_N }}">미완료</button>
                                    </td>
                                    <td>
                                        <div class="d-flex text-center">
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                완료<br>
                                                {{ number_format($imgInspectYCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                미완료<br>
                                                {{ number_format($imgInspectNCnt) }}건
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품정보 검수</th>
                                    <td>
                                        <button type="button" name="inspect_prd_status" class="btn-status btn btn-sm {{ $inspect_prd_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="inspect_prd_status" class="btn-status btn btn-sm {{ $inspect_prd_status == InspectConstant::IS_INSPECT_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_Y }}">완료</button>
                                        <button type="button" name="inspect_prd_status" class="btn-status btn btn-sm {{ $inspect_prd_status == InspectConstant::IS_INSPECT_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_N }}">미완료</button>
                                    </td>
                                    <td>
                                        <div class="d-flex text-center">
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                완료<br>
                                                {{ number_format($prdInspectYCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                미완료<br>
                                                {{ number_format($prdInspectNCnt) }}건
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정보고시 검수</th>
                                    <td>
                                        <button type="button" name="inspect_gosi_status" class="btn-status btn btn-sm {{ $inspect_gosi_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="inspect_gosi_status" class="btn-status btn btn-sm {{ $inspect_gosi_status == InspectConstant::IS_INSPECT_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_Y }}">완료</button>
                                        <button type="button" name="inspect_gosi_status" class="btn-status btn btn-sm {{ $inspect_gosi_status == InspectConstant::IS_INSPECT_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ InspectConstant::IS_INSPECT_N }}">미완료</button>
                                    </td>
                                    <td>
                                        <div class="d-flex text-center">
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                완료<br>
                                                {{ number_format($gosiInspectYCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 60px;">
                                                미완료<br>
                                                {{ number_format($gosiInspectNCnt) }}건
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">W type</th>
                                    <td colspan="2">
                                        <button type="button" name="w_type" class="btn-status btn btn-sm {{ $w_type == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="w_type" class="btn-status btn btn-sm {{ $w_type == WConstant::WAPP_W1 ? "btn-primary" : "btn-dark" }}"
                                        value="{{ WConstant::WAPP_W1 }}">W1</button>
                                        <button type="button" name="w_type" class="btn-status btn btn-sm {{ $w_type == WConstant::WAPP_W2 ? "btn-primary" : "btn-dark" }}"
                                        value="{{ WConstant::WAPP_W2 }}">W2</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">카테고리 맵핑</th>
                                    <td colspan="2">
                                        <button type="button" name="mapping_status" class="btn-status btn btn-sm {{ $mapping_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="mapping_status" class="btn-status btn btn-sm {{ $mapping_status == ProductConstant::MAPPING_STATUS_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MAPPING_STATUS_Y }}">맵핑</button>
                                        <button type="button" name="mapping_status" class="btn-status btn btn-sm {{ $mapping_status == ProductConstant::MAPPING_STATUS_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MAPPING_STATUS_N }}">미맵핑</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">판매 상태</th>
                                    <td colspan="2">
                                        <button type="button" name="prd_status" class="btn-status btn btn-sm {{ $prd_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="prd_status" class="btn-status btn btn-sm {{ $prd_status == ProductConstant::PRD_STATUS_PUBLISH ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::PRD_STATUS_PUBLISH }}">정상판매</button>
                                        <button type="button" name="prd_status" class="btn-status btn btn-sm {{ $prd_status == ProductConstant::PRD_STATUS_STOP ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::PRD_STATUS_STOP }}">판매중지</button>
                                        <button type="button" name="prd_status" class="btn-status btn btn-sm {{ $prd_status == ProductConstant::PRD_STATUS_MISS ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::PRD_STATUS_MISS }}">정보누락</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">판매가 설정</th>
                                    <td colspan="2">
                                        <button type="button" name="mdPrice_status" class="btn-status btn btn-sm {{ $mdPrice_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="mdPrice_status" class="btn-status btn btn-sm {{ $mdPrice_status == ProductConstant::MD_PRICE_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MD_PRICE_Y }}">설정</button>
                                        <button type="button" name="mdPrice_status" class="btn-status btn btn-sm {{ $mdPrice_status == ProductConstant::MD_PRICE_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MD_PRICE_N }}">미설정</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">배송비 적용</th>
                                    <td colspan="2">
                                        <button type="button" name="weight_status" class="btn-status btn btn-sm {{ $weight_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="weight_status" class="btn-status btn btn-sm {{ $weight_status == ProductConstant::WEIGHT_STATUS_PRODUCT ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::WEIGHT_STATUS_PRODUCT }}">{{ ProductConstant::WEIGHT_STATUS[ProductConstant::WEIGHT_STATUS_PRODUCT] }}</button>
                                        <button type="button" name="weight_status" class="btn-status btn btn-sm {{ $weight_status == ProductConstant::WEIGHT_STATUS_CATEGORY ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::WEIGHT_STATUS_CATEGORY }}">{{ ProductConstant::WEIGHT_STATUS[ProductConstant::WEIGHT_STATUS_CATEGORY] }}</button>
                                        <button type="button" name="weight_status" class="btn-status btn btn-sm {{ $weight_status == ProductConstant::WEIGHT_STATUS_NONE ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::WEIGHT_STATUS_NONE }}">{{ ProductConstant::WEIGHT_STATUS[ProductConstant::WEIGHT_STATUS_NONE] }}</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">최소 구매 수량</th>
                                    <td colspan="2">
                                        <button type="button" name="quantity_count" class="btn-status btn btn-sm {{ $quantity_count == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="quantity_count" class="btn-status btn btn-sm {{ $quantity_count == ProductConstant::QUANTITY_COUNT_1 ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::QUANTITY_COUNT_1 }}">{{ ProductConstant::QUANTITY_LIST[ProductConstant::QUANTITY_COUNT_1] }}</button>
                                        <button type="button" name="quantity_count" class="btn-status btn btn-sm {{ $quantity_count == ProductConstant::QUANTITY_COUNT_2 ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::QUANTITY_COUNT_2 }}">{{ ProductConstant::QUANTITY_LIST[ProductConstant::QUANTITY_COUNT_2] }}</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">채널 미전송</th>
                                    <td colspan="2">
                                        @foreach (MallConstant::SEND_CHANNEL_LIST as $mallKey => $mallChannel)
                                            @php
                                                $checked = "";
                                                $no_send_channel_list = explode(",", $no_send_channel);
                                                if( in_array($mallKey, $no_send_channel_list) ){
                                                    $checked = "checked";
                                                }
                                            @endphp
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="no_send_channel_list" id="no_send_channel_{{ $mallKey }}" value="{{ $mallKey }}" {{ $checked }}>
                                                <label class="form-check-label" for="no_send_channel_{{ $mallKey }}">{{ $mallChannel }}</label>
                                            </div>
                                        @endforeach
                                        <label class="text-danger">* 복수 선택 시 AND 조건으로 검색됩니다.</label>
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
                                            <option value="prd_name" @if($search_cls == "prd_name") selected @endif>상품명</option>
                                            <option value="option_name" @if($search_cls == "option_name") selected @endif>옵션명</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정렬</th>
                                    <td colspan="2">
                                        <select class="form-select" name="sort" style="width: 200px">
                                            <option value="updated_at|desc" @if($sort == "updated_at|desc") selected @endif>수정일 내림차순</option>
                                            <option value="updated_at|asc" @if($sort == "updated_at|asc") selected @endif>수정일 오름차순</option>
                                            <option value="created_at|desc" @if($sort == "created_at|desc") selected @endif>등록일 내림차순</option>
                                            <option value="created_at|asc" @if($sort == "created_at|asc") selected @endif>동록일 오름차순</option>
                                            <option value="start_quantity|desc" @if($sort == "start_quantity|desc") selected @endif>최소구매수량 내림차순</option>
                                            <option value="start_quantity|asc" @if($sort == "start_quantity|asc") selected @endif>최소구매수량 오름차순</option>
                                            <option value="md_price|desc" @if($sort == "md_price|desc") selected @endif>MD 판매가 내림차순</option>
                                            <option value="md_price|asc" @if($sort == "md_price|asc") selected @endif>MD 판매가 오름차순</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=50 @if($pageSize == 50) selected @endif>50개 노출</option>
                                            <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                            <option value=150 @if($pageSize == 150) selected @endif>150개 노출</option>
                                            <option value=200 @if($pageSize == 200) selected @endif>200개 노출</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="3">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/product/list'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>

                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-dark text-white me-2" id="btn-send-select">상품 전송 요청</button>
                    <button class="btn btn-md btn-outline-danger me-2" id="btn-recollect-select">재 수집 요청</button>
                    <button class="btn btn-md btn-outline-primary me-2" id="btn-inspect-select">검수상태 변경</button>
                    <button class="btn btn-md btn-outline-danger me-2" id="btn-status-select">판매상태 변경</button>
                    <button class="btn btn-md btn-outline-dark me-2" id="btn-select">선택번역 요청</button>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 50px">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 120px">
                                    제품ID<br>
                                    (카테고리ID)
                                </th>
                                <th scope="col">제품명(국문)</th>
                                <th scope="col" style="width: 50px">최소 구매 수량</th>
                                <th scope="col" style="width: 80px">원본이미지</th>
                                <th scope="col" style="width: 80px">번역이미지</th>
                                <th scope="col" style="width: 120px" class="text-center">
                                    W 공급가<br>
                                    (환율: {{ number_format($exchangeRate, 2); }}원)
                                </th>
                                <th scope="col" style="width: 120px" class="text-center">
                                    기준: 중량 (kg)<br>
                                    배송비 (원)
                                </th>
                                <th scope="col" style="width: 100px" class="text-center">
                                    일반 판매가(원)
                                </th>
                                <th scope="col" style="width: 100px" class="text-center">
                                    MD 판매가(원)
                                </th>
                                <th scope="col" style="width: 100px" class="text-center">
                                    온채널<br>
                                    공급가(원)
                                </th>
                                <th scope="col" style="width: 100px" class="text-center">
                                    상품상태
                                </th>
                                <th style="width: 100px" class="text-center">
                                    상품전송
                                </th>
                                <th style="width: 100px" class="text-center">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data->offer_id }}" class="form-check-input chk-inp" type="checkbox" value="{{ $data->offer_id }}">
                                    </td>
                                    <td>
                                        <label for="checkbox-{{ $data->offer_id }}" class="cursor-pointer">
                                            {{ number_format(($datas->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td>
                                        <a href="https://detail.1688.com/offer/{{ $data->offer_id }}.html" target="_blank">{{ $data->offer_id }}</a>
                                        @if ($data->mapping_status == ProductConstant::MAPPING_STATUS_Y)
                                            <br>
                                            <span>({{ $data->category_id }})</span>
                                        @endif
                                        @if ($data->mapping_status == ProductConstant::MAPPING_STATUS_N)
                                            <button class="btn btn-sm btn-outline-success btn-modal mt-1" cateid={{ $data->category_id }}>맵핑하기</button>
                                            <br>
                                            <span class="text-danger">*카테고리 미맵핑</span>
                                            <span class="text-danger">({{ $data->category_id }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $data->prd_name_kr }}</small>
                                    </td>
                                    <td>
                                        {{ number_format($data->start_quantity) }}
                                    </td>
                                    <td>
                                        @if( $data->main_img != null && $data->main_img->img_url_origin )
                                            <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_origin }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td>
                                        @if( $data->main_img != null && $data->main_img->img_url_trans )
                                            <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_trans }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- w 공급가 --}}
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                                {{ $option->price_1688 }}(위안)<br>
                                                {{ number_format(wOptionPrice($option->price_1688)) }}(원)
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- 기준: 중량 --}}
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
                                        {{-- 일반 판매자가 --}}
                                        @if (count($data->options) > 0)
                                            @php
                                                $option         = $data->options[0];
                                                $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                                                if( $data->weight_type != null ){
                                                    $delivery_price = $data->delivery_price;
                                                }
                                            @endphp
                                                {{ number_format(calcWSalePrice($option->price_1688, $delivery_price)) }}
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- MD 판매자가 --}}
                                        @if (count($data->options) > 0)
                                            @php
                                                $option         = $data->options[0];
                                                $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                                                if( $data->weight_type != null ){
                                                    $delivery_price = $data->delivery_price;
                                                }
                                            @endphp
                                            @if ($option->md_price)
                                                @php
                                                    $salePrice = calcWSalePrice($option->price_1688);
                                                    $saleHigh  = compareWSalePrice($salePrice, $option->md_price);
                                                @endphp
                                                @if ($saleHigh === true)
                                                    <button class="btn btn-sm btn-primary btn-md-modi" offerid={{ $data->offer_id }} saleprice={{ $salePrice }} mdprice={{ $option->md_price }}>{{ number_format($option->md_price) }}</button>
                                                @else
                                                    <button class="btn btn-sm btn-danger btn-md-modi" offerid={{ $data->offer_id }} saleprice={{ $salePrice }} mdprice={{ $option->md_price }}>{{ number_format($option->md_price) }}</button>
                                                @endif
                                            @else
                                                <button class="btn btn-sm btn-warning btn-md-modi" offerid={{ $data->offer_id }} saleprice={{ calcWSalePrice($option->price_1688, $delivery_price) }} mdprice=0>MD 가격 설정</button>
                                            @endif
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{-- 온채널 공급가 --}}
                                        @if (count($data->options) > 0)
                                            @php
                                                $option         = $data->options[0];
                                                $delivery_price = ProductConstant::WEIGHT_STATUS_NONE_PRICE;
                                                if( $data->weight_type != null ){
                                                    $delivery_price = $data->delivery_price;
                                                }
                                            @endphp
                                            {{ number_format(calcOnchannelOptionPrice($option->price_1688, $delivery_price)) }}
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
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
                                        <div class="btn bg-success text-white btn-log-detail" style="font-size: 12px;" offerid={{ $data->offer_id }}>
                                            <span class="d-block">전송 채널</span>
                                            @if ($data->es_w_log != null && $data->es_w_log->regist_success == MallConstant::REGIST_SUCCESS)
                                                <span class="d-block">더블유</span>
                                            @endif
                                            @if ($data->es_drop_hub_log != null && $data->es_drop_hub_log->regist_success == MallConstant::REGIST_SUCCESS)
                                                <span class="d-block">Drop Hub</span>
                                            @endif
                                            @if ($data->oc_public_log != null && $data->oc_public_log->regist_success == MallConstant::REGIST_SUCCESS)
                                                <span class="d-block">OC: 일반</span>
                                            @endif
                                            @if ($data->oc_private_log != null && $data->oc_private_log->regist_success == MallConstant::REGIST_SUCCESS)
                                                <span class="d-block">OC: 사업</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-detail" offerid={{ $data->offer_id }}>국문 상세</button>
                                        @if ($data->prd_name_en != "")
                                            <button class="btn btn-sm btn-outline-success btn-en-detail mt-2" offerid={{ $data->offer_id }}>영문 상세</button>
                                        @endif
                                        @if ($data->trans_status == ProductConstant::IMG_TRANS_Y )
                                            <button class="btn btn-sm btn-outline-success AItoolBtn mt-2" offerid={{ $data->offer_id }}>A.I Tool</button>
                                        @endif
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
                        <h5 class="modal-title" id="htmlModalLabel">카테고리 맵핑</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="chkCateIds[]" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">W 카테고리</label>
                            </div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100 cate-1688-list">
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col">
                                        <label class="fs-7">WApp 카테고리</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_first" level="1">
                                            <option value="">1차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_second" level="2">
                                            <option value="">2차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_third" level="3">
                                            <option value="">3차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_fourth" level="4">
                                            <option value="">4차 분류</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">WApp 카테고리 키워드</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="w_cate_keyword" placeholder="검색어를 입력하세요." value="">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="text-left">
                                <button type="button" class="btn btn-primary btn-w-cate-search">검색</button>
                            </div>
                            <hr>

                            <table class="table table-white bg-white w-cate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 200px">WApp 1차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 2차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 3차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 4차 카테고리</th>
                                        <th scope="col" style="width: 200px">맵핑 코드</th>
                                        <th scope="col" style="width: 100px">맵핑</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel2">MD 판매가 설정</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="offer_ids[]" />
                        <input type="hidden" name="sale_price" />

                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">일반 판매가</label>
                                    </div>
                                    <div class="col sale-price">

                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">MD 판매가</label>
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" name="md_price" placeholder="MD 판매가를 입력하세요." value="">
                                        {{-- <p class="text-danger mt-3 md-danger" style="display: none">* MD 판매가는 일반 판매가 보다 높게 입력해야 합니다.</p> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-primary btn-md-price-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose2">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal3" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel3" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel3">판매 상태 변경</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="prd_status_offer_ids[]" />

                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">판매 상태</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status1" value="{{ ProductConstant::PRD_STATUS_PUBLISH }}" checked>
                                            <label class="form-check-label" for="status1">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_PUBLISH] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status2" value="{{ ProductConstant::PRD_STATUS_STOP }}">
                                            <label class="form-check-label" for="status2">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_STOP] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status3" value="{{ ProductConstant::PRD_STATUS_EXCEPT }}">
                                            <label class="form-check-label" for="status3">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_EXCEPT] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status4" value="{{ ProductConstant::PRD_STATUS_MISS }}">
                                            <label class="form-check-label" for="status4">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_MISS] }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-status-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose3">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal4" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel4" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel4">검수상태 변경</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="inspect_status_offer_ids[]" />

                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">이미지</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_img_status" id="inspect_img_status1" value="{{ InspectConstant::IS_INSPECT_Y }}">
                                            <label class="form-check-label" for="inspect_img_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_img_status" id="inspect_img_status2" value="{{ InspectConstant::IS_INSPECT_N }}" checked>
                                            <label class="form-check-label" for="inspect_img_status2">미완료</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">상품정보</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_prd_status" id="inspect_prd_status1" value="{{ InspectConstant::IS_INSPECT_Y }}">
                                            <label class="form-check-label" for="inspect_prd_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_prd_status" id="inspect_prd_status2" value="{{ InspectConstant::IS_INSPECT_N }}" checked>
                                            <label class="form-check-label" for="inspect_prd_status2">미완료</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">정보고시</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_gosi_status" id="inspect_gosi_status1" value="{{ InspectConstant::IS_INSPECT_Y }}">
                                            <label class="form-check-label" for="inspect_gosi_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_gosi_status" id="inspect_gosi_status2" value="{{ InspectConstant::IS_INSPECT_N }}" checked>
                                            <label class="form-check-label" for="inspect_gosi_status2">미완료</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-inspect-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose4">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal5" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel5" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel5">상품 중량 (배송비) 수정</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="offerIds[]" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">중량 및 배송비</label>
                            </div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100 weight-list">
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <p class="text-danger">* 입력한 중량(배송비)는 개별 상품에 적용 됩니다.</p>
                                </div>
                            </div>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">표준 중량(kg)</label>
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" name="weight" placeholder="" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-evenly px-3 mt-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">배송비(원)</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="delivery_price" placeholder="" value="" disabled>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-weight-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose5">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal6" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel6" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel6">전송 채널 선택</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="send_prd_offer_ids[]" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-4">이지셀</label>
                            </div>
                            <div class="d-flex flex-column px-3 mt-3">
                                <div class="row w-100 mb-2">
                                    <div class="col d-flex align-items-center">
                                        <input type="checkbox" name="es-send-type" id="es-checkbox1" class="form-check-input me-2" value="{{ EasySellConstant::TYPE_W }}">
                                        <label for="es-checkbox1" class="d-flex align-items-center w-100 ms-2 cursor-pointer">
                                            <span class="fs-5 fw-bold" style="width: 150px;">더블유 (국내)</span>
                                            <span class="ms-4">*국문 정보 | 번역 이미지 전송</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row w-100 mb-2 mt-1">
                                    <div class="col d-flex align-items-center">
                                        <input type="checkbox" name="es-send-type" id="es-checkbox2" class="form-check-input me-2" value="{{ EasySellConstant::TYPE_DROPHUB }}">
                                        <label for="es-checkbox2" class="d-flex align-items-center w-100 ms-2 cursor-pointer">
                                            <span class="fs-5 fw-bold" style="width: 150px;">Drop Hub (해외)</span>
                                            <span class="ms-4">*영문 정보 | 원본 이미지 전송</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex align-items-center">
                                <label class="fs-4">온채널</label>
                            </div>
                            <div class="d-flex flex-column px-3 mt-3">
                                <div class="row w-100 mb-2">
                                    <div class="col d-flex align-items-center">
                                        <input type="checkbox" name="oc-send-type" id="oc-checkbox1" class="form-check-input me-2" value="{{ OnchannelConstant::PRD_CHANNEL }}">
                                        <label for="oc-checkbox1" class="d-flex align-items-center w-100 ms-2 cursor-pointer">
                                            <span class="fs-5 fw-bold" style="width: 150px;">일반 상품</span>
                                            <span class="ms-4">*국문 정보 | 원본 이미지 전송</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row w-100 mb-2 mt-1">
                                    <div class="col d-flex align-items-center">
                                        <input type="checkbox" name="oc-send-type" id="oc-checkbox2" class="form-check-input me-2" value="{{ OnchannelConstant::PRD_CHANNEL_PRIVATE }}">
                                        <label for="oc-checkbox2" class="d-flex align-items-center w-100 ms-2 cursor-pointer">
                                            <span class="fs-5 fw-bold" style="width: 150px;">사입 상품</span>
                                            <span class="ms-4">*국문 정보 | 번역 이미지 전송</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-send-product">전송</button>
                        <button type="button" class="btn btn-secondary htmlModalClose6">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal7" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel7" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel7">상품 전송 상세</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-4">이지셀</label>
                            </div>
                            <div class="d-flex flex-column px-3 mt-3">
                                <div class="row w-100 mb-2">
                                    <div class="col d-flex align-items-center">
                                        <label class="d-flex align-items-center w-100 ms-2">
                                            <span class="fs-5 fw-bold" style="width: 150px;">더블유 (국내)</span>
                                            <span class="w-log-text"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row w-100 mb-2 mt-1">
                                    <div class="col d-flex align-items-center">
                                        <label class="d-flex align-items-center w-100 ms-2">
                                            <span class="fs-5 fw-bold" style="width: 150px;">Drop Hub (해외)</span>
                                            <span class="drop-log-text"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex align-items-center">
                                <label class="fs-4">온채널</label>
                            </div>
                            <div class="d-flex flex-column px-3 mt-3">
                                <div class="row w-100 mb-2">
                                    <div class="col d-flex align-items-center">
                                        <label class="d-flex align-items-center w-100 ms-2">
                                            <span class="fs-5 fw-bold" style="width: 150px;">일반 상품</span>
                                            <span class="oc-log-text"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="row w-100 mb-2 mt-1">
                                    <div class="col d-flex align-items-center">
                                        <label class="d-flex align-items-center w-100 ms-2">
                                            <span class="fs-5 fw-bold" style="width: 150px;">사입 상품</span>
                                            <span class="oc-private-log-text"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary htmlModalClose7">닫기</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
<script type="text/javascript">

</script>

@endsection
