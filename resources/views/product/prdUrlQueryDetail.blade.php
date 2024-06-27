@php
    use App\Constants\ProductConstant;
    use App\Constants\LogConstant;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .img-limited {
        max-height: 80px; /* 이미지 최대 높이 */
        width: 100%; /* 너비를 카드에 맞춤 */
        object-fit: contain; /* 이미지가 카드 너비에 맞춰 잘리도록 설정 */
    }
    .body-limited {
        max-height: 100px; /* card-body 최대 높이 */
        overflow-y: auto; /* 내용이 넘치면 스크롤바 생성 */
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
                <li class="breadcrumb-item">W</li>
                <li class="breadcrumb-item">상품</li>
                <li class="breadcrumb-item">상품 수집 관리</li>
                <li class="breadcrumb-item">상품상세 URL로 수집</li>
                <li class="breadcrumb-item active" aria-current="page">상세보기</li>
            </ol>
        </nav>
        
        <div class="row my-4 bg-white py-3">
            <div class="mt-3 d-flex align-items-center justify-content-between">
                <div class="d-flex justify-content-start">
                    <input type="checkbox" id="chk-ai-active" class="me-2">
                    <label for="chk-ai-active" class="text-danger">수집 시 자동 번역 요청(이지셀: 더블유)</label>
                </div>
            </div>

            <div class="col-12 mb-3 mt-3">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div style="max-width: 250px; overflow-wrap: break-word;">제목: {{ $obj->search_title }}</div>
                        <div>상태: {{ ProductConstant::SEARCH_STATUS[$obj->status] }}</div>
                        <div>요청 수 : {{ number_format($obj->search_count) }}건</div>
                        <div>완료 수 : {{ number_format($obj->details_y_cnt()) }}건</div>
                        <div>실패 수 : {{ number_format($obj->details_n_cnt()) }}건</div>
                        <div>
                            <button class="btn btn-md btn-outline-dark me-2" id="btn-select">선택상품 수집</button>
                            <button class="btn btn-md btn-outline-success" id="btn-all">전체상품 수집</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-start">
                            <label class="form-check-label me-2" for="allCheckbox">전체 선택</label>
                            <input class="form-check-input" type="checkbox" id="allCheckbox">
                        </div>
                        <div class="row">
                            @foreach ($obj->details as $detail)
                                @if( $detail->is_search == ProductConstant::IS_SEARCH_Y )
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                        <div class="card h-100" onclick="toggleCheckbox(event, this)">
                                            <div class="position-relative">
                                                <img src="{{ $detail->prd_image }}" class="card-img-top img-limited" alt="이미지 설명">
                                                <div class="position-absolute" style="top: 10px; left: 10px;">
                                                    <input type="checkbox" class="form-check-input chk-inp" value="{{ $detail->offer_id }}" hasprd={{ $detail->hasPrd }}>
                                                </div>
                                            </div>
                                            <div class="card-body body-limited">
                                                <p class="card-text" style="font-size: 12px">
                                                    <a href="https://detail.1688.com/offer/{{ $detail->offer_id }}.html" target="_blank">
                                                        offerID: {{ $detail->offer_id }}
                                                    </a>
                                                    @if( $detail->hasPrd == ProductConstant::HAS_PRD_Y )
                                                        <span class="text-danger">[수집완료]</span>
                                                    @endif
                                                    <br>
                                                    {{ $detail->prd_name_kr }}
                                                    <br>
                                                    판매량: {{ number_format($detail->sold_out) }}
                                                    <br>
                                                    가격: {{ number_format($detail->price_1688) }}(元)
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                        <div class="card h-100">
                                            <div class="card-body d-flex justify-content-center align-items-center text-danger fs-4">
                                                <p class="card-text text-center m-0">
                                                    <a href="https://detail.1688.com/offer/{{ $detail->offer_id }}.html" target="_blank">
                                                        offerID: {{ $detail->offer_id }}
                                                    </a>
                                                    {{ $detail->msg }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
            
                    </div>
                </div>
            </div>


        </div>
        
        
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        var log_type = "{{ LogConstant::COLLECT_API_URLQUERY }}";

        $("#btn-select").click(function(){
            let offer_ids = [];
            let hasPrd    = false;
            let aiActive  = $("#chk-ai-active").is(":checked") ? 'true' : 'false';

            $(".chk-inp:checked").each(function(index, element){
                offer_ids.push($(this).val());
                if( $(this).attr("hasPrd") != "N" ){
                    hasPrd = true;
                }
            });
            
            if(offer_ids.length < 1 ){
                return alert("검색 된 상품이 없습니다.");
            }
            
            let text = `${offer_ids.length}건의 상품을 수집 하시겠습니까?`;
            if( hasPrd === true ){
                text = `${offer_ids.length}건의 상품을 수집 하시겠습니까?\n재 수집 시 저장 된 상품의 정보가 초기화 됩니다.`;
            }

            if(confirm(text)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectProduct') }}",
                    "data"       : { 
                        offer_ids,
                        log_type,
                        "ai_active": aiActive
                    },
                    beforeSend: function () {
                    },
                    complete: function () {
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

        $("#btn-all").click(function(){
            let offer_ids = [];
            let hasPrd    = false;
            let aiActive  = $("#chk-ai-active").is(":checked") ? 'true' : 'false';

            $(".chk-inp").each(function(index, element){
                offer_ids.push($(this).val());
                if( $(this).attr("hasPrd") != "N" ){
                    hasPrd = true;
                }
            });

            if(offer_ids.length < 1 ){
                return alert("검색 된 상품이 없습니다.");
            }

            let text = `${offer_ids.length}건의 상품을 수집 하시겠습니까?`;
            if( hasPrd === true ){
                text = `${offer_ids.length}건의 상품을 수집 하시겠습니까?\n재 수집 시 저장 된 상품의 정보가 초기화 됩니다.`;
            }

            if(confirm(text)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectProduct') }}",
                    "data"       : { 
                        offer_ids,
                        log_type,
                        "ai_active": aiActive
                    },
                    beforeSend: function () {
                    },
                    complete: function () {
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
    });
</script>

@endsection
