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
                <li class="breadcrumb-item">상품 수집 관리</li>
                <li class="breadcrumb-item active" aria-current="page">상품상세 URL로 수집</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">
                <form id="searchFrm">
                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">상품상세 URL 검색</th>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) https://detail.1688.com/offer/776602958006.html,https://detail.1688.com/offer/737834654023.html">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/product/urlQuery'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-outline-dark me-2" id="btn-select">선택상품 수집</button>
                    <button class="btn btn-md btn-outline-success" id="btn-all">전체상품 수집</button>
                </div>

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
                                    1688 소비자가<br>
                                    옵션가격<br>
                                    온채널가<br>
                                    소비자가
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data["offerId"] }}">
                                    </td>
                                    <td>
                                        {{ count($datas) - $index }}
                                    </td>
                                    <td>
                                        <a href="https://detail.1688.com/offer/{{ $data["offerId"] }}.html" target="_blank">{{ $data["offerId"] }}</a>
                                    </td>
                                    <td>
                                        {{ $data["subject"] }}
                                    </td>
                                    <td>
                                        {{ $data["subjectTrans"] }}
                                    </td>
                                    <td>
                                        @if (count($data["productImage"]["images"]) > 4)
                                            <img class="lazy-img preview-image" data-src="{{ $data["productImage"]["images"][4] }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src="{{ $data["productImage"]["images"][0] }}" width=60 height=60/>
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($data["soldOut"]))
                                            {{ number_format($data["soldOut"]) }}
                                        @else
                                            데이터 없음
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $data["price_1688"] }}(元)<br>
                                        {{ number_format($data["option_price"]) }}(원)<br>
                                        {{ number_format($data["onch_price"]) }}(원)<br>
                                        {{ number_format($data["cus_price"]) }}(원)<br>
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
            let keyword = $("#keyword").val();
            keyword = fn_split(keyword);
            if( keyword.length > 20 ){
                return alert("최대 검색 수는 20개를 초과할 수 없습니다.");
            }

            $("#searchFrm").submit();
        });

        $("#btn-select").click(function(){
            let offer_ids = [];

            $(".chk-inp:checked").each(function(index, element){
                offer_ids.push($(this).val());
            });

            if(offer_ids.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            if(confirm(`${offer_ids.length}건의 상품을 수집 하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectProductUrl') }}",
                    "data"       : { offer_ids },
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

            $(".chk-inp").each(function(index, element){
                offer_ids.push($(this).val());
            });

            if(offer_ids.length < 1){
                return alert("검색 된 상품이 없습니다.");
            }

            if(confirm(`${offer_ids.length}건의 상품을 수집 하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectProductUrl') }}",
                    "data"       : { offer_ids },
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
