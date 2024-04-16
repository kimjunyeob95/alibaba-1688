@php
    use App\Constants\ProductConstant;
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
                <li class="breadcrumb-item">상품 리스트</li>
                <li class="breadcrumb-item active" aria-current="page">1688 수집 상품 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="trans_status" value={{ $trans_status }}>
                    <input type="hidden" name="mapping_status" value={{ $mapping_status }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 현황</th>
                                    <td colspan="3">
                                        <div class="d-flex text-center">
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 100px;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 100px;">
                                                번역완료<br>
                                                {{ number_format($transYCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 100px;">
                                                번역 미완료<br>
                                                {{ number_format($transNCnt) }}건
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 번역</th>
                                    <td>
                                        <button type="button" name="trans_status" class="btn-status btn btn-md {{ $trans_status == ProductConstant::TRANS_STATUS_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::TRANS_STATUS_Y }}">완료</button>
                                        <button type="button" name="trans_status" class="btn-status btn btn-md {{ $trans_status == ProductConstant::TRANS_STATUS_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::TRANS_STATUS_N }}">미완료</button>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">카테고리 맵핑</th>
                                    <td>
                                        <button type="button" name="mapping_status" class="btn-status btn btn-md {{ $mapping_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="mapping_status" class="btn-status btn btn-md {{ $mapping_status == ProductConstant::MAPPING_STATUS_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MAPPING_STATUS_Y }}">맵핑</button>
                                        <button type="button" name="mapping_status" class="btn-status btn btn-md {{ $mapping_status == ProductConstant::MAPPING_STATUS_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MAPPING_STATUS_N }}">미맵핑</button>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="offer_id" @if($search_cls == "offer_id") selected @endif>제품 ID</option>
                                            <option value="prd_name_trans" @if($search_cls == "prd_name_trans") selected @endif>상품명</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
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
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/product/list'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3 d-flex justify-content-end">
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
                                <th scope="col" style="width: 150px">제품ID</th>
                                <th scope="col">제품명</th>
                                <th scope="col">제품명(번역)</th>
                                <th scope="col" style="width: 50px">최소 구매 수량</th>
                                <th scope="col" style="width: 100px">원본이미지</th>
                                <th scope="col" style="width: 100px">번역이미지</th>
                                <th scope="col" style="width: 150px" class="text-center">
                                    1688 소비자가<br>
                                    옵션가격<br>
                                    온채널가<br>
                                    소비자가
                                </th>
                                <th scope="col" style="width: 100px" class="text-center">이미지<br>번역여부</th>
                                <th style="width: 100px" class="text-center">관리</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->offer_id }}">
                                    </td>
                                    <td>
                                        {{ number_format(($datas->total() - $offset) - $index) }}
                                    </td>
                                    <td>
                                        <a href="https://detail.1688.com/offer/{{ $data->offer_id }}.html" target="_blank">{{ $data->offer_id }}</a>
                                        @if ($data->mapping_status == ProductConstant::MAPPING_STATUS_N)
                                            <p class="text-danger">*카테고리 미맵핑</p>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->prd_name }}
                                    </td>
                                    <td>
                                        {{ $data->prd_name_trans }}
                                    </td>
                                    <td>
                                        {{ number_format($data->start_quantity) }}
                                    </td>
                                    <td>
                                        <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_origin }}" width=60 height=60/>
                                    </td>
                                    <td>
                                        @if( $data->main_img->img_url_trans )
                                            <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_trans }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                                {{ $option->price_1688 }}(元)<br>
                                                {{ number_format($option->option_price) }}(원)<br>
                                                {{ number_format($option->onch_price) }}(원)<br>
                                                {{ number_format($option->cus_price) }}(원)<br>
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ ProductConstant::IMG_TRANS_STATUS[$data->trans_status] }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-detail" offerid={{ $data->offer_id }}>상세보기</button>
                                        <button class="btn btn-sm btn-outline-primary btn-trans-img mt-2" offerid={{ $data->offer_id }}>번역요청</button>
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
        $(".btn-detail").click(function(){
            let offer_id = $(this).attr("offerid");
            location.href = `/product/${offer_id}`;
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });

        $(".btn-trans-img").click(function(){
            let offerIds = [$(this).attr("offerid")];

            if(confirm(`해당 상품을 번역 요청 하시겠습니까?`)){
                $("#loadingOverlay").show();

                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('genuio.imgTransRequest') }}",
                    "data"       : { offerIds },
                    beforeSend: function () {
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });
        
        $("#btn-select").click(function(){
            let offerIds = [];

            $(".chk-inp:checked").each(function(index, element){
                offerIds.push($(this).val());
            });

            if(offerIds.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            if(confirm(`${offerIds.length}건의 상품을 번역 요청 하시겠습니까?`)){
                $("#loadingOverlay").show();

                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('genuio.imgTransRequest') }}",
                    "data"       : { offerIds },
                    beforeSend: function () {
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });
        
    })
</script>

@endsection
