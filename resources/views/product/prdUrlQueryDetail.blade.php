@php
    use App\Constants\ProductConstant;
    use App\Constants\LogConstant;
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
                <li class="breadcrumb-item">
                    <a href="/">상품 수집 관리</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="/product/urlQuery">상품상세 URL로 수집</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">상세보기</li>
            </ol>
        </nav>
        
        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">
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
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                    <div class="card h-100" onclick="toggleCheckbox(event, this)">
                                        <div class="position-relative">
                                            <img src="{{ $detail->prd_image }}" class="card-img-top" alt="이미지 설명">
                                            <div class="position-absolute" style="top: 10px; left: 10px;">
                                                <input type="checkbox" class="form-check-input chk-inp" value="{{ $detail->offer_id }}">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                <a href="https://detail.1688.com/offer/{{ $detail->offer_id }}.html" target="_blank">
                                                    offerID: {{ $detail->offer_id }}
                                                </a>
                                            </p>
                                            <p class="card-text">{{ $detail->prd_name_trans }}</p>
                                            <p class="card-text">판매량: {{ number_format($detail->sold_out) }}</p>
                                            <p class="card-text">가격: {{ number_format($detail->price_1688) }}(元)</p>
                                        </div>
                                    </div>
                                </div>
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
                    "url"        : "{{ route('w.product.collectProduct') }}",
                    "data"       : { offer_ids, log_type },
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
                    "url"        : "{{ route('w.product.collectProduct') }}",
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
    });
</script>

@endsection
