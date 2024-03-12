@extends('dashboard.base')

@section('styles')
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
                <div class="col-md-4" style="text-align: -webkit-center; position: relative;">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img src={{ $prdObj->main_img_origin}} >
                            </div>
                            @foreach ($prdObj->images as $prdImg)
                            <div class="swiper-slide">
                                <img src={{ $prdImg->img_url_origin}}>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <div class="col-md-8 d-flex flex-column justify-content-center">
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품ID</div>
                        <div class="col-md-8">{{ $prdObj->offer_id }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제풍명</div>
                        <div class="col-md-8">{{ $prdObj->prd_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(영문)</div>
                        <div class="col-md-8">{{ $prdObj->prd_name_trans }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">카테고리</div>
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
                                    <th scope="col">옵션명(영문)</th>
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
                                            {{ $option->consign_price }}(元)
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

            </div>
        </div>
    </div>
<script type="text/javascript">

</script>

@endsection
