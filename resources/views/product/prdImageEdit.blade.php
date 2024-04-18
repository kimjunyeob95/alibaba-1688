@php
    use App\Constants\ImageConstant;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
.swiper-container{
    width: 100%;
    max-width: 100%;
    height: auto;
}
.swiper-button-prev{
    left: -20px;
}
.swiper-button-next{
    right: -20px;
}
.swiper-slide{
    background-color: rgba(0, 0, 0, 0);
}
.swiper-slide img{
    width: 100%;
    height: 100%;
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
            <li class="breadcrumb-item">
                <a href="/">상품 리스트</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Image 수정</li>
        </ol>
    </nav>

    <div class="container-fluid">
        <div class="row my-4 bg-white py-3">
            <div class="container-fluid main-container">
                <div class="d-flex justify-content-between">
                    <h4>썸네일 이미지</h4>
                    <div>
                        <button type="button" class="btn btn-light allCheckbtn" attr-type="main">전체선택</button>
                        <button type="button" class="btn btn-success text-white allAImodiBtn" attr-type="main">A.I 수정</button>
                        <button type="button" class="btn btn-warning text-white allAItoolBtn" attr-type="main">A.I Tool</button>
                        <button type="button" class="btn btn-primary text-white allApplyBtn"  attr-type="main">적용하기</button>
                    </div>
                </div>

                {{-- 1. 메인이미지 --}}
                <div class="row my-4 mx-5 p-4 px-5 bg-light border justify-content-between">
                    <div class="col-3">
                        <div class="row">
                            <img src="{{ $prdObj->main_img->img_url_trans }}" class="rounded img-fluid" alt="...">
                            <figcaption class="figure-caption fs-6 text-center mt-3">{{ $prdObj->main_img->trans_dated_at }}</figcaption>
                        </div>
                    </div>
                    <div class="col-3 position-relative swiper-box">
                        <div class="swiper-container" id="swiper-container0">
                            <div class="swiper-wrapper align-items-center">
                                @if ($prdObj->main_img->ai_origin_img)    
                                    <div class="swiper-slide">
                                        <div>
                                            <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $prdObj->main_img->ai_origin_img->id }}" style="z-index: 10;">
                                            <img src="{{ $prdObj->main_img->ai_origin_img->img_url_ai }}" class="" alt="...">
                                            <figcaption class="figure-caption fs-6 text-center mt-3">{{ $prdObj->main_img->ai_origin_img->created_at }}</figcaption>
                                        </div>
                                    </div>
                                @endif
                                @foreach ($prdObj->main_img->ai_imgs as $aiImg)
                                    <div class="swiper-slide">
                                        <div>
                                            <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $aiImg->id }}" style="z-index: 10;">
                                            <img src="{{ $aiImg->img_url_ai }}" class="" alt="...">
                                            <figcaption class="figure-caption fs-6 text-center mt-3">{{ $aiImg->created_at }}</figcaption>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="swiper-button-next swiper-button-next0"></div>
                        <div class="swiper-button-prev swiper-button-prev0"></div>
                    </div>
                    <div class="col-2 d-flex flex-column justify-content-evenly">
                        <button type="button" class="btn btn-success text-white AImodiBtn">A.I 수정</button>
                        <button type="button" class="btn btn-warning text-white AItoolBtn">A.I Tool</button>
                        <button type="button" class="btn btn-primary text-white applyBtn">적용하기</button>
                    </div>
                </div>

                {{-- 2. 서브 이미지 --}}
                @php
                    $idx = 1;
                @endphp
                @foreach ( $prdObj->sub_imgs as $img)
                    @if( $img->img_url_trans )
                        <div class="row my-4 mx-5 p-4 px-5 bg-light border justify-content-between">
                            <div class="col-3">
                                <div class="row">
                                    <img src="{{ $img->img_url_trans }}" class="rounded img-fluid" alt="...">
                                    @if( $img->is_except == ImageConstant::IS_EXCEPT_N )
                                        <figcaption class="figure-caption fs-6 text-center mt-3">{{ $img->trans_dated_at }}</figcaption>
                                    @else
                                        <figcaption class="figure-caption fs-4 text-center mt-3 text-danger">*제외처리</figcaption>
                                    @endif
                                </div>
                            </div>
                            <div class="col-3 position-relative swiper-box">
                                <div class="swiper-container" id="swiper-container{{ $idx }}">
                                    <div class="swiper-wrapper align-items-center">
                                        @if( $img->ai_origin_img )
                                            <div class="swiper-slide">
                                                <div>
                                                    <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $img->ai_origin_img->id }}" style="z-index: 10;">
                                                    <img src="{{ $img->ai_origin_img->img_url_ai }}" class="" alt="...">
                                                    <figcaption class="figure-caption fs-6 text-center mt-3">{{ $img->ai_origin_img->created_at }}</figcaption>
                                                </div>
                                            </div>
                                        @endif
                                        @foreach ($img->ai_imgs as $aiImg)
                                            <div class="swiper-slide">
                                                <div>
                                                    <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $aiImg->id }}" style="z-index: 10;">
                                                    <img src="{{ $aiImg->img_url_ai }}" class="" alt="...">
                                                    <figcaption class="figure-caption fs-6 text-center mt-3">{{ $aiImg->created_at }}</figcaption>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="swiper-button-next swiper-button-next{{ $idx }}"></div>
                                <div class="swiper-button-prev swiper-button-prev{{ $idx }}"></div>
                            </div>
                            <div class="col-2 d-flex flex-column justify-content-evenly">
                                <button type="button" class="btn btn-success text-white AImodiBtn">A.I 수정</button>
                                <button type="button" class="btn btn-warning text-white AItoolBtn">A.I Tool</button>
                                <button type="button" class="btn btn-primary text-white applyBtn">적용하기</button>
                                @if ($img->is_except == ImageConstant::IS_EXCEPT_N )
                                    <button type="button" class="btn btn-danger text-white exceptBtn" except="{{ ImageConstant::IS_EXCEPT_Y }}" imgid={{ $img->id }}>제외하기</button>
                                @else
                                    <button type="button" class="btn btn-danger text-white exceptBtn" except="{{ ImageConstant::IS_EXCEPT_N }}" imgid={{ $img->id }}>제외 취소하기</button>
                                @endif
                            </div>
                        </div>
                    @php
                        $idx++;
                    @endphp
                    @endif
                @endforeach
            </div>
        </div>

        <div class="row my-4 bg-white py-3">
            <div class="container-fluid desc-container">
                <div class="d-flex justify-content-between">
                    <h4>상세이미지</h4>
                    <div>
                        <button type="button" class="btn btn-light allCheckbtn" attr-type="desc">전체선택</button>
                        <button type="button" class="btn btn-success text-white allAImodiBtn" attr-type="desc">A.I 수정</button>
                        <button type="button" class="btn btn-warning text-white allAItoolBtn" attr-type="desc">A.I Tool</button>
                        <button type="button" class="btn btn-primary text-white allApplyBtn"  attr-type="desc">적용하기</button>
                    </div>
                </div>

                @foreach ( $prdObj->desc_imgs as $img)
                    @if( $img->img_url_trans )
                        <div class="row my-4 mx-5 p-4 px-5 bg-light border justify-content-between">
                            <div class="col-3">
                                <div class="row">
                                    <img src="{{ $img->img_url_trans }}" class="rounded img-fluid" alt="...">
                                    @if( $img->is_except == ImageConstant::IS_EXCEPT_N )
                                        <figcaption class="figure-caption fs-6 text-center mt-3">{{ $img->trans_dated_at }}</figcaption>
                                    @else
                                        <figcaption class="figure-caption fs-4 text-center mt-3 text-danger">*제외처리</figcaption>
                                    @endif
                                </div>
                            </div>
                            <div class="col-3 position-relative swiper-box">
                                <div class="swiper-container" id="swiper-container{{ $idx }}">
                                    <div class="swiper-wrapper align-items-center">
                                        @if( $img->ai_origin_img )
                                            <div class="swiper-slide">
                                                <div>
                                                    <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $img->ai_origin_img->id }}" style="z-index: 10;">
                                                    <img src="{{ $img->ai_origin_img->img_url_ai }}" class="" alt="...">
                                                    <figcaption class="figure-caption fs-6 text-center mt-3">{{ $img->ai_origin_img->created_at }}</figcaption>
                                                </div>
                                            </div>
                                        @endif
                                        @foreach ($img->ai_imgs as $aiImg)
                                            <div class="swiper-slide">
                                                <div>
                                                    <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $aiImg->id }}" style="z-index: 10;">
                                                    <img src="{{ $aiImg->img_url_ai }}" class="" alt="...">
                                                    <figcaption class="figure-caption fs-6 text-center mt-3">{{ $aiImg->created_at }}</figcaption>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="swiper-button-next swiper-button-next{{ $idx }}"></div>
                                <div class="swiper-button-prev swiper-button-prev{{ $idx }}"></div>
                            </div>
                            <div class="col-2 d-flex flex-column justify-content-evenly">
                                <button type="button" class="btn btn-success text-white AImodiBtn">A.I 수정</button>
                                <button type="button" class="btn btn-warning text-white AItoolBtn">A.I Tool</button>
                                <button type="button" class="btn btn-primary text-white applyBtn">적용하기</button>
                                @if ($img->is_except == ImageConstant::IS_EXCEPT_N )
                                    <button type="button" class="btn btn-danger text-white exceptBtn" except="{{ ImageConstant::IS_EXCEPT_Y }}" imgid={{ $img->id }}>제외하기</button>
                                @else
                                    <button type="button" class="btn btn-danger text-white exceptBtn" except="{{ ImageConstant::IS_EXCEPT_N }}" imgid={{ $img->id }}>제외 취소하기</button>
                                @endif
                            </div>
                        </div>
                    @php
                        $idx++;
                    @endphp
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- ai 수정요청 모달 --}}
<div class="modal fade" id="AImodi-modal" tabindex="-1" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">A.I 수정 요청</h5>
                <button type="button" class="btn-close" data-coreui-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>AI 알고리즘 선택</p>
                <select class="form-select form-select-sm" id="AImodi-select">
                    <option selected value="automatic">자동 수정</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="AImodi-submit">요청</button>
                <button type="button" class="btn btn-secondary" data-coreui-dismiss="modal" aria-label="Close">취소</button>
                <input type="hidden" class="imgChecked">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $(".swiper-container").each(function(idx){
            var mySwiper = new Swiper('#swiper-container'+idx, {
                // Optional parameters
                slidesPerView: 1,
                spaceBetween: 0,
                direction: 'horizontal',
                loop: false,
                // If we need pagination
                pagination: {
                    el: '.swiper-pagination'+idx,
                    clickable: true,
                },
                // Navigation arrows
                navigation: {
                    nextEl: '.swiper-button-next'+idx,
                    prevEl: '.swiper-button-prev'+idx,
                },
                // And if we need scrollbar
                scrollbar: {
                    el: '.swiper-scrollbar'+idx,
                }
            });

            var container = $(this);
            mySwiper.on('slideChange', function () {
                container.find('.swiper-slide').find('input[type="checkbox"]').prop('checked', false);
            });
        })

        $(".allCheckbtn").click(function(){
            var type = $(this).attr("attr-type");
            var flag = !$("."+type+"-container").find("input[name='selectImg']").prop("checked");

            $("."+type+"-container").find("input[name='selectImg']").prop("checked", false);
            $("."+type+"-container .swiper-slide-active").find("input[name='selectImg']").prop("checked", flag);
        })


        $(".AImodiBtn, .allAImodiBtn").click(function(){
            if($(this).hasClass("AImodiBtn")){
                var checked = $(this).parent().prev(".swiper-box").find("input[name='selectImg']:checked");
            }else{
                var type = $(this).attr("attr-type");
                var checked = $("."+type+"-container").find("input[name='selectImg']:checked");
            }

            if(!checked.length){
                alert("선택된 이미지가 없습니다.");
                return false;
            }else{
                let imgChecked = [];
                checked.each(function(){
                    imgChecked.push($(this).val());
                });

                $("#AImodi-modal").find(".imgChecked").val(imgChecked);
                showModal("#AImodi-modal");
            }
        })

        $("#AImodi-submit").click(function(){
            let imgChecked = $("#AImodi-modal").find(".imgChecked").val();

            let imgIds = imgChecked.split(",");
            if( imgIds.length > 0 ){
                if(confirm("요청하시겠습니까?")){
                    $("#loadingOverlay").show();
                    $.ajax({
                        "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"       : "POST",
                        "url"        : "{{ route('genuio.imgAiTransRequest') }}",
                        "data"       : { imgIds },
                        beforeSend: function () {
                        },
                        complete: function () {
                            $("#loadingOverlay").hide();
                        },
                        success: function (resp) {
                            alert(resp.msg);
                            hideModal("#AImodi-modal");
                        },
                        error: function error(request, status, _error) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        })

        $(".AItoolBtn, .allAItoolBtn").click(function(){
            if($(this).hasClass("AItoolBtn")){
                var checked = $(this).parent().prev(".swiper-box").find("input[name='selectImg']:checked");
            }else{
                var type = $(this).attr("attr-type");
                var checked = $("."+type+"-container").find("input[name='selectImg']:checked");
            }

            if(!checked.length){
                alert("선택된 이미지가 없습니다.");
                return false;
            }else{
                let imgChecked = [];
                checked.each(function(){
                    imgChecked.push($(this).val());
                });

                return alert("A.I 툴 확인중..");
                // if(confirm("A.I 툴로 이동합니다.")){
                // }
            }
        })

        $(".applyBtn, .allApplyBtn").click(function(){
            if($(this).hasClass("applyBtn")){
                var checked = $(this).parent().prev(".swiper-box").find("input[name='selectImg']:checked");
            }else{
                var type = $(this).attr("attr-type");
                var checked = $("."+type+"-container").find("input[name='selectImg']:checked");
            }

            if(!checked.length){
                alert("선택된 이미지가 없습니다.");
                return false;
            }else{
                let imgChecked = [];
                checked.each(function(){
                    imgChecked.push($(this).val());
                });

                if( imgChecked.length > 0 ){
                    if(confirm(`선택한 이미지로 적용하시겠습니까?`)){
                        $("#loadingOverlay").show();
                        $.ajax({
                            "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            "type"       : "POST",
                            "url"        : "{{ route('w.product.imageAccept') }}",
                            "data"       : { aiImgIds: imgChecked },
                            beforeSend: function () {
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
                }
            }
        })

        $(".exceptBtn, .allExceptBtn").click(function(){
            let imgChecked = [$(this).attr("imgid")];
            let except     = $(this).attr("except");
            let exceptText = "제외";
            if( except == "N" ){
                exceptText = "제외 취소";
            }
            if( imgChecked.length > 0 ){
                if(confirm(`수집 ${exceptText}하시겠습니까?`)){
                    $("#loadingOverlay").show();
                    $.ajax({
                        "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"       : "POST",
                        "url"        : "{{ route('w.product.imageExcept') }}",
                        "data"       : { imgIds: imgChecked, is_except: except },
                        beforeSend: function () {
                        },
                        complete: function () {
                            $("#loadingOverlay").hide();
                        },
                        success: function (resp) {
                            alert(resp.msg);
                            location.reload();
                            // hideModal("#AImodi-modal");
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

    function showModal(id) {
        $(id).modal('show');
    }

    function hideModal(id) {
        $(id).modal('hide');
    }
</script>
@endsection