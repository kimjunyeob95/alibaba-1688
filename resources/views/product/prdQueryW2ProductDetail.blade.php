@php
    use App\Constants\ProductConstant;
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
                <li class="breadcrumb-item">W2</li>
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
                                        {{ $data["soldOut"] }}
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
            let offer_ids = [];
            let hasPrd    = false;

            $(".chk-inp:checked").each(function(index, element){
                if( $(this).attr("hasPrd") == "N" ){
                    offer_ids.push($(this).val());
                } else {
                    hasPrd = true;
                }
            });

            if(offer_ids.length < 1 && hasPrd == true){
                return alert("수집 완료 된 상품만 선택했습니다.");
            }else if(offer_ids.length < 1 ){
                return alert("검색 된 상품이 없습니다.");
            }

            if(confirm(`${offer_ids.length}건의 상품을 수집 하시겠습니까?\n재 수집 시 저장 된 상품의 정보가 초기화 됩니다.`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w2.product.collectProduct') }}",
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
            let hasPrd    = false;

            $(".chk-inp").each(function(index, element){
                if( $(this).attr("hasPrd") == "N" ){
                    offer_ids.push($(this).val());
                } else {
                    hasPrd = true;
                }
            });

            if(offer_ids.length < 1 && hasPrd == true){
                return alert("수집 완료 된 상품만 선택했습니다.");
            }else if(offer_ids.length < 1 ){
                return alert("검색 된 상품이 없습니다.");
            }

            if(confirm(`${offer_ids.length}건의 상품을 수집 하시겠습니까?\n재 수집 시 저장 된 상품의 정보가 초기화 됩니다.`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w2.product.collectProduct') }}",
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
