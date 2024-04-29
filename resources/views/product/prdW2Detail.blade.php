@php
    use App\Constants\ProductConstant;
    use App\Constants\ImageConstant;
    $exchangeRate = env("1688_EXCHANGE_RATE", 200);
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .prd-desc {
        padding: 10px;
        width: 100% !important;
    }
    .prd-desc div,
    .prd-desc img,
    .prd-desc table,
    .prd-desc table td div,
    .prd-desc table td a{
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
    }
    .untranslated-text {
        color: red;
        font-size: 20px;
        font-weight: bold;
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
                <li class="breadcrumb-item active" aria-current="page">수집 상품 상세</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
                <div class="col-md-6" style="text-align: -webkit-center; position: relative;">
                    <div class="col">
                        <h5>[원본 이미지]</h5>
                    </div>
                    <div id="swiper-container1" class="swiper-container">
                        <div class="swiper-wrapper">
                            @foreach ($prdObj->images as $prdImg)
                            @if ($prdImg->img_type == "main")
                                <div class="swiper-slide">
                                    <img src={{ $prdImg->img_url_origin}}>
                                </div>
                            @endif
                            @endforeach
                            @foreach ($prdObj->images as $prdImg)
                            @if ($prdImg->img_type == "sub")
                                <div class="swiper-slide">
                                    <img src={{ $prdImg->img_url_origin}}>
                                </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="swiper-pagination swiper-pagination1"></div>
                    <div class="swiper-button-next swiper-button-next1"></div>
                    <div class="swiper-button-prev swiper-button-prev1"></div>
                </div>
                <div class="col-md-6" style="text-align: -webkit-center; position: relative;">
                    <div class="col">
                        <h5>[번역 이미지]</h5>
                    </div>
                    @if ($prdObj->trans_status == ProductConstant::IMG_TRANS_Y)
                        <div id="swiper-container2" class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($prdObj->images as $prdImg)
                                @if ($prdImg->img_type == "main")
                                    <div class="swiper-slide">
                                        <img src={{ $prdImg->img_url_trans}}>
                                    </div>
                                @endif
                                @endforeach
                                @foreach ($prdObj->images as $prdImg)
                                @if ($prdImg->img_type == "sub" && $prdImg->is_except == ImageConstant::IS_EXCEPT_N)
                                    <div class="swiper-slide">
                                        <img src={{ $prdImg->img_url_trans}}>
                                    </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="swiper-pagination swiper-pagination2"></div>
                        <div class="swiper-button-next swiper-button-next2"></div>
                        <div class="swiper-button-prev swiper-button-prev2"></div>
                    @else
                        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                            <span class="untranslated-text">
                                미변역
                            </span>
                        </div>
                    @endif
                </div>

                <hr style="margin-top: 20px">
                <div class="row mb-12">
                    <div class="col">
                        <h5>[기본 정보]</h5>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품ID</div>
                        <div class="col-md-8"><a href="https://detail.1688.com/offer/{{ $prdObj->offer_id }}.html" target="_blank">{{ $prdObj->offer_id }}</a></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명</div>
                        <div class="col-md-8">{{ $prdObj->prd_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(영문)</div>
                        <div class="col-md-8">{{ $prdObj->prd_name_en }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(국문)</div>
                        <div class="col-md-8">{{ $prdObj->prd_name_kr }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">W 카테고리</div>
                        <div class="col-md-8">
                            @if ( $prdObj->category != null )   
                                @if(isset($prdObj->category->cate_first))
                                    {{ $prdObj->category->cate_first }}
                                @endif
                                @if(isset($prdObj->category->cate_second))
                                    > {{ $prdObj->category->cate_second }}
                                @endif
                                @if(isset($prdObj->category->cate_third))
                                    > {{ $prdObj->category->cate_third }}
                                @endif
                            @else
                                <span class="text-danger fs-5">미수집된 W 카테고리</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">WApp 카테고리 (맵핑 코드)</div>
                        <div class="col-md-8">
                            @if ( $prdObj->mapping_status == ProductConstant::MAPPING_STATUS_Y)    
                                @php
                                    $wCate = $prdObj->w_mapping;
                                @endphp
                                @if(isset($wCate->w_cate_name->cate_first))
                                    {{ $wCate->w_cate_name->cate_first }}
                                @endif
                                @if(isset($wCate->w_cate_name->cate_second))
                                    > {{ $wCate->w_cate_name->cate_second }}
                                @endif
                                @if(isset($wCate->w_cate_name->cate_third))
                                    > {{ $wCate->w_cate_name->cate_third }}
                                @endif
                                ({{ $wCate->mapping_code }})
                            @else
                                <span class="text-danger fs-5">미맵핑</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">배송비</div>
                        <div class="col-md-8">
                            기본 배송비: {{ number_format($prdObj->extends->send_default_price) }}(원)</br>
                            제주도 배송비: {{ number_format($prdObj->extends->send_jeju_price) }}(원)</br>
                            도서산간지역 배송비: {{ number_format($prdObj->extends->send_etc_price) }}(원)
                        </div>
                    </div>
                </div>

                <hr style="margin-top: 20px">
                <div class="row mt-3">
                    <div class="col">
                        <h5>[옵션]</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th scope="col">skuID</th>
                                    <th scope="col">옵션명(영문)</th>
                                    <th scope="col">옵션명(국문)</th>
                                    <th scope="col">W 공급가(위안)</th>
                                    <th scope="col">W 공급가(원)</th>
                                    <th scope="col">적용 환율(원)</th>
                                    <th scope="col">일반 판매가(원)</th>
                                    <th scope="col">MD 판매가(원)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prdObj->options as $option)
                                    <tr class="text-center">
                                        <td>
                                            {{ $option->sku_id }}
                                        </td>
                                        <td>
                                            {{ $option->option_name_en }}
                                        </td>
                                        <td>
                                            {{ $option->option_name_kr }}
                                        </td>
                                        <td>
                                            {{ $option->price_1688 }}
                                        </td>
                                        <td>
                                            {{ number_format($option->option_price) }}
                                        </td>
                                        <td>
                                            {{ number_format($option->exchange_rate) }}
                                        </td>
                                        <td>
                                            {{ number_format(calcWSalePrice($option->option_price)) }}
                                        </td>
                                        <td>
                                            {{ number_format($option->md_price) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr style="margin-top: 20px">
                <div class="row mt-3">
                    <div class="col">
                        <h5>[고시정보(번역)]</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <tbody>
                                @foreach ($prdObj->notices as $gosiKey => $gosi)
                                    @if ( $gosiKey % 4 == 0)
                                        <tr>
                                    @endif

                                        <th class="bg-light">{{ $gosi->attribute_name_trans }}</th>
                                        <td>{{ $gosi->attribute_value_trans }}</td>

                                    @if (($gosiKey + 1) % 4 == 0 || $loop->last)
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr style="margin-top: 20px">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h5>[제품상세 원본]</h5>
                        <div class="d-flex justify-content-center">
                            <div class="text-center prd-desc">
                                {!! $prdObj->prd_desc !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>[제품상세 번역]</h5>
                        @if ($prdObj->trans_status == ProductConstant::IMG_TRANS_Y)
                            <div class="d-flex justify-content-center">
                                <div class="text-center prd-desc" >
                                    {!! $prdObj->prd_desc_trans !!}
                                </div>
                            </div>
                        @else
                            <div class="text-center mt-3">
                                <span class="untranslated-text">
                                    미변역
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        {{-- <div class="me-5 mb-4 fixed-bottom d-flex flex-column align-items-stretch" style="left: auto;">
            <a class="btn btn-primary btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="all">
                전체 이미지<br>번역요청
            </a>
            <a class="btn btn-warning btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="thumbnail">
                썸네일 이미지<br>번역요청
            </a>
            <a class="btn btn-success btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="desc">
                상세 이미지<br>번역요청
            </a>
            <a class="btn btn-danger btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="detail">이미지 수정</a>
        </div>         --}}
    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $('.btn-edit-img').click(function(e){
            e.preventDefault();

            let type         = $(this).attr("type");
            let trans_status = "{{ $prdObj->trans_status }}";
            let offer_id     = "{{ $prdObj->offer_id }}";

            if( type == "all" ){
                if( confirm("전체 이미지 번역요청을 하시겠습니까?") ){
                    $.ajax({
                        "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"       : "POST",
                        "url"        : "{{ route('genuio.imgTransRequest') }}",
                        "data"       : { offerIds: [offer_id] },
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete: function () {
                            $("#loadingOverlay").hide();
                        },
                        success: function (resp) {
                            alert(resp.msg);
                        },
                        error: function error(request, status, _error) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            } else if( type == "thumbnail" ){
                if( confirm("썸네일 이미지 번역요청을 하시겠습니까?") ){
                    $.ajax({
                        "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"       : "POST",
                        "url"        : `/api/genuio/img/thumnail/trans/request/${offer_id}`,
                        "data"       : {},
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete: function () {
                            $("#loadingOverlay").hide();
                        },
                        success: function (resp) {
                            alert(resp.msg);
                        },
                        error: function error(request, status, _error) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            } else if( type == "desc" ){
                if( confirm("상세 이미지 번역요청을 하시겠습니까?") ){
                    $.ajax({
                        "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"       : "POST",
                        "url"        : `/api/genuio/img/desc/trans/request/${offer_id}`,
                        "data"       : {},
                        beforeSend: function () {
                            $("#loadingOverlay").show();
                        },
                        complete: function () {
                            $("#loadingOverlay").hide();
                        },
                        success: function (resp) {
                            alert(resp.msg);
                        },
                        error: function error(request, status, _error) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            } else if( type == "detail" ){
                if( trans_status == "N" ){
                    return alert("번역이 완료 된 상태에서만 수정 가능합니다.");
                }
                window.open(`/product/img/edit/${offer_id}`, '_blank');
            }
        });

        var mySwiper = new Swiper('#swiper-container1', {
            // Optional parameters
            slidesPerView: 1,
            spaceBetween: 10,
            direction: 'horizontal',
            loop: false,
            // If we need pagination
            pagination: {
                el: '.swiper-pagination1',
                clickable: true,
            },
            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next1',
                prevEl: '.swiper-button-prev1',
            },

            // And if we need scrollbar
            scrollbar: {
                el: '.swiper-scrollbar1',
            }
        });

        var mySwiper2 = new Swiper('#swiper-container2', {
            // Optional parameters
            slidesPerView: 1,
            spaceBetween: 10,
            direction: 'horizontal',
            loop: false,
            // If we need pagination
            pagination: {
                el: '.swiper-pagination2',
                clickable: true,
            },
            // Navigation arrows
            navigation: {
                nextEl: '.swiper-button-next2',
                prevEl: '.swiper-button-prev2',
            },

            // And if we need scrollbar
            scrollbar: {
                el: '.swiper-scrollbar2',
            }
        });
    })
</script>

@endsection
