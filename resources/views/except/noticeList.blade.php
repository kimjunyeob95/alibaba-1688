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
                                        <button type="button" name="except_type" class="btn-status btn btn-md {{ $except_type == ExceptConstant::IS_EXCEPT_MODI ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ExceptConstant::IS_EXCEPT_MODI }}">수정</button>
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
                    <button class="btn btn-md btn-outline-success me-2" id="btn-select-modi">수정하기</button>
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
                                <th scope="col">유형</th>
                                <th scope="col">Attrubute ID</th>
                                <th scope="col">정보고시 항목명</th>
                                <th scope="col">적용 정보고시 항목명</th>
                                <th class="text-center">관리</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->attribute_id }}" attrname='{{ $data->attribute_names }}'>
                                    </td>
                                    <td>
                                        {{ number_format(($datas->total() - $offset) - $index) }}
                                    </td>
                                    <td>
                                        @if( $data->b_is_except != null && $data->b_is_except == ExceptConstant::IS_EXCEPT_Y )
                                            <span class="text-danger">제외</span>
                                        @elseif ( $data->apply_attribute_name != "" )
                                            <span>수정</span>
                                        @else
                                            <span>적용</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->attribute_id }}
                                    </td>
                                    <td>
                                        {{ $data->attribute_names }}
                                    </td>
                                    <td>
                                        @if( $data->b_is_except != null && $data->b_is_except == ExceptConstant::IS_EXCEPT_Y )
                                            <span class="text-danger">제외</span>
                                        @elseif ( $data->apply_attribute_name != "" )
                                            <span>{{ $data->apply_attribute_name }}</span>
                                        @else
                                            <span>적용</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-modi" dataid={{ $data->attribute_id }} attrname='{{ $data->attribute_names }}'>수정</button>
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

            <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="htmlModalLabel">정보고시 항목명 관리</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="chkIds[]" />
        
                            <div>
                                <div class="d-flex align-items-center">
                                    <label class="fs-7">Attribute IDs:</label>
                                </div>
                                <div class="d-flex justify-content-evenly px-3">
                                    <div class="row w-100 attr-list">
                                    </div>
                                </div>
                                <hr>

                                <div class="d-flex align-items-center">
                                    <label class="fs-7">사용 정보고시 항목명: </label>
                                </div>
                                <div class="d-flex justify-content-evenly px-3">
                                    <div class="row w-100 attr-name-list">
                                    </div>
                                </div>
                                <hr>
    
                                <div class="d-flex justify-content-evenly px-3">
                                    <div class="row w-100">
                                        <div class="col-2">
                                            <label class="fs-7">적용 항목고시 항목명</label>
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" name="apply_attribute_name" placeholder="" value="">
                                            <p></p>
                                            <p class="text-danger">*입력 한 키워드에 공백이 있을 경우, 공백을 포함하여 적용 됩니다.</p>
                                            <p class="text-danger">*빈 값을 입력한 경우 기존 적용 정보고시 항목명은 자동으로 해제되며 기존 항목명으로 업데이트 됩니다.</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-gosi-save">저장</button>
                            <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                        </div>
                    </div>
                </div>
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

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });

        $('.btn-modi').click(function(){
            let ids       = [$(this).attr("dataid")];
            let attrNames = [$(this).attr("attrname")];

            let attrIdsText = "";
            ids.map(function(ele){
                attrIdsText += `- ${ele}<br>`;
            });
            $('.attr-list').html(attrIdsText);

            let attrNamesText = "";
            attrNames.map(function(ele){
                attrNamesText += `- ${ele}<br>`;
            });
            $('.attr-name-list').html(attrNamesText);

            $('input[name="chkIds[]"]').val(ids);
            $("#htmlModal").modal('show');
        });

        $("#btn-select-modi").click(function(){
            let ids = [];
            let attrNames = [];

            $(".chk-inp:checked").each(function(index, element){
                ids.push($(this).val());
                attrNames.push($(this).attr("attrname"));
            });

            if(ids.length < 1){
                return alert("선택 된 항목이 없습니다.");
            }

            let attrIdsText = "";
            ids.map(function(ele){
                attrIdsText += `- ${ele}<br>`;
            });
            $('.attr-list').html(attrIdsText);

            let attrNamesText = "";
            attrNames.map(function(ele){
                attrNamesText += `- ${ele}<br>`;
            });
            $('.attr-name-list').html(attrNamesText);

            $('input[name="chkIds[]"]').val(ids);
            $("#htmlModal").modal('show');
        });

        $('.btn-gosi-save').click(function(){
            let confirmText = "선택 한 항목명으로 적용 하시겠습니까?\nWApp 상품들 고시 항목에는 바로 반영됩니다.";
            let ids = $('input[name="chkIds[]"]').val().split(",");
            let apply_attribute_name = $("input[name=apply_attribute_name]").val() ?? "";

            if( apply_attribute_name == "" ){
                confirmText = confirmText + "\n빈 값으로 입력 시 기존 적용 고시항목명 설정이 해제되며 기존 값으로 저장됩니다.";
            }

            if(ids.length < 1){
                return alert("선택 된 항목이 없습니다.");
            }

            if( confirm(confirmText) ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.noticeNameUpdate') }}",
                    "data"       : { attribute_ids: ids, apply_attribute_name },
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
