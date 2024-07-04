@php
    use App\Constants\ProductConstant;
    use App\Constants\CollectConstatnt;
    $exchangeRate = env("1688_EXCHANGE_RATE", 200);
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
                <li class="breadcrumb-item">W</li>
                <li class="breadcrumb-item">상품</li>
                <li class="breadcrumb-item">상품 수집 관리</li>
                <li class="breadcrumb-item active" aria-current="page">상품 ID로 수집</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">
                <form id="searchFrm">
                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 검색</th>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/product/queryProductDetail'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
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

                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 150px">제품ID</th>
                                <th scope="col">제품명</th>
                                <th scope="col">제품명(번역)</th>
                                <th scope="col" style="width: 100px">원본이미지</th>
                                <th scope="col" style="width: 100px">판매량</th>
                                <th scope="col" style="width: 150px" class="text-center">
                                    W 공급가<br>
                                    (환율: {{ number_format($exchangeRate) }}원)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data["offerId"] }}" hasprd={{ $data["hasPrd"] }}>
                                    </td>
                                    <td>
                                        {{ count($datas) - $index }}
                                    </td>
                                    <td>
                                        <a href="https://detail.1688.com/offer/{{ $data["offerId"] }}.html" target="_blank">{{ $data["offerId"] }}</a>
                                        @if( $data["hasPrd"] == ProductConstant::HAS_PRD_Y )
                                            <p class="text-danger">[수집완료]</p>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data["subject"] }}
                                    </td>
                                    <td>
                                        {{ $data["subjectTrans"] }}
                                    </td>
                                    <td>
                                        <img class="lazy-img preview-image" data-src="{{ $data["productImage"]["images"][0] }}" width=60 height=60/>
                                    </td>
                                    <td>
                                        @if( isset($data["soldOut"]) )
                                            {{ number_format($data["soldOut"]) }}
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $data["price_1688"] }}(위안)<br>
                                        {{ number_format($data["option_price"]) }}(원)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $("#btn-select").click(function(){
            let offer_ids        = [];
            let hasPrd           = false;
            let nCollectOption   = $('input[name=nCollectOption]:checked').val();
            let nTranslateOption = $('input[name=nTranslateOption]:checked').val();
            let yCollectOption   = $('input[name=yCollectOption]:checked').val();
            let yTranslateOption = $('input[name=yTranslateOption]:checked').val();

            $(".chk-inp:checked").each(function(index, element){
                // if( $(this).attr("hasPrd") == "N" ){
                //     offer_ids.push($(this).val());
                // } else {
                //     hasPrd = true;
                // }
                offer_ids.push($(this).val());
            });

            if(offer_ids.length < 1 ){
                return alert("검색 된 상품이 없습니다.");
            }

            if(confirm(`${offer_ids.length}건의 상품을 수집 하시겠습니까?\n이미 수집 된 상품은 수집 대상에서 제외 됩니다.`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectProduct') }}",
                    "data"       : { 
                        offer_ids,
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
                // if( $(this).attr("hasPrd") == "N" ){
                //     offer_ids.push($(this).val());
                // } else {
                //     hasPrd = true;
                // }
                offer_ids.push($(this).val());
            });

            if(offer_ids.length < 1 ){
                return alert("검색 된 상품이 없습니다.");
            }

            if(confirm(`${offer_ids.length}건의 상품을 수집 하시겠습니까?\n이미 수집 된 상품은 수집 대상에서 제외 됩니다.`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectProduct') }}",
                    "data"       : { 
                        offer_ids,
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
    })
</script>

@endsection
