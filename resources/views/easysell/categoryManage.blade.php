@php
    use App\Constants\ProductConstant;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
.cate-table tbody {
  display: block;
  max-height: 350px;
  overflow-y: auto;
}

.cate-table thead,
.cate-table tbody tr {
  display: table;
  width: 100%;
}

.cate-table thead tr td,
.cate-table tbody tr td{
    width: 25%;
}

.cate-table tbody tr{
    cursor: pointer;
}

input[name='channelCategory']{
    visibility: hidden;
}
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
                <li class="breadcrumb-item">채널 관리</li>
                <li class="breadcrumb-item">카테고리 관리</li>
                <li class="breadcrumb-item active" aria-current="page">이지셀 카테고리 맵핑</li>
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
                                            <label class="form-check-label" for="mapping_status_y">맵핑 완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_n" value="N" {{ $mapping_status == 'N' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="mapping_status_n">미맵핑</label>
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
                                    <th style="width: 150px">WApp 카테고리</th>
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-2">
                                                <select class="form-control select-opt-w" name="cate_first" level=1>
                                                    <option value="">1차 분류</option>
                                                    @foreach($cateFirstList as $cate)
                                                        <option value="{{ $cate }}" {{ $cate_first == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt-w" name="cate_second" level=2>
                                                    <option value="">2차 분류</option>
                                                    @foreach($cateSecondList as $cate)
                                                        @if ( $cate )
                                                            <option value="{{ $cate }}" {{ $cate_second == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt-w" name="cate_third" level=3>
                                                    <option value="">3차 분류</option>
                                                    @foreach($cateThirdList as $cate)
                                                        @if ( $cate )
                                                            <option value="{{ $cate }}" {{ $cate_third == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt-w" name="cate_fourth" level=4>
                                                    <option value="">4차 분류</option>
                                                    @foreach($cateFourthList as $cate)
                                                        @if ( $cate )
                                                            <option value="{{ $cate }}" {{ $cate_fourth == $cate ? 'selected' : '' }}>{{ $cate }}</option>
                                                        @endif
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
                                <th scope="col" width="45%">WApp 카테고리</th>
                                <th scope="col" width="45%">이지셀 카테고리</th>
                                <th scope="col" width="10%" class="text-center">관리</th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($datas as $data)
                                @php
                                    $wCateName = "";
                                    if(!empty($data->cate_first)){
                                        $wCateName .= $data->cate_first;
                                    }
                                    if(!empty($data->cate_second)){
                                        $wCateName .= " > " . $data->cate_second;
                                    }
                                    if(!empty($data->cate_third)){
                                        $wCateName .= " > " . $data->cate_third;
                                    }
                                    if(!empty($data->cate_fourth)){
                                        $wCateName .= " > " . $data->cate_fourth;
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        {{ $wCateName }}
                                    </td>
                                    <td>
                                        @empty($data->es_mapping_code)
                                            <span class="text-danger">
                                                {{ ProductConstant::MAPPING_STATUS[ProductConstant::MAPPING_STATUS_N] }}
                                            </span>
                                        @else
                                            @isset($data->es_category)
                                                @if(!empty($data->es_category->cate_first))
                                                    {{ $data->es_category->cate_first }}
                                                @endif
                                                @if(!empty($data->es_category->cate_second))
                                                    > {{ $data->es_category->cate_second }}
                                                @endif
                                                @if(!empty($data->es_category->cate_third))
                                                    > {{ $data->es_category->cate_third }}
                                                @endif
                                                @if(!empty($data->es_category->cate_fourth))
                                                    > {{ $data->es_category->cate_fourth }}
                                                @endif
                                            @endisset
                                        @endempty
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-modal" cateid="{{$data->category_id}}" catename="{{ $wCateName }}">맵핑하기</button>
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
                        <h5 class="modal-title" id="htmlModalLabel">WApp 맵핑 카테고리 : <span class="mapping-cate-nm"></span></h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="cateId" />

                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">이지셀 카테고리 맵핑하기</label>
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
                                        <label class="fs-7">이지셀 카테고리</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_first" level="1">
                                            <option value="">1차 분류</option>
                                            @foreach($channelCateFirstList as $cate)
                                                <option value="{{ $cate }}">{{ $cate }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_second" level="2">
                                            <option value="">2차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_third" level="3">
                                            <option value="">3차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-channel" name="channel_cate_fourth" level="4">
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

                            <table class="table table-white bg-white cate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 200px">1차 카테고리</th>
                                        <th scope="col" style="width: 200px">2차 카테고리</th>
                                        <th scope="col" style="width: 200px">3차 카테고리</th>
                                        <th scope="col" style="width: 200px">4차 카테고리</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($channelCateList as $channelCate)
                                        <tr>
                                            <td>
                                                <input type='radio' name='channelCategory' value='{{ $channelCate->sellerhub_cate }}'>
                                                {{ $channelCate->cate_first }}
                                            </td>
                                            <td>
                                                {{ $channelCate->cate_second }}
                                            </td>
                                            <td>
                                                {{ $channelCate->cate_third }}
                                            </td>
                                            <td>
                                                {{ $channelCate->cate_fourth }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save">확인</button>
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function(){
        var channelCateList = '{!! json_encode($channelCateList) !!}';
        channelCateList = JSON.parse(channelCateList);

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $('.select-opt-w').on('change', function() {
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
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"     : "{{ route('w.category.getWDepth') }}",
                        "data"    : {
                            level     : selectedLevel,
                            cate_name : cate_name,
                        },
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
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

        $('.select-opt-channel').on('change', function() {
            let selectedLevel = parseInt($(this).attr('level'));

            if( selectedLevel < 4 ){
                let cate_name = "";
                $('.select-opt-channel').each(function(key, ele) {
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
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"     : "/api/mall/easySell/category/depth",
                        "data"    : {
                            level     : selectedLevel,
                            cate_name : cate_name,
                        },
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete  : function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $(`.select-opt-channel[level=${selectedLevel+1}]`).html(`<option value="">${selectedLevel+1}차 분류</option>`);
                            if( selectedLevel == 1 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_second ){
                                        $(`.select-opt-channel[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_second}">${obj.cate_second}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 2 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_third ){
                                        $(`.select-opt-channel[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_third}">${obj.cate_third}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 3 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_fourth ){
                                        $(`.select-opt-channel[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_fourth}">${obj.cate_fourth}</option>`)
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

        $(".btn-modal").click(function(){
            let cateId   = $(this).attr("cateid");
            let cateName = $(this).attr("catename");

            $("#htmlModal").find("select").val("");
            $("input[name='cate_keyword']").val("");
            $(".cate-table tbody").html("");

            $("input[name='cateId']").val(cateId);
            $(`.mapping-cate-nm`).text(cateName);

            channelCateList.map(function(obj){
                $(`.cate-table tbody`).append(`
                    <tr>
                        <td>
                            <input type='radio' name='channelCategory' value='${obj.sellerhub_cate}'>
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
                    </tr>
                `);
            });

            $("#htmlModal").modal('show');
        });

        $('.cate-table').on('click', 'tr', function() {
            $(this).find('input[type="radio"]').prop('checked', true);

            $('.cate-table tr').removeClass('bg-secondary');
            $(this).addClass("bg-secondary");
        });

        $(".btn-cate-search").click(function(){
            var keyword     = $("input[name='cate_keyword']").val();
            var cate_first  = $("select[name='channel_cate_first']").val();
            var cate_second = $("select[name='channel_cate_second']").val();
            var cate_third  = $("select[name='channel_cate_third']").val();
            var cate_fourth = $("select[name='channel_cate_fourth']").val();

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "/api/mall/easySell/category/list",
                "data"   : {
                    "keyword"    : keyword,
                    "cate_first" : cate_first,
                    "cate_second": cate_second,
                    "cate_third" : cate_third,
                    "cate_fourth": cate_fourth,
                },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(".cate-table tbody").html("");
                    resp.data.map(function(obj){
                        $(`.cate-table tbody`).append(`
                            <tr>
                                <td>
                                    <input type='radio' name='channelCategory' value='${obj.sellerhub_cate}'>
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
                            </tr>
                        `);
                    });
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $(".btn-save").click(function(){
            var categoryId      = $("input[name='cateId']").val();
            var channelCateCode = $("input[name='channelCategory']:checked").val();

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "/api/mall/easySell/category/mapping",
                "data"   : {
                    "categoryId"     : categoryId,
                    "channelCateCode": channelCateCode,
                },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    alert(resp.msg);
                    location.reload();
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        })
    });
</script>

@endsection
