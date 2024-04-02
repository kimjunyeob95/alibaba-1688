@php
    use App\Constants\ProductConstant;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .prd-desc {
        padding: 10px;
    }
    .prd-desc img{
        max-width: 100%; /* 이미지가 부모 요소 너비를 넘지 않게 함 */
        height: auto; /* 이미지의 높이를 비율에 맞게 조정 */
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
                <li class="breadcrumb-item active" aria-current="page">1688 수집 상품 상세</li>
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
                                @if ($prdImg->img_type == "sub")
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
                        <div class="col-md-8">{{ $prdObj->offer_id }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명</div>
                        <div class="col-md-8">{{ $prdObj->prd_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(번역)</div>
                        <div class="col-md-8">{{ $prdObj->prd_name_trans }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">맵핑 카테고리</div>
                        <div class="col-md-8">
                            {{ $prdObj->category->cate_first }}
                            @if($prdObj->category->cate_second)
                                > {{ $prdObj->category->cate_second }}
                            @endif
                            @if($prdObj->category->cate_third)
                                > {{ $prdObj->category->cate_third }}
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">1688 카테고리</div>
                        <div class="col-md-8">
                            {{ $prdObj->category->cate_chinese_first }}
                            @if($prdObj->category->cate_chinese_second)
                                > {{ $prdObj->category->cate_chinese_second }}
                            @endif
                            @if($prdObj->category->cate_chinese_third)
                                > {{ $prdObj->category->cate_chinese_third }}
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
                                    <th scope="col">옵션명</th>
                                    <th scope="col">옵션명(번역)</th>
                                    <th scope="col">1688 소비자가</th>
                                    <th scope="col">옵션가격</th>
                                    <th scope="col">온채널가</th>
                                    <th scope="col">소비자가</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prdObj->options as $option)
                                    <tr class="text-center">
                                        <td>
                                            {{ $option->sku_id }}
                                        </td>
                                        <td>
                                            {{ $option->option_name }}
                                        </td>
                                        <td>
                                            {{ $option->option_name_trans }}
                                        </td>
                                        <td>
                                            {{ $option->price_1688 }}(元)
                                        </td>
                                        <td>
                                            {{ number_format($option->option_price) }}(원)
                                        </td>
                                        <td>
                                            {{ number_format($option->onch_price) }}(원)
                                        </td>
                                        <td>
                                            {{ number_format($option->cus_price) }}(원)
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
                    <div class="col">
                        <h5>[고시정보]</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <tbody>
                                @foreach ($prdObj->notices as $gosiKey => $gosi)    
                                    @if ( $gosiKey % 4 == 0)
                                        <tr>
                                    @endif

                                        <th class="bg-light">{{ $gosi->attribute_name }}</th>
                                        <td>{{ $gosi->attribute_value }}</td>

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
    </div>
<script type="text/javascript">

    $(document).ready(function(){
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
