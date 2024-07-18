@php
    use App\Constants\AdminConstant;
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
                <li class="breadcrumb-item">관리자 관리</li>
                <li class="breadcrumb-item active" aria-current="page">관리자 등록</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row justify-content-center my-4">
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header">
                            <h5>[관리자 등록]</h5>
                        </div>
                        <div class="card-body">
                            <form id="searchFrm">
                                <div class="mb-3 row">
                                    <label for="user_id" class="col-md-2 col-form-label text-end">아이디</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required-inp" id="user_id" name="user_id" value="">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="password" class="col-md-2 col-form-label text-end">비밀번호</label>
                                    <div class="col-md-8">
                                        <input type="password" class="form-control required-inp" id="password" name="password" value="">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="email" class="col-md-2 col-form-label text-end">이메일</label>
                                    <div class="col-md-8">
                                        <input type="email" class="form-control required-inp" id="email" name="email" value="">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="company" class="col-md-2 col-form-label text-end">사업부</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="company" name="company">
                                            @foreach (AdminConstant::COMPANY as $company => $name)
                                                <option value="{{ $company }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="name" class="col-md-2 col-form-label text-end">이름</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control required-inp" id="name" name="name" value="">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="level" class="col-md-2 col-form-label text-end">권한</label>
                                    <div class="col-md-8">
                                        <select class="form-control" id="level" name="level">
                                            @foreach (AdminConstant::LEVEL_NAME as $level => $name)
                                                <option value="{{ $level }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-md-8 offset-md-2 text-center">
                                        <button type="button" class="btn btn-primary btn-md btn-save">등록</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        
    </div>
<script type="text/javascript">

    $(document).ready(function(){

        $('.btn-save').click(function(){
            let validate = true;
            $(".required-inp").each(function(){
                if($(this).val().trim() == ""){
                    alert("빈 값없이 입력해주세요.");
                    validate = false;
                    $(this).focus();
                    return false;
                }
            })

            if( validate == true ){
                let formData = $("#searchFrm").serialize();
                if( confirm("관리자로 등록하시겠습니까?") ){
                    $.ajax({
                        "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"       : "POST",
                        "url"        : "{{ route('wapp.admin.regist') }}",
                        "data"       : formData,
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete: function () {
                            $("#loadingOverlay").hide();
                        },
                        success: function (resp) {
                            alert(resp.message);
                            location.reload();
                        },
                        error: function error(request, status, _error) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }

        })
    })
</script>

@endsection
