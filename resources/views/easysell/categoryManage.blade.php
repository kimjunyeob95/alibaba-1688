@php
    use App\Constants\ProductConstant;
@endphp
@extends('dashboard.base')

@section('styles')
<style>

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
                <li class="breadcrumb-item">이지셀</li>
                <li class="breadcrumb-item active" aria-current="page">카테고리 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">맵핑여부</th>
                                    <td colspan="3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_all" value="" checked>
                                            <label class="form-check-label" for="mapping_status_all">전체</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_y" value="Y" {{ $mapping_status == 'Y' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mapping_status_y">매칭 완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_n" value="N" {{ $mapping_status == 'N' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mapping_status_n">매칭 필요</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">키워드</th>
                                    <td colspan="3">
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="검색어를 입력하세요.">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">카테고리</th>
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_first" level=1>
                                                    <option value="" hidden>1차 분류</option>
                                                    @foreach($cateFirstList as $cate)
                                                    <option value="{{ $cate }}" {{ $cate_first == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_second" level=2>
                                                    <option value="" hidden>2차 분류</option>
                                                    @foreach($cateSecondList as $cate)
                                                    <option value="{{ $cate }}" {{ $cate_second == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_third" level=3>
                                                    <option value="" hidden>3차 분류</option>
                                                    @foreach($cateThirdList as $cate)
                                                    <option value="{{ $cate }}" {{ $cate_third == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_fourth" level=4>
                                                    <option value="" hidden>4차 분류</option>
                                                    @foreach($cateFourthList as $cate)
                                                    <option value="{{ $cate }}" {{ $cate_fourth == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/easySell/category'" class="btn btn-md btn-light btn-reset">초기화</button>
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
                                <th scope="col" width="45%">W.app 카테고리</th>
                                <th scope="col" width="45%">이지셀 카테고리</th>
                                <th scope="col" width="10%" class="text-center">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($datas as $data)
                                <tr>
                                    <td>
                                        @if(!empty($data->cate_first))
                                            {{ $data->cate_first }}
                                        @endif
                                        @if(!empty($data->cate_second))
                                            > {{ $data->cate_second }}
                                        @endif
                                        @if(!empty($data->cate_third))
                                            > {{ $data->cate_third }}
                                        @endif
                                        @if(!empty($data->cate_fourth))
                                            > {{ $data->cate_fourth }}
                                        @endif
                                    </td>
                                    <td>
                                        @empty($data->es_mapping_code)
                                            <span class="text-danger">
                                                {{ ProductConstant::MAPPING_STATUS[ProductConstant::MAPPING_STATUS_N] }}
                                            </span>
                                        @else
                                            @isset($data->es_category)
                                                {{ $data->es_category->sellerhub_cate_nm }}
                                            @endisset
                                        @endempty
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-modal" cateid="">맵핑하기</button>
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

        <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel">매칭 카테고리 : <span></span></h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="chkCateIds[]" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">카테고리 매칭하기</label>
                            </div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100 cate-1688-list">
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">키워드</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="cate_keyword" placeholder="검색어를 입력하세요." value="">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col">
                                        <label class="fs-7">카테고리</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="cate_first" level="1">
                                            <option value="">1차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="cate_second" level="2">
                                            <option value="">2차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="cate_third" level="3">
                                            <option value="">3차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-es" name="cate_fourth" level="4">
                                            <option value="">4차 분류</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="text-left">
                                <button type="button" class="btn btn-primary btn-cate-search">검색</button>
                            </div>
                            <hr>

                            <table class="table table-white bg-white w-cate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 200px">1차 카테고리</th>
                                        <th scope="col" style="width: 200px">2차 카테고리</th>
                                        <th scope="col" style="width: 200px">3차 카테고리</th>
                                        <th scope="col" style="width: 200px">4차 카테고리</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary">확인</button>
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $('.select-opt').change(function(){
            let selectedLevel = parseInt($(this).attr('level'));
            let categoryNm   = $(this).val();

            if( selectedLevel < 4 ){
                var cateFirst = "";
                $('.select-opt').each(function(idx) {
                    if(idx == 0){
                        cateFirst = $(this).val();
                    }
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).html(`<option value="" hidden>${level}차 분류</option>`);
                    }
                });

                if( categoryNm != "" ){
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"        : "{{ route('easySell.category.categoryDepth') }}",
                        "data"       : {
                            "cateFirst" : cateFirst,
                            "categoryNm": categoryNm,
                            "level"     : selectedLevel
                        },
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete  : function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $.each(resp.categoryList, function(idx,value) {
                                $(`.select-opt[level=${selectedLevel+1}]`).append(`<option value="${value}">${value}</option>`)
                            });
                        },
                        error: function (request) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        });

        $(".btn-modal").click(function(){
            let cateId = $(this).attr("cateid");
            $("#loadingOverlay").show();

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('w.category.getInfos') }}",
                "data"    : { categoryIds: [cateId] },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(`.cate-1688-list`).html("");
                    let category_ids = [];
                    resp.data.cateResult.map(function(obj){
                        category_ids.push(obj.category_id);
                        $(`.cate-1688-list`).append(`<p>- ${obj.cate_name}</p>`)
                    });
                    $('input[name="chkCateIds[]"]').val(category_ids);

                    $(`.select-opt-es[level=1]`).html(`<option value="">1차 분류</option>`);
                    resp.data.wCateDepth1.map(function(obj){
                        $(`.select-opt-es[level=1]`).append(`<option value="${obj.cate_first}">${obj.cate_first}</option>`)
                    });
                    $("#htmlModal").modal('show');
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });
    });
</script>

@endsection
