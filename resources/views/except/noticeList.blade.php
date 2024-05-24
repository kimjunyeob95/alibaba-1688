@php
    use App\Constants\ExceptConstant;
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
                <li class="breadcrumb-item">제외 관리</li>
                <li class="breadcrumb-item active" aria-current="page">정보고시 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="except_type" value={{ $except_type }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">관리 유형</th>
                                    <td colspan="3">
                                        <button type="button" name="except_type" class="btn-status btn btn-md {{ $except_type == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="except_type" class="btn-status btn btn-md {{ $except_type == ExceptConstant::IS_EXCEPT_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ExceptConstant::IS_EXCEPT_N }}">적용</button>
                                        <button type="button" name="except_type" class="btn-status btn btn-md {{ $except_type == ExceptConstant::IS_EXCEPT_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ExceptConstant::IS_EXCEPT_Y}}">제외</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">키워드 검색</th>
                                    <td colspan="3">
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="검색 할 내용을 입력하세요.">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=50 @if($pageSize == 50) selected @endif>50개 노출</option>
                                            <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                            <option value=150 @if($pageSize == 150) selected @endif>150개 노출</option>
                                            <option value=200 @if($pageSize == 200) selected @endif>200개 노출</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/except/notice/list'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-outline-primary me-2" id="btn-select-save">적용하기</button>
                    <button class="btn btn-md btn-outline-danger me-2" id="btn-select-del">제외하기</button>
                </div>
    
                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 50px">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col">No</th>
                                <th scope="col">Attrubute ID</th>
                                <th scope="col">고시명</th>
                                <th scope="col">고시값</th>
                                <th scope="col">적용여부</th>
                                <th class="text-center">관리</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->attribute_id }}">
                                    </td>
                                    <td>
                                        {{ number_format(($datas->total() - $offset) - $index) }}
                                    </td>
                                    <td>
                                        {{ $data->attribute_id }}
                                    </td>
                                    <td>
                                        {{ $data->attribute_name_kr }}
                                    </td>
                                    <td>
                                        {{ $data->attribute_value_kr }}
                                    </td>
                                    <td>
                                        @if( $data->b_is_except == null || $data->b_is_except == ExceptConstant::IS_EXCEPT_N )
                                        <span>적용</span>
                                        @elseif ( $data->b_is_except == ExceptConstant::IS_EXCEPT_Y )
                                            <span class="text-danger">제외</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary btn-save" dataid={{ $data->attribute_id }}>적용</button>
                                        <button class="btn btn-sm btn-outline-danger btn-del" dataid={{ $data->attribute_id }}>제외</button>
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
        var is_except_y = '{{ ExceptConstant::IS_EXCEPT_Y }}';
        var is_except_n = '{{ ExceptConstant::IS_EXCEPT_N }}';

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });

        $("#btn-select-save").click(function(){
            let ids = [];

            $(".chk-inp:checked").each(function(index, element){
                ids.push($(this).val());
            });

            if(ids.length < 1){
                return alert("선택 된 항목이 없습니다.");
            }

            if( confirm("선택 한 항목을 적용 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.except.noticeUpdate') }}",
                    "data"       : { attribute_ids: ids, is_except: is_except_n },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                        location.reload();
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $("#btn-select-del").click(function(){
            let ids = [];

            $(".chk-inp:checked").each(function(index, element){
                ids.push($(this).val());
            });

            if(ids.length < 1){
                return alert("선택 된 항목이 없습니다.");
            }

            if( confirm("선택 한 항목을 제외 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.except.noticeUpdate') }}",
                    "data"       : { attribute_ids: ids, is_except: is_except_y },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                        location.reload();
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $('.btn-del').click(function(){
            let id = $(this).attr("dataid");

            if( confirm("선택 한 항목을 제외 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.except.noticeUpdate') }}",
                    "data"       : { attribute_ids: [id], is_except: is_except_y },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                        location.reload();
                    },
                    error: function error(request, status, _error) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $('.btn-save').click(function(){
            let id = $(this).attr("dataid");

            if( confirm("선택 한 항목을 적용 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.except.noticeUpdate') }}",
                    "data"       : { attribute_ids: [id], is_except: is_except_n },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
                    complete: function () {
                        $("#loadingOverlay").hide();
                    },
                    success: function (resp) {
                        alert(resp.msg);
                        location.reload();
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
