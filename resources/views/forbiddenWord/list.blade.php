@php
    use App\Constants\ForbiddenWordConstant;
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
                <li class="breadcrumb-item active" aria-current="page">금칙어 관리</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="keyword_type" value={{ $keyword_type }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">관리 유형</th>
                                    <td colspan="3">
                                        <button type="button" name="keyword_type" class="btn-status btn btn-md {{ $keyword_type == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="keyword_type" class="btn-status btn btn-md {{ $keyword_type == ForbiddenWordConstant::KEYWORD_DELETE ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ForbiddenWordConstant::KEYWORD_DELETE }}">키워드 삭제</button>
                                        <button type="button" name="keyword_type" class="btn-status btn btn-md {{ $keyword_type == ForbiddenWordConstant::KEYWORD_REPLACE ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ForbiddenWordConstant::KEYWORD_REPLACE}}">키워드 교체</button>
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
                                        <button type="button" onclick="location.href='/forbiddenWord/list'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-outline-dark me-2" id="btn-reg">등록</button>
                    <button class="btn btn-md btn-outline-danger me-2" id="btn-select-del">선택 삭제</button>
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
                                <th scope="col">대상 키워드</th>
                                <th scope="col">변경 키워드</th>
                                <th scope="col">적용위치</th>
                                <th class="text-center">관리</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->id }}">
                                    </td>
                                    <td>
                                        {{ number_format(($datas->total() - $offset) - $index) }}
                                    </td>
                                    <td>
                                        {{ ForbiddenWordConstant::KEYWORD_STATUS[$data->keyword_type] }}
                                    </td>
                                    <td>
                                        {{ $data->target_keyword }}
                                    </td>
                                    <td>
                                        @if( $data->keyword_type == ForbiddenWordConstant::KEYWORD_DELETE )
                                            <span class="text-danger">{{ ForbiddenWordConstant::KEYWORD_STATUS[$data->keyword_type] }}</span>
                                        @else
                                            <span class="text-danger">{{ $data->replace_keyword }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ ForbiddenWordConstant::KEYWORD_APPLY_STATUS[$data->apply_type] }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-modi" dataid={{ $data->id }}>수정</button>
                                        <button class="btn btn-sm btn-outline-danger btn-del" dataid={{ $data->id }}>삭제</button>
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
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel">키워드 등록</h5>
                    </div>
                    <div class="modal-body">    
                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">관리 유형</label>
                                    </div>
                                    <div class="col d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="keyword_type" id="keyword_type1" value="{{ ForbiddenWordConstant::KEYWORD_DELETE }}" checked>
                                            <label class="form-check-label" for="keyword_type1">{{ ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_DELETE] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="keyword_type" id="keyword_type2" value="{{ ForbiddenWordConstant::KEYWORD_REPLACE }}">
                                            <label class="form-check-label" for="keyword_type2">{{ ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_REPLACE] }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex align-items-center">
                                <label class="fs-7">키워드</label>
                            </div>
                            <div class="px-3 mt-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">대상 키워드</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <input type="text" class="form-control" name="target_keyword" placeholder="대상 키워드를 입력하세요." value="">
                                    </div>
                                </div>
                                <div class="row w-100 mt-3">
                                    <div class="col-2">
                                        <label class="fs-7">교체 키워드</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <input type="text" class="form-control" name="replace_keyword" placeholder="교체 키워드를 입력하세요." value="" disabled>
                                    </div>
                                </div>
                                <p class="text-danger mt-2 fs-7">*입력 한 키워드에 공백이 있을 경우, 공백을 포함하여 적용 됩니다.</p>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">적용위치</label>
                                    </div>
                                    <div class="col d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="apply_type" id="apply_type1" value="{{ ForbiddenWordConstant::KEYWORD_APPLY_ALL }}" checked>
                                            <label class="form-check-label" for="apply_type1">{{ ForbiddenWordConstant::KEYWORD_APPLY_STATUS[ForbiddenWordConstant::KEYWORD_APPLY_ALL] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="apply_type" id="apply_type2" value="{{ ForbiddenWordConstant::KEYWORD_APPLY_TITLE }}">
                                            <label class="form-check-label" for="apply_type2">{{ ForbiddenWordConstant::KEYWORD_APPLY_STATUS[ForbiddenWordConstant::KEYWORD_APPLY_TITLE] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="apply_type" id="apply_type3" value="{{ ForbiddenWordConstant::KEYWORD_APPLY_DESC }}">
                                            <label class="form-check-label" for="apply_type3">{{ ForbiddenWordConstant::KEYWORD_APPLY_STATUS[ForbiddenWordConstant::KEYWORD_APPLY_DESC] }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel2">키워드 수정</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="keyword_id">

                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">관리 유형</label>
                                    </div>
                                    <div class="col d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="keyword_modi_type" id="keyword_modi_type1" value="{{ ForbiddenWordConstant::KEYWORD_DELETE }}">
                                            <label class="form-check-label" for="keyword_modi_type1">{{ ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_DELETE] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="keyword_modi_type" id="keyword_modi_type2" value="{{ ForbiddenWordConstant::KEYWORD_REPLACE }}">
                                            <label class="form-check-label" for="keyword_modi_type2">{{ ForbiddenWordConstant::KEYWORD_STATUS[ForbiddenWordConstant::KEYWORD_REPLACE] }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex align-items-center">
                                <label class="fs-7">키워드</label>
                            </div>
                            <div class="px-3 mt-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">대상 키워드</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <input type="text" class="form-control" name="target_modi_keyword" placeholder="대상 키워드를 입력하세요." value="">
                                    </div>
                                </div>
                                <div class="row w-100 mt-3">
                                    <div class="col-2">
                                        <label class="fs-7">교체 키워드</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <input type="text" class="form-control" name="replace_modi_keyword" placeholder="교체 키워드를 입력하세요." value="" disabled>
                                    </div>
                                </div>
                                <p class="text-danger mt-2 fs-7">*입력 한 키워드에 공백이 있을 경우, 공백을 포함하여 적용 됩니다.</p>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">적용위치</label>
                                    </div>
                                    <div class="col d-flex">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="apply_modi_type" id="apply_modi_type1" value="{{ ForbiddenWordConstant::KEYWORD_APPLY_ALL }}" checked>
                                            <label class="form-check-label" for="apply_modi_type1">{{ ForbiddenWordConstant::KEYWORD_APPLY_STATUS[ForbiddenWordConstant::KEYWORD_APPLY_ALL] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="apply_modi_type" id="apply_modi_type2" value="{{ ForbiddenWordConstant::KEYWORD_APPLY_TITLE }}">
                                            <label class="form-check-label" for="apply_modi_type2">{{ ForbiddenWordConstant::KEYWORD_APPLY_STATUS[ForbiddenWordConstant::KEYWORD_APPLY_TITLE] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="apply_modi_type" id="apply_modi_type3" value="{{ ForbiddenWordConstant::KEYWORD_APPLY_DESC }}">
                                            <label class="form-check-label" for="apply_modi_type3">{{ ForbiddenWordConstant::KEYWORD_APPLY_STATUS[ForbiddenWordConstant::KEYWORD_APPLY_DESC] }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-modi-save">수정</button>
                        <button type="button" class="btn btn-secondary htmlModalClose2">닫기</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $("input[name=keyword_type]").click(function(){
            let type = $(this).val();
            
            if( type == "delete" ){
                $("input[name=replace_keyword]").prop('disabled', true);
            } else {
                $("input[name=replace_keyword]").prop('disabled', false);
            }
        });

        $("input[name=keyword_modi_type]").click(function(){
            let type = $(this).val();
            
            if( type == "delete" ){
                $("input[name=replace_modi_keyword]").prop('disabled', true);
            } else {
                $("input[name=replace_modi_keyword]").prop('disabled', false);
            }
        });

        $('#btn-reg').click(function(){
            $("#htmlModal").modal('show');
        })

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $('.btn-modi').click(function(){
            let id = $(this).attr("dataid");

            $("#keyword_id").val(id);

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/forbiddenWord/${id}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    let { apply_type, keyword_type, replace_keyword, target_keyword } = resp.data;
                    
                    $(`input[name=keyword_modi_type]`).prop("checked", false);
                    $(`input[name=keyword_modi_type][value=${keyword_type}]`).prop("checked", true);

                    $(`input[name=target_modi_keyword]`).val(target_keyword);
                    
                    if( keyword_type == "delete" ){
                        $(`input[name=replace_modi_keyword]`).attr('disabled', true);
                        $(`input[name=replace_modi_keyword]`).val("");
                    } else {
                        $(`input[name=replace_modi_keyword]`).attr('disabled', false);
                        $(`input[name=replace_modi_keyword]`).val(replace_keyword);
                    }

                    $(`input[name=apply_modi_type]`).prop("checked", false);
                    $(`input[name=apply_modi_type][value=${apply_type}]`).prop("checked", true);

                    $("#htmlModal2").modal('show');
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $("#btn-select-del").click(function(){
            let ids = [];

            $(".chk-inp:checked").each(function(index, element){
                ids.push($(this).val());
            });

            if(ids.length < 1){
                return alert("선택 된 키워드가 없습니다.");
            }

            if( confirm("선택 한 키워드를 삭제 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.forbiddenWord.delete') }}",
                    "data"       : { ids },
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
            if( confirm("선택 한 키워드를 삭제 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.forbiddenWord.delete') }}",
                    "data"       : { ids: [id] },
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
            let keyword_type    = $('input[name=keyword_type]:checked').val();
            let target_keyword  = $('input[name=target_keyword]').val();
            let replace_keyword = $('input[name=replace_keyword]').val();
            let apply_type      = $('input[name=apply_type]:checked').val();

            if( target_keyword == "" ){
                return alert("대상 키워드를 입력하세요.");
            }

            if( keyword_type == "replace" ){
                if( replace_keyword == "" ){
                    return alert("교체 키워드를 입력하세요.");
                }   
            }

            if( confirm("해당 키워드를 등록하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.forbiddenWord.create') }}",
                    "data"       : { 
                        keyword_type,
                        target_keyword,
                        replace_keyword,
                        apply_type,
                    },
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

        $('.btn-modi-save').click(function(){
            let id              = $("#keyword_id").val();
            let keyword_type    = $('input[name=keyword_modi_type]:checked').val();
            let target_keyword  = $('input[name=target_modi_keyword]').val();
            let replace_keyword = $('input[name=replace_modi_keyword]').val();
            let apply_type      = $('input[name=apply_modi_type]:checked').val();

            if( target_keyword == "" ){
                return alert("대상 키워드를 입력하세요.");
            }

            if( keyword_type == "replace" ){
                if( replace_keyword == "" ){
                    return alert("교체 키워드를 입력하세요.");
                }   
            }

            if( confirm("해당 키워드를 수정하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.forbiddenWord.update') }}",
                    "data"       : {
                        id,
                        keyword_type,
                        target_keyword,
                        replace_keyword,
                        apply_type,
                    },
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
