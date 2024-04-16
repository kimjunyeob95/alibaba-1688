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
<script type="text/javascript">
    $(function(){
        $(".swiper-container").each(function(idx){
            console.log(idx);
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
        })
    })
</script>
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
            <div class="container-fluid">
                <div class="d-flex justify-content-between">
                    <h4>대표이미지</h4>
                    <div>
                        <button type="button" class="btn btn-light">전체선택</button>
                        <button type="button" class="btn btn-success">A.I 수정</button>
                        <button type="button" class="btn btn-warning">A.I Tool</button>
                        <button type="button" class="btn btn-primary">적용하기</button>
                    </div>
                </div>

            @php
                $idx = 0;
            @endphp
            @foreach ($prdObj->images as $prdImg)
                @if (in_array($prdImg->img_type, [ImageConstant::IMAGE_TYPE_MAIN, ImageConstant::IMAGE_TYPE_SUB]))
                <div class="row my-4 mx-5 p-4 px-5 bg-light border justify-content-between">
                    <div class="col-3">
                        <div class="row">
                            <img src="{{ $prdImg->img_url_trans }}" class="rounded img-fluid" alt="...">
                            <figcaption class="figure-caption fs-6 text-center">{{ $prdImg->trans_dated_at }}</figcaption>
                        </div>
                    </div>
                    <div class="col-3 position-relative">
                        <div class="swiper-container" id="swiper-container{{ $idx }}">
                            <div class="swiper-wrapper align-items-center">
                            @foreach ($prdObj->images as $mainImg)
                                @if (in_array($mainImg->img_type, [ImageConstant::IMAGE_TYPE_MAIN, ImageConstant::IMAGE_TYPE_SUB]))
                                <div class="swiper-slide">
                                    <div>
                                        <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $mainImg->id }}" style="z-index: 10;">
                                        <img src="{{ $mainImg->img_url_trans }}" class="" alt="...">
                                        <figcaption class="figure-caption fs-6 text-center">{{ $prdImg->trans_dated_at }}</figcaption>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            </div>
                        </div>
                        <div class="swiper-button-next swiper-button-next{{ $idx }}"></div>
                        <div class="swiper-button-prev swiper-button-prev{{ $idx }}"></div>
                    </div>
                    <div class="col-2 d-flex flex-column justify-content-evenly">
                        <button type="button" class="btn btn-success">A.I 수정</button>
                        <button type="button" class="btn btn-warning">A.I Tool</button>
                        <button type="button" class="btn btn-primary">적용하기</button>
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
            <div class="container-fluid">
                <div class="d-flex justify-content-between">
                    <h4>상세이미지</h4>
                    <div>
                        <button type="button" class="btn btn-light">전체선택</button>
                        <button type="button" class="btn btn-success">A.I 수정</button>
                        <button type="button" class="btn btn-warning">A.I Tool</button>
                        <button type="button" class="btn btn-primary">적용하기</button>
                    </div>
                </div>

            @foreach ($prdObj->images as $prdImg)
                @if ($prdImg->img_type == ImageConstant::IMAGE_TYPE_DESC)
                <div class="row my-4 mx-5 p-4 px-5 bg-light border justify-content-between">
                    <div class="col-3 d-flex align-items-center">
                        <div class="text-center">
                            <img src="{{ $prdImg->img_url_trans }}" class="rounded img-fluid" alt="...">
                            <figcaption class="figure-caption fs-6 text-center">{{ $prdImg->trans_dated_at }}</figcaption>
                        </div>
                    </div>
                    <div class="col-3 position-relative">
                        <div class="swiper-container" id="swiper-container{{ $idx }}">
                            <div class="swiper-wrapper align-items-center">
                            @foreach ($prdObj->images as $descImg)
                                @if ($descImg->img_type == ImageConstant::IMAGE_TYPE_DESC)
                                <div class="swiper-slide">
                                    <div>
                                        <input class="form-check-input position-absolute start-0 m-2" type="checkbox" name="selectImg" value="{{ $descImg->id }}" style="z-index: 10;">
                                        <img src="{{ $descImg->img_url_trans }}" class="" alt="...">
                                        <figcaption class="figure-caption fs-6 text-center">{{ $prdImg->trans_dated_at }}</figcaption>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            </div>
                        </div>
                        <div class="swiper-button-next swiper-button-next{{ $idx }}"></div>
                        <div class="swiper-button-prev swiper-button-prev{{ $idx }}"></div>
                    </div>
                    <div class="col-2 d-flex flex-column justify-content-evenly">
                        <button type="button" class="btn btn-success">A.I 수정</button>
                        <button type="button" class="btn btn-warning">A.I Tool</button>
                        <button type="button" class="btn btn-primary">적용하기</button>
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
@endsection