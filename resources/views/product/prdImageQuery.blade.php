@php
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
                <li class="breadcrumb-item">상품 수집 관리</li>
                <li class="breadcrumb-item active" aria-current="page">상품 Image로 수집</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">
                <form id="searchFrm">
                    <input type="hidden" id="imageId" name="imageId" value={{ $imageId }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">Image 등록</th>
                                    <td colspan="2">
                                        <input type="file" class="form-control" id="imgFile" accept="image/*">
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">Image ID</th>
                                    <td colspan="2">
                                        <span id="span-imgId">{{ $imageId }}</span>
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
                                        <button type="button" onclick="location.href='/product/imageQuery'" class="btn btn-md btn-light btn-reset">초기화</button>
                                        @if ( $payload )
                                            <div class="mt-3">payload: {{ $payload }}</div>
                                        @endif
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
                                <th scope="col" style="width: 150px">제품ID</th>
                                <th scope="col">제품명</th>
                                <th scope="col">제품명(번역)</th>
                                <th scope="col" style="width: 100px">원본이미지</th>
                                <th scope="col" style="width: 100px">판매량(월)</th>
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
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data["offerId"] }}">
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
        $("#form-submit").click(function(){
            let imageId = $("#imageId").val();
            if( !imageId ){
                return alert("파일을 먼저 등록해주세요.");
            }

            $("#searchFrm").submit();
        });

        $("#imgFile").change(function(){
            $("#loadingOverlay").show();

            let file = this.files[0];

            // 파일 타입 검사 (이미지 파일인지 확인)
            if (file.type.indexOf('image') == -1) {
                this.value = '';
                $("#imageId").val('');
                $("#loadingOverlay").hide();
                return alert('이미지 파일만 업로드 가능합니다.');
            } else if (file.size > 300000) { // 파일 크기 검사 (300KB 이하인지 확인)
                this.value = '';
                $("#imageId").val('');
                $("#loadingOverlay").hide();
                return alert('파일 크기는 300KB 이하로 업로드 가능합니다.');
            }

            let formData = new FormData();
            formData.append('imgFile[]', file);

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type" : "POST",
                "url" : "{{ route('w.product.createImgId') }}",
                "data" : formData,
                "processData": false,
                "contentType": false,
                beforeSend : function () {},
                complete: function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $("#imageId").val(resp.result);
                    $("#span-imgId").text(resp.result);
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
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
                    "url"        : "{{ route('w.product.collectProductImage') }}",
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
            let totalRecords = "{{ $totalRecords }}";
            let imageId      = "{{ $imageId }}";

            if( totalRecords < 1 ){
                return alert("수집 할 상품이 없습니다.");
            }
            if( !imageId ){
                return alert("이미지 ID가 없습니다. 이미지를 등록하세요.");
            }

            if(confirm(`${totalRecords}건의 상품을 수집 하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectImageQuery') }}",
                    "data"       : {
                        "imageIds": imageId,
                        "sort"    : $("select[name=sort]").val(),
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
