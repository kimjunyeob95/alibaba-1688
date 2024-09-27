@php
    use App\Constants\WmsConstant;
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
                <li class="breadcrumb-item">WMS</li>
                <li class="breadcrumb-item active" aria-current="page">HS code 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            @foreach (WmsConstant::HSCODE_SEARCH_TYPE as $key => $search)
                                                <option value="{{ $key }}" @if($search_cls == $key) selected @endif>{{ $search }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="검색어를 입력해주세요.">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=50 @if($pageSize == 50) selected @endif>50개 노출</option>
                                            <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                            <option value=500 @if($pageSize == 500) selected @endif>500개 노출</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="3">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='{{ url()->current() }}'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>

                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 1%">No</th>
                                <th scope="col" style="width: 5%">성질통합 분류코드명</th>
                                <th scope="col" style="width: 5%">HS code</th>
                                <th scope="col" style="width: 10%">한글 품목명</th>
                                <th scope="col" style="width: 10%">영문 품목명</th>
                                <th scope="col" style="width: 5%">수량단위</th>
                                <th scope="col" style="width: 5%">중량단위</th>
                                <th scope="col" style="width: 5%">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator->items() as $index => $data)
                                <tr>
                                    <td>
                                        <small>{{ number_format(($paginator->total() - $offset) - $index) }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->property_code_name }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->hs_code }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->ko_name }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->en_name }}</small>
                                    </td>
                                    <td>
                                        <small>
                                            @if( !empty($data->unit_code) )
                                                {{ $data->unit_code }}(단위)
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <small>
                                            @if( !empty($data->weight_code) )
                                                {{ $data->weight_code }}
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-sm btn-success btn-api text-white" hs_code={{ $data->hs_code }}>관세율 조회</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <form id="modalFrm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="htmlModalLabel2">관세율 조회</h5>
                            </div>
                            <div class="modal-body">        
                                <div>
                                    <div class="d-flex flex-column mt-3">
                                        <div class="mb-2">
                                            <div class="col">
                                                <label class="d-flex justify-content-center align-items-center w-100 mb-4">
                                                    <span class="fs-5 fw-bold">관세율</span>
                                                </label>
                                            </div>
                                            <div class="col mt-2">
                                                <table class="table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th scope="col" style="width: 1%">No</th>
                                                            <th scope="col" style="width: 5%">관세율 구분코드</th>
                                                            <th scope="col" style="width: 20%">관세율 구분명</th>
                                                            <th scope="col" style="width: 5%">관세율</th>
                                                            <th scope="col" style="width: 5%">단위당 세액</th>
                                                            <th scope="col" style="width: 5%">기준가격</th>
                                                            <th scope="col" style="width: 5%">적용 시작일자</th>
                                                            <th scope="col" style="width: 5%">적용 종료일자</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="modal-tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer d-flex justify-content-center">
                                <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $paginator->links("vendor.pagination.bootstrap-4") }}
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".btn-api").click(function(){
            let hs_code = $(this).attr("hs_code");

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/wms/tariff/${hs_code}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    console.log(resp);

                    let markup   = "";
                    let prutXamt = "";
                    let basePrc  = "";
                    resp.data.map(function(ele, key){
                        let prutXamt = Array.isArray(ele.prutXamt) ? ele.prutXamt.join(',') : ele.prutXamt;
                            prutXamt = prutXamt === '' ? '-' : prutXamt;
                        let basePrc  = Array.isArray(ele.basePrc) ? ele.basePrc.join(',') : ele.basePrc;
                            basePrc  = basePrc  === '' ? '-' : basePrc;

                        markup += `
                            <tr>
                                <td>${key+1}</td>
                                <td>${ele.trrtTpcd}</td>
                                <td>${ele.trrtTpNm}</td>
                                <td>${ele.trrt}</td>
                                <td>${prutXamt}</td>
                                <td>${basePrc}</td>
                                <td>${formatDate(ele.aplyStrtDt)}</td>
                                <td>${formatDate(ele.aplyEndDt)}</td>
                            </tr>
                        `;
                    });

                    $(".modal-tbody").html(markup);
                    $("#htmlModal").modal('show');
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

    });
</script>

@endsection
