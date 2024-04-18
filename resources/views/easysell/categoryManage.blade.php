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
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_y" value="{{ ProductConstant::TRANS_STATUS_Y }}" {{ $mapping_status == ProductConstant::TRANS_STATUS_Y ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mapping_status_y">전체</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_y" value="{{ ProductConstant::TRANS_STATUS_Y }}" {{ $mapping_status == ProductConstant::TRANS_STATUS_Y ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mapping_status_y">매칭 완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_n" value="{{ ProductConstant::TRANS_STATUS_N }}" {{ $mapping_status == ProductConstant::TRANS_STATUS_N ? 'checked' : '' }}>
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
                                                    <option value="">1차 분류</option>
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_second" level=2>
                                                    <option value="">2차 분류</option>
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_third" level=3>
                                                    <option value="">3차 분류</option>
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_fourth" level=4>
                                                    <option value="">4차 분류</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/category'" class="btn btn-md btn-light btn-reset">초기화</button>
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
                                <th scope="col">W.app 카테고리</th>
                                <th scope="col">이지셀 카테고리</th>
                                <th style="width: 150px" class="text-center">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($datas as $data)
                                <tr>
                                    <td>
                                        ({{ $data->mapping_code }})
                                        @if(!empty($data->w_cate_name->cate_first))
                                            {{ $data->w_cate_name->cate_first }}
                                        @endif
                                        @if(!empty($data->w_cate_name->cate_second))
                                            > {{ $data->w_cate_name->cate_second }}
                                        @endif
                                        @if(!empty($data->w_cate_name->cate_third))
                                            > {{ $data->w_cate_name->cate_third }}
                                        @endif
                                        @if(!empty($data->w_cate_name->cate_fourth))
                                            > {{ $data->w_cate_name->cate_fourth }}
                                        @endif
                                    </td>
                                    <td>
                                        @empty($data->es_mapping_code)
                                            <p class="text-danger">
                                                {{ ProductConstant::MAPPING_STATUS[ProductConstant::MAPPING_STATUS_N] }}
                                            </p>
                                        @else
                                            ({{ $data->es_mapping_code }})
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
            </div>
        </div>

        <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel">카테고리 맵핑</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="chkCateIds[]" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">W 카테고리</label>
                            </div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100 cate-1688-list">
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col">
                                        <label class="fs-7">WApp 카테고리</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_first" level="1">
                                            <option value="">1차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_second" level="2">
                                            <option value="">2차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_third" level="3">
                                            <option value="">3차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_fourth" level="4">
                                            <option value="">4차 분류</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">WApp 카테고리 키워드</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="w_cate_keyword" placeholder="검색어를 입력하세요." value="">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="text-left">
                                <button type="button" class="btn btn-primary btn-w-cate-search">검색</button>
                            </div>
                            <hr>

                            <table class="table table-white bg-white w-cate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 200px">WApp 1차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 2차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 3차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 4차 카테고리</th>
                                        <th scope="col" style="width: 200px">맵핑 코드</th>
                                        <th scope="col" style="width: 100px">맵핑</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $("#btn-select").click(function(){
            let cateIds = [];

            $(".chk-inp:checked").each(function(index, element){
                cateIds.push($(this).val());
            });

            if(cateIds.length < 1){
                return alert("선택 된 카테고리가 없습니다.");
            }

            $("#loadingOverlay").show();
            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('w.category.getInfos') }}",
                "data"    : { categoryIds: cateIds },
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

                    $(`.select-opt-w[level=1]`).html(`<option value="">1차 분류</option>`);
                    resp.data.wCateDepth1.map(function(obj){
                        $(`.select-opt-w[level=1]`).append(`<option value="${obj.cate_first}">${obj.cate_first}</option>`)
                    });
                    $("#htmlModal").modal('show');
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });

        });

        $(document).on('click', '.btn-save', function(){
            let category_ids = $('input[name="chkCateIds[]"]').val();
            let w_cate_id    = Number($(this).attr("value"));

            $("#loadingOverlay").show();
            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "{{ route('w.category.wMapping') }}",
                "data"   : {
                    category_ids,
                    w_cate_id
                },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    alert(resp.msg);
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".btn-w-cate-search").click(function(){
            let cate_first = $("select[name=w_cate_first]").val();
            if( cate_first == "" ){
                return alert("1차 분류를 선택하세요.");
            }
            let cate_second    = $("select[name=w_cate_second]").val();
            let cate_third     = $("select[name=w_cate_third]").val();
            let cate_fourth    = $("select[name=w_cate_fourth]").val();
            let w_cate_keyword = $("input[name=w_cate_keyword]").val();

            $("#loadingOverlay").show();

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('w.category.getW') }}",
                "data"    : {
                    cate_first,
                    cate_second,
                    cate_third,
                    cate_fourth,
                    w_cate_keyword,
                },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(".w-cate-table tbody").html("");

                    resp.data.map(function(obj){
                        $(`.w-cate-table tbody`).append(`
                            <tr>
                                <td>
                                    ${obj.cate_first}
                                </td>
                                <td>
                                    ${obj.cate_second}
                                </td>
                                <td>
                                    ${obj.cate_third}
                                </td>
                                <td>
                                    ${obj.cate_fourth}
                                </td>
                                <td>
                                    ${obj.mapping_code}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-save" value="${obj.id}">적용</button>
                                </td>
                            </tr
                        `);
                    });
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });

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

                    $(`.select-opt-w[level=1]`).html(`<option value="">1차 분류</option>`);
                    resp.data.wCateDepth1.map(function(obj){
                        $(`.select-opt-w[level=1]`).append(`<option value="${obj.cate_first}">${obj.cate_first}</option>`)
                    });
                    $("#htmlModal").modal('show');
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $('.select-opt-w').change(function(){
            let selectedLevel = parseInt($(this).attr('level'));

            if( selectedLevel < 4 ){
                let cate_name = "";
                $('.select-opt-w').each(function(key, ele) {
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).html(`<option value="">${level}차 분류</option>`);
                    }
                    if (selectedLevel >= level) {
                        if( key == 0 ){
                            cate_name = $(this).val();
                        }else{
                            cate_name += "," + $(this).val();
                        }
                    }
                });

                if( cate_name != "" ){
                    $("#loadingOverlay").show();
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"     : "{{ route('w.category.getWDepth') }}",
                        "data"    : {
                            level    : selectedLevel,
                            cate_name: cate_name,
                        },
                        beforeSend: function () {},
                        complete  : function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $(`.select-opt-w[level=${selectedLevel+1}]`).html(`<option value="">${selectedLevel+1}차 분류</option>`);
                            if( selectedLevel == 1 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_second ){
                                        $(`.select-opt-w[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_second}">${obj.cate_second}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 2 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_third ){
                                        $(`.select-opt-w[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_third}">${obj.cate_third}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 3 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_fourth ){
                                        $(`.select-opt-w[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_fourth}">${obj.cate_fourth}</option>`)
                                    }
                                })
                            }
                        },
                        error: function (request) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $('.select-opt').change(function(){
            let selectedLevel = parseInt($(this).attr('level'));
            let category_id   = $(this).val();

            if( selectedLevel < 3 ){
                $('.select-opt').each(function() {
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).html(`<option value="">${level}차 분류</option>`);
                    }
                });

                if( category_id != "" ){
                    $("#loadingOverlay").show();
                    category_id = parseInt($(this).val());
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type" : "GET",
                        "url" : `/api/w/category/depth/${category_id}`,
                        beforeSend : function () {},
                        complete: function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $(`.select-opt[level=${selectedLevel+1}]`).html(`<option value="">${selectedLevel+1}차 분류</option>`);
                            resp.data.map(function(obj){
                                $(`.select-opt[level=${selectedLevel+1}]`).append(`<option value="${obj.category_id}">${obj.category_name}</option>`)
                            })
                        },
                        error: function (request) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        });

        $(".btn-detail").click(function(){
            return alert("준비중...");
        });

    });
</script>

@endsection
