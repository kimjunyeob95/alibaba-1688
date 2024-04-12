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
                                            <label class="form-check-label" for="mapping_status_y">맵핑 완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="mapping_status" id="mapping_status_n" value="{{ ProductConstant::TRANS_STATUS_N }}" {{ $mapping_status == ProductConstant::TRANS_STATUS_N ? 'checked' : '' }}>
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
                                    <th style="width: 120px">카테고리</th>
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
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 100px">category_id</th>
                                <th scope="col" style="width: 200px">cate_first</th>
                                <th scope="col" style="width: 200px">cate_second</th>
                                <th scope="col" style="width: 200px">cate_third</th>
                                <th scope="col">맵핑 카테고리</th>
                                <th scope="col" style="width: 100px">맵핑여부</th>
                                <th style="width: 100px" class="text-center">관리</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td>
                                        {{ number_format((count($datas) - $index)) }}
                                    </td>
                                    <td>
                                        {{ $data->category_id }}
                                    </td>
                                    <td>
                                        {{ $data->categoryTree->cate_first }}
                                    </td>
                                    <td>
                                        {{ $data->categoryTree->cate_second }}
                                    </td>
                                    <td>
                                        {{ $data->categoryTree->cate_third }}
                                    </td>
                                    <td>
                                        {{ $data->cate_first }}
                                        @if($data->cate_second)
                                        > {{ $data->cate_second }}
                                        @endif
                                        @if($data->cate_third)
                                        > {{ $data->cate_third }}
                                        @endif
                                    </td>
                                    <td>
                                        @if( $data->mapping_code == 0 )
                                            <p class="text-danger">
                                                {{ ProductConstant::MAPPING_STATUS[ProductConstant::MAPPING_STATUS_N] }}
                                            </p>
                                        @else
                                            <p>
                                                {{ ProductConstant::MAPPING_STATUS[ProductConstant::MAPPING_STATUS_Y] }}
                                            </p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-detail" cateid={{ $data->category_id }}>맵핑하기</button>
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

        $('.select-opt').change(function(){
            let selectedLevel = parseInt($(this).attr('level'));
            let category_id   = $(this).val();

            if( selectedLevel < 3 ){
                $('.select-opt').each(function() {
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).val('');
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
