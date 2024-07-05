@php
    use App\Constants\ProductConstant;
    use App\Constants\LogConstant;
    use App\Constants\CollectConstatnt;
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
            <div class="col-12 mb-3 mt-3">
                
                <div class="card">
                    <div class="card-header d-flex justify-content-around align-items-center">
                        <div style="max-width: 250px; overflow-wrap: break-word;">제목: {{ $obj->search_title }}</div>
                        <div>상태: {{ ProductConstant::SEARCH_STATUS[$obj->status] }}</div>
                        <div>요청 수 : {{ number_format($obj->search_count) }}건</div>
                        <div>완료 수 : {{ number_format($obj->details_y_cnt()) }}건</div>
                        <div>실패 수 : {{ number_format($obj->details_n_cnt()) }}건</div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h4>상품 수집 옵션</h4>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <tr class="align-middle">
                                <th style="width: 120px;">미수집 상품</th>
                                <td class="d-flex align-items-center">
                                    <div class="d-flex align-items-center me-4">
                                        <label class="bg-light px-2 py-1 rounded me-2">상품 수집</label>
                                        <div class="btn-group" role="group" aria-label="상품 수집">
                                            <input type="radio" class="btn-check" name="nCollectOption" id="nCollectNone" value="{{ CollectConstatnt::COLLECT_NONE }}" checked>
                                            <label class="btn btn-outline-primary" for="nCollectNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_NONE] }}</label>
                
                                            <input type="radio" class="btn-check" name="nCollectOption" id="nCollect" value="{{ CollectConstatnt::COLLECT_PRODUCT }}">
                                            <label class="btn btn-outline-primary" for="nCollect">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_PRODUCT] }}</label>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="bg-light px-2 py-1 rounded me-2">번역 요청</label>
                                        <div class="btn-group" role="group" aria-label="번역 요청">
                                            <input type="radio" class="btn-check" name="nTranslateOption" id="nTranslateNone" value="{{ CollectConstatnt::TRANSLATE_NONE }}" checked>
                                            <label class="btn btn-outline-primary" for="nTranslateNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_NONE] }}</label>
                
                                            <input type="radio" class="btn-check" name="nTranslateOption" id="nTranslateAll" value="{{ CollectConstatnt::TRANSLATE_ALL }}">
                                            <label class="btn btn-outline-primary" for="nTranslateAll">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_ALL] }}</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr class="align-middle">
                                <th style="width: 120px;">수집완료 상품</th>
                                <td class="d-flex align-items-center">
                                    <div class="d-flex align-items-center me-4">
                                        <label class="bg-light px-2 py-1 rounded me-2">상품 수집</label>
                                        <div class="btn-group" role="group" aria-label="상품 수집">
                                            <input type="radio" class="btn-check" name="yCollectOption" id="yCollectNone" value="{{ CollectConstatnt::COLLECT_NONE }}" checked>
                                            <label class="btn btn-outline-primary" for="yCollectNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_NONE] }}</label>
                
                                            <input type="radio" class="btn-check" name="yCollectOption" id="yCollect" value="{{ CollectConstatnt::COLLECT_PRODUCT }}">
                                            <label class="btn btn-outline-primary" for="yCollect">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_PRODUCT] }}</label>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <label class="bg-light px-2 py-1 rounded me-2">번역 요청</label>
                                        <div class="btn-group" role="group" aria-label="번역 요청">
                                            <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateNone" value="{{ CollectConstatnt::TRANSLATE_NONE }}" checked>
                                            <label class="btn btn-outline-primary" for="yTranslateNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_NONE] }}</label>
                
                                            <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateAll" value="{{ CollectConstatnt::TRANSLATE_ALL }}">
                                            <label class="btn btn-outline-primary" for="yTranslateAll">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_ALL] }}</label>

                                            <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateStatusY" value="{{ CollectConstatnt::TRANSLATE_STATUS_Y }}">
                                            <label class="btn btn-outline-primary" for="yTranslateStatusY">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_STATUS_Y] }}</label>

                                            <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateStatusN" value="{{ CollectConstatnt::TRANSLATE_STATUS_N }}">
                                            <label class="btn btn-outline-primary" for="yTranslateStatusN">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_STATUS_N] }}</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </table>
                        <div class="d-flex justify-content-start">
                            <button type="button" class="btn btn-md btn-outline-dark me-2" id="btn-select">선택상품 수집</button>
                            <button type="button" class="btn btn-md btn-outline-success" id="btn-all">전체상품 수집</button>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
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
            let offer_ids        = [];
            let hasPrd           = false;
            let nCollectOption   = $('input[name=nCollectOption]:checked').val();
            let nTranslateOption = $('input[name=nTranslateOption]:checked').val();
            let yCollectOption   = $('input[name=yCollectOption]:checked').val();
            let yTranslateOption = $('input[name=yTranslateOption]:checked').val();

            $(".chk-inp:checked").each(function(index, element){
                offer_ids.push($(this).val());
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
                        "collectParams": {
                            nCollectOption,
                            nTranslateOption,
                            yCollectOption,
                            yTranslateOption
                        } 
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
            let offer_ids        = [];
            let hasPrd           = false;
            let nCollectOption   = $('input[name=nCollectOption]:checked').val();
            let nTranslateOption = $('input[name=nTranslateOption]:checked').val();
            let yCollectOption   = $('input[name=yCollectOption]:checked').val();
            let yTranslateOption = $('input[name=yTranslateOption]:checked').val();

            $(".chk-inp").each(function(index, element){
                offer_ids.push($(this).val());
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
                        "collectParams": {
                            nCollectOption,
                            nTranslateOption,
                            yCollectOption,
                            yTranslateOption
                        } 
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
