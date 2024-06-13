@php
    use App\Constants\ProductConstant;
    use App\Constants\CategoryConstant;
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
                <li class="breadcrumb-item active" aria-current="page">표준 중량(배송비) 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="weight_status" value={{ $weight_status }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">표준 배송비</th>
                                    <td colspan="3">
                                        <button type="button" name="weight_status" class="btn-status btn btn-md {{ $weight_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="weight_status" class="btn-status btn btn-md {{ $weight_status == CategoryConstant::WEIGHT_STATUS_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ CategoryConstant::WEIGHT_STATUS_Y }}">완료</button>
                                        <button type="button" name="weight_status" class="btn-status btn btn-md {{ $weight_status == CategoryConstant::WEIGHT_STATUS_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ CategoryConstant::WEIGHT_STATUS_N }}">미완료</button>
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
                                        <button type="button" onclick="location.href='/category/weight/list'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
    
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-outline-success me-2" id="btn-select">설정하기</button>
                    <button class="btn btn-md btn-outline-danger me-2" id="btn-select-remove">삭제하기</button>
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
                                <th scope="col" style="width: 200px">W 1차 분류</th>
                                <th scope="col" style="width: 200px">W 2차 분류</th>
                                <th scope="col" style="width: 200px">W 3차 분류</th>
                                <th scope="col" style="width: 100px">표준 중량(kg)</th>
                                <th scope="col" style="width: 100px">배송비(원)</th>
                                <th style="width: 100px" class="text-center">설정하기</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->category_id }}">
                                    </td>
                                    <td>
                                        {{ number_format(($datas->total() - $offset) - $index) }}
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
                                        @if ($data->weight == null)
                                            <label class="text-danger">미설정</label>
                                        @else
                                            {{ $data->weight }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($data->weight == null)
                                            {{ number_format(ProductConstant::WEIGHT_STATUS_NONE_PRICE) }}
                                        @else
                                            {{ number_format(CategoryConstant::WEIGHTS[$data->weight]) }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-modal" cateid={{ $data->category_id }}>설정하기</button>
                                        <br>
                                        <button class="btn btn-sm btn-outline-danger btn-remove mt-1" cateid={{ $data->category_id }} weight={{ $data->weight }}>삭제하기</button>
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

            <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="htmlModalLabel">표준 중량 (배송비) 설정</h5>
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
                                        <div class="col-2">
                                            <label class="fs-7">표준 중량(kg)</label>
                                        </div>
                                        <div class="col">
                                            <input type="number" class="form-control" name="weight" placeholder="" value="">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-evenly px-3 mt-3">
                                    <div class="row w-100">
                                        <div class="col-2">
                                            <label class="fs-7">배송비(원)</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="delivery_price" placeholder="" value="" disabled>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-save">저장</button>
                            <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    $(document).ready(function(){

        var weightList = '{!! json_encode(CategoryConstant::WEIGHTS) !!}';
        weightList = JSON.parse(weightList);

        const WEIGHT_STATUS_NONE_PRICE = '{{ ProductConstant::WEIGHT_STATUS_NONE_PRICE }}';

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });

        $('input[name=weight]').on('input', function(e) {
            const keyCode = e.originalEvent.inputType;
            if (keyCode === 'deleteContentBackward' || keyCode === 'deleteContentForward') {
                return; // 백스페이스 또는 Delete 키가 눌렸을 경우 input 이벤트 무시
            }

            const value = $(this).val();
            let weightValue = weightList[value];
            if( weightValue == undefined ){
                weightValue = weightList[0];
                $('input[name=weight]').val(0);
                alert("정의되지 않은 중량입니다.");
            }
            weightValue = weightValue.toLocaleString('ko-KR');
            $('input[name=delivery_price]').val(weightValue);
        });

        $("#btn-select").click(function(){
            let cateIds = [];

            $(".chk-inp:checked").each(function(index, element){
                cateIds.push($(this).val());
            });

            if(cateIds.length < 1){
                return alert("선택 된 카테고리가 없습니다.");
            }

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('w.category.getInfos') }}",
                "data"    : { categoryIds: cateIds },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(`.cate-1688-list`).html("");
                    $('input[name=weight]').val("");
                    $('input[name=delivery_price]').val("");

                    let category_ids = [];
                    resp.data.cateResult.map(function(obj){
                        category_ids.push(obj.category_id);
                        $(`.cate-1688-list`).append(`<p>- ${obj.cate_name} / 중량: ${obj.weight.toLocaleString('ko-KR')}(kg) / 배송비: ${obj.delivery_price.toLocaleString('ko-KR')}(원)</p>`)
                    });
                    $('input[name="chkCateIds[]"]').val(category_ids);
                    $("#htmlModal").modal('show');
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
            
        });

        $("#btn-select-remove").click(function(){
            let cateIds     = [];
            let confirmText = "선택 한 카테고리의 표준 중량을 삭제 하시겠습니까?";
            $(".chk-inp:checked").each(function(index, element){
                cateIds.push($(this).val());
            });

            if(cateIds.length < 1){
                return alert("선택 된 카테고리가 없습니다.");
            }

            if(confirm(confirmText)){
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.category.weightRemove') }}",
                    "data"    : { category_ids: cateIds },
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
            }
        });

        $(document).on('click', '.btn-save', function(){
            let category_ids = $('input[name="chkCateIds[]"]').val();
            let weight       = $('input[name=weight]').val();

            if( weight == "" ){
                return alert("표준 중량을 입력하세요.");
            }

            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "{{ route('w.category.weightSave') }}",
                "data"   : { 
                    category_ids,
                    weight
                },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                    $("#htmlModal").modal('hide');
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
        });

        $(document).on('click', '.btn-remove', function(){
            let weight       = $(this).attr("weight");
            let category_ids = [$(this).attr("cateid")];
            let noneWeightPrice = Number(WEIGHT_STATUS_NONE_PRICE).toLocaleString('ko-KR');

            let confirmText = `삭제 시 해당 카테고리의 배송비는 대표 배송비(${noneWeightPrice})가 적용됩니다.\r\n선택 한 카테고리의 표준 중량을 삭제 하시겠습니까?`;
            if( weight == "" ){
                return alert("설정 된 표중 중량이 없습니다.");
            }
            
            if(confirm(confirmText)){
                $.ajax({
                    "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"   : "POST",
                    "url"    : "{{ route('w.category.weightRemove') }}",
                    "data"   : { 
                        category_ids
                    },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
                    complete  : function(xhr, status) {
                        $("#loadingOverlay").hide();
                        $("#htmlModal").modal('hide');
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
            }
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".btn-modal").click(function(){
            let cateId = $(this).attr("cateid");

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('w.category.getInfos') }}",
                "data"    : { categoryIds: [cateId] },
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(`.cate-1688-list`).html("");
                    let category_ids = [];
                    resp.data.cateResult.map(function(obj){
                        category_ids.push(obj.category_id);
                        $(`.cate-1688-list`).append(`<p>- ${obj.cate_name} / 중량: ${obj.weight.toLocaleString('ko-KR')}(kg) / 배송비: ${obj.delivery_price.toLocaleString('ko-KR')}(원)</p>`)
                        if( obj.weight != 0 ){
                            $('input[name=weight]').val(obj.weight);
                            $('input[name=delivery_price]').val(weightList[obj.weight].toLocaleString('ko-KR'));
                        }
                    });
                    $('input[name="chkCateIds[]"]').val(category_ids);

                    $("#htmlModal").modal('show');
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
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
