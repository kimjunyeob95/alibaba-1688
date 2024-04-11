@php
    use App\Constants\ProductConstant;
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
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) https://detail.1688.com/offer/776602958006.html,https://detail.1688.com/offer/737834654023.html"></textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">제목</th>
                                    <td>
                                        <input type="text" class="form-control" id="search_title" name="search_title" placeholder="요청자 또는 수집 상품의 내용을 작성해 주세요." value="">
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">상품 조회 요청</button>
                                        <button type="button" onclick="location.href='/product/urlQuery'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-outline-danger" id="btn-all-del">선택 삭제</button>
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col">제목</th>
                                <th scope="col" style="width: 100px">요청건수</th>
                                <th scope="col" style="width: 100px">완료건수</th>
                                <th scope="col" style="width: 100px">진행상태</th>
                                <th scope="col" style="width: 200px">요청일자</th>
                                <th scope="col" style="width: 200px">완료일자</th>
                                <th scope="col" class="text-center" style="width: 150px">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->id }}">
                                    </td>
                                    <td>
                                        {{ count($datas) - $index }}
                                    </td>
                                    <td>
                                        {{ $data->search_title }}
                                    </td>
                                    <td>
                                        {{ $data->search_count }}
                                    </td>
                                    <td>
                                        {{ $data->details_y_cnt() }}
                                    </td>
                                    <td>
                                        {{ ProductConstant::SEARCH_STATUS[$data->status] }}
                                    </td>
                                    <td>
                                        {{ $data->created_at }}
                                    </td>
                                    <td>
                                        {{ $data->completed_at }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-detail" searchid={{ $data->id }}>상세</button>
                                        <button class="btn btn-sm btn-outline-danger btn-del" searchid={{ $data->id }}>삭제</button>
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
        $("#form-submit").click(function(){
            let keyword      = $("#keyword").val();
            let search_title = $("#search_title").val();
            
            if( keyword.trim() == "" ) {
                return alert("상세 Url을 입력하세요.");
            }
            if( search_title.trim() == "" ) {
                return alert("제목을 입력하세요.");
            }

            let formData = $("#searchFrm").serialize();
            if(confirm(`상품 조회 요청을 하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.productSearchData') }}",
                    "data"       : formData,
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

        $(".btn-detail").click(function(){
            let searchid = $(this).attr("searchid");
            location.href = `/product/urlQuery/log/${searchid}`;
        });

        $("#btn-all-del").click(function(){
            let ids = [];

            $(".chk-inp").each(function(index, element){
                ids.push($(this).val());
            });

            if(ids.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            return alert("준비중..");

            if(confirm(`${ids.length}건의 요청을 삭제 하시겠습니까?`)){
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

        $(".btn-del").click(function(){
            let ids = [$(this).val()];

            if(ids.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            return alert("준비중..");

            if(confirm(`해당 요청을 삭제 하시겠습니까?`)){
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
