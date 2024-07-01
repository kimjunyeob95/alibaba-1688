@php
    use App\Constants\ProductConstant;
    use App\Constants\CategoryConstant;
    use App\Constants\MallConstant;
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
                <li class="breadcrumb-item">W App</li>
                <li class="breadcrumb-item">카테고리 관리</li>
                <li class="breadcrumb-item active" aria-current="page">전송 카테고리 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="send_type" value={{ $send_type }}>
                    <input type="hidden" name="is_regist" value={{ $is_regist }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">전송 채널</th>
                                    <td colspan="3">
                                        <button type="button" name="send_type" class="btn-status btn btn-md {{ $send_type == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        @foreach (MallConstant::SEND_CHANNEL_LIST as $mallKey => $mallChannel)
                                            <button type="button" name="send_type" class="btn-status btn btn-md {{ $send_type == $mallKey ? "btn-primary" : "btn-dark" }}"
                                            value="{{ $mallKey }}">{{ $mallChannel }}</button>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">전송 여부</th>
                                    <td colspan="3">
                                        <button type="button" name="is_regist" class="btn-status btn btn-md {{ $is_regist == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="is_regist" class="btn-status btn btn-md {{ $is_regist == MallConstant::REGIST_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ MallConstant::REGIST_Y }}">전송</button>
                                        <button type="button" name="is_regist" class="btn-status btn btn-md {{ $is_regist == MallConstant::REGIST_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ MallConstant::REGIST_N }}">전송제외</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">키워드</th>
                                    <td colspan="3">
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="검색어를 입력하세요.">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">W 카테고리</th>
                                    <td colspan="3">
                                        <div class="row">
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_first" level=1>
                                                    <option value="">1차 분류</option>
                                                    @foreach ($firstCateObjs as $firstCateObj)
                                                        <option value="{{ $firstCateObj->category_id }}" {{ $firstCateObj->category_id == $cate_first ? "selected" : "" }}>{{ $firstCateObj->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_second" level=2>
                                                    <option value="">2차 분류</option>
                                                    @foreach ($secondCateObjs as $secondCateObj)
                                                        <option value="{{ $secondCateObj->category_id }}" {{ $secondCateObj->category_id == $cate_second ? "selected" : "" }}>{{ $secondCateObj->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <select class="form-control select-opt" name="cate_third" level=3>
                                                    <option value="">3차 분류</option>
                                                    @foreach ($thirdCateObjs as $thirdCateObj)
                                                        <option value="{{ $thirdCateObj->category_id }}" {{ $thirdCateObj->category_id == $cate_third ? "selected" : "" }}>{{ $thirdCateObj->category_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                            <option value=300 @if($pageSize == 300) selected @endif>300개 노출</option>
                                            <option value=500 @if($pageSize == 500) selected @endif>500개 노출</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/category/send/mall'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
    
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-outline-primary me-2" id="btn-select">수정하기</button>
                </div>
                
                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 50px">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 100px">카테고리ID</th>
                                <th scope="col" style="width: 100px">W 1차 분류</th>
                                <th scope="col" style="width: 100px">W 2차 분류</th>
                                <th scope="col" style="width: 100px">W 3차 분류</th>
                                <th scope="col" style="width: 100px">{{ MallConstant::SEND_CHANNEL_LIST[MallConstant::OC_PUBLIC] }}</th>
                                <th scope="col" style="width: 100px">{{ MallConstant::SEND_CHANNEL_LIST[MallConstant::OC_PRIVATE] }}</th>
                                <th scope="col" style="width: 100px">{{ MallConstant::SEND_CHANNEL_LIST[MallConstant::EASYSELL_W] }}</th>
                                <th scope="col" style="width: 100px">{{ MallConstant::SEND_CHANNEL_LIST[MallConstant::EASYSELL_DROPHUB] }}</th>
                                <th style="width: 100px" class="text-center">관리</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input id="checkbox-{{ $data->category_id }}" class="form-check-input chk-inp" type="checkbox" value="{{ $data->category_id }}">
                                    </td>
                                    <td>
                                        <label for="checkbox-{{ $data->category_id }}" class="cursor-pointer">
                                            {{ number_format(($datas->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td>
                                        {{ $data->category_id }}
                                    </td>
                                    <td>
                                        {{ $data->cate_first }}
                                    </td>
                                    <td>
                                        {{ $data->cate_second }}
                                    </td>
                                    <td>
                                        {{ $data->cate_third }}
                                    </td>
                                    <td>
                                        <div class="form-check form-check-lg">
                                            @if ($data->cate_oc_public && $data->cate_oc_public->is_regist == MallConstant::REGIST_Y)
                                                <input class="form-check-input" name="oc_public" checked type="checkbox" value="{{ $data->category_id }}">
                                            @else
                                                <input class="form-check-input" name="oc_public" type="checkbox" value="{{ $data->category_id }}">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-lg">
                                            @if ($data->cate_oc_private && $data->cate_oc_private->is_regist == MallConstant::REGIST_Y)
                                                <input class="form-check-input" name="oc_private" checked type="checkbox" value="{{ $data->category_id }}">
                                            @else
                                                <input class="form-check-input" name="oc_private" type="checkbox" value="{{ $data->category_id }}">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-lg">
                                            @if ($data->cate_es_w && $data->cate_es_w->is_regist == MallConstant::REGIST_Y)
                                                <input class="form-check-input" name="es_w" checked type="checkbox" value="{{ $data->category_id }}">
                                            @else
                                                <input class="form-check-input" name="es_w" type="checkbox" value="{{ $data->category_id }}">
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-lg">
                                            @if ($data->cate_es_drophub && $data->cate_es_drophub->is_regist == MallConstant::REGIST_Y)
                                                <input class="form-check-input" name="es_drophub" checked type="checkbox" value="{{ $data->category_id }}">
                                            @else
                                                <input class="form-check-input" name="es_drophub" type="checkbox" value="{{ $data->category_id }}">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary btn-save" cateid={{ $data->category_id }}>수정하기</button>
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

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });

        $("#btn-select").click(function(){
            let cateParams = [];

            $(".chk-inp:checked").each(function(index, element){
                let cateId    = $(this).val();
                let ocPublic  = $(`input[name=oc_public][value=${cateId}]`).is(":checked");
                let ocPrivate = $(`input[name=oc_private][value=${cateId}]`).is(":checked");
                let esW       = $(`input[name=es_w][value=${cateId}]`).is(":checked");
                let esDropHub = $(`input[name=es_drophub][value=${cateId}]`).is(":checked");

                cateParams.push({
                    "categoryId": cateId,
                    "ocPublic"  : ocPublic,
                    "ocPrivate" : ocPrivate,
                    "esW"       : esW,
                    "esDropHub" : esDropHub,
                });
            });

            if(cateParams.length < 1){
                return alert("선택 된 카테고리가 없습니다.");
            }

            if(confirm("선택 한 카테고리를 수정하시겠습니까?")){
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"        : "{{ route('w.category.sendMallUpdate') }}",
                    "data"    : { cateParams },
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
            };
        });

        $(document).on('click', '.btn-save', function(){
            let cateParams = [];

            let cateId    = $(this).attr('cateid');
            let ocPublic  = $(`input[name=oc_public][value=${cateId}]`).is(":checked");
            let ocPrivate = $(`input[name=oc_private][value=${cateId}]`).is(":checked");
            let esW       = $(`input[name=es_w][value=${cateId}]`).is(":checked");
            let esDropHub = $(`input[name=es_drophub][value=${cateId}]`).is(":checked");

            cateParams.push({
                "categoryId": cateId,
                "ocPublic"  : ocPublic,
                "ocPrivate" : ocPrivate,
                "esW"       : esW,
                "esDropHub" : esDropHub,
            });

            if(confirm("선택 한 카테고리를 수정하시겠습니까?")){
                $.ajax({
                    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"   : "POST",
                    "url"    : "{{ route('w.category.sendMallUpdate') }}",
                    "data"   : { cateParams },
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
            };
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
        
    });
</script>

@endsection
