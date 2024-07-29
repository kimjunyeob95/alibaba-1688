@php
    use App\Constants\ProductConstant;
    use App\Constants\CollectConstatnt;
    $exchangeRate = config('1688_EXCHANGE_RATE', 200);
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
                <li class="breadcrumb-item active" aria-current="page">기본 정보로 수집</li>
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
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="productCollectionId" @if($search_cls == "productCollectionId") selected @endif>PALLET ID</option>
                                            <option value="categoryId" @if($search_cls == "categoryId") selected @endif>Category ID</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" class="form-control" id="keyword" name="keyword" placeholder="검색어 입력" value="{{ $keyword }}">
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정렬</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="sort">
                                            <option value="monthSold|desc" @if($sort == "monthSold|desc") selected @endif>판매량 내림차순</option>
                                            <option value="monthSold|asc" @if($sort == "monthSold|asc") selected @endif>판매량 오름차순</option>
                                            <option value="price|desc" @if($sort == "price|desc") selected @endif>가격 내림차순</option>
                                            <option value="price|asc" @if($sort == "price|asc") selected @endif>가격 오름차순</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=50 @if($pageSize == 50) selected @endif>50개 노출</option>
                                            <option value=30 @if($pageSize == 30) selected @endif>30개 노출</option>
                                            <option value=20 @if($pageSize == 20) selected @endif>20개 노출</option>
                                            <option value=10 @if($pageSize == 10) selected @endif>10개 노출</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/product/keywordQuery'" class="btn btn-md btn-light btn-reset">초기화</button>
                                        @if ( $payload )
                                            <div class="mt-3">payload: {{ $payload }}</div>
                                        @endif
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
                                <th scope="col" style="width: 100px">판매량(월)</th>
                                <th scope="col" style="width: 150px" class="text-center">
                                    W 공급가<br>
                                    (환율: {{ number_format($exchangeRate, 2); }}원)
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
                                        {{ number_format(($totalRecords - $offset) - $index) }}
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
                                        <img class="lazy-img preview-image" data-src="{{ $data["imageUrl"] }}" width=60 height=60/>
                                    </td>
                                    <td>
                                        {{ number_format($data["monthSold"]) }}
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

            <div class="d-flex justify-content-center">
                @if ($paginator)
                    {{ $paginator->links("vendor.pagination.bootstrap-4") }}
                @endif
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

            if(confirm(`${offer_ids.length}건의 상품을 수집 하시겠습니까?\n재 수집 시 저장 된 상품의 정보가 초기화 됩니다.`)){
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
                        },
                        search_cls: $("select[name=search_cls]").val(),
                        keyword   : $("#keyword").val(),
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
            let totalRecords     = "{{ $totalRecords }}";
            let formData         = $("#searchFrm").serializeArray();
            let nCollectOption   = $('input[name=nCollectOption]:checked').val();
            let nTranslateOption = $('input[name=nTranslateOption]:checked').val();
            let yCollectOption   = $('input[name=yCollectOption]:checked').val();
            let yTranslateOption = $('input[name=yTranslateOption]:checked').val();

            // formData를 객체로 변환
            let formDataObject = {};
            formData.forEach(item => {
                formDataObject[item.name] = item.value;
            });

            // collectParams 객체 생성
            formDataObject.collectParams = {
                nCollectOption  : nCollectOption,
                nTranslateOption: nTranslateOption,
                yCollectOption  : yCollectOption,
                yTranslateOption: yTranslateOption
            };


            if(totalRecords < 1){
                return alert("검색 된 상품이 없습니다.");
            }
            if(confirm(`${totalRecords}건의 상품을 수집 하시겠습니까?\n재 수집 시 저장 된 상품의 정보가 초기화 됩니다.`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectKeywordQuery') }}",
                    "data"       : formDataObject,
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
