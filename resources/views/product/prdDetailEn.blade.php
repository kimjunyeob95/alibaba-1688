@php
    use App\Constants\ProductConstant;
    use App\Constants\WConstant;
    use App\Constants\ImageConstant;
    use App\Constants\GosiConstants;
    use App\Constants\OptionConstants;
    use App\Constants\InspectConstant;
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
        max-width: 100% !important;
        height: auto !important;
    }
    .untranslated-text {
        color: red;
        font-size: 20px;
        font-weight: bold;
    }
    .swiper-slide {
        position: relative;
        display: inline-block; /* 이미지와 텍스트를 인라인 블록으로 처리 */
    }

    .swiper-slide img {
        display: block; /* 이미지가 div 크기에 맞춰서 확장되도록 설정 */
        width: 100%; /* 이미지 너비를 div에 맞춤 */
    }

    .swiper-slide .badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(255, 255, 255, 0.75); /* 텍스트 배경 투명도 설정 */
        color: black; /* 텍스트 색상 설정 */
        padding: 5px; /* 패딩 설정 */
        border-radius: 0 0 0 5px; /* 오른쪽 상단 모서리 둥글게 처리 */
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
                <li class="breadcrumb-item">W App</li>
                <li class="breadcrumb-item active" aria-current="page">수집 상품 영문 상세</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
                <div class="col-md-6" style="text-align: -webkit-center; position: relative;">
                    <div class="col">
                        <h5>[영문 원본 이미지]</h5>
                    </div>
                    <div id="swiper-container1" class="swiper-container">
                        <div class="swiper-wrapper">
                            @foreach ($prdObj->en_images as $prdImg)
                            @if ($prdImg->img_type == "main")
                                <div class="swiper-slide">
                                    <img src={{ $prdImg->img_url_origin}}>
                                </div>
                            @endif
                            @endforeach
                            @foreach ($prdObj->en_images as $prdImg)
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
                    @if ($prdObj->trans_status_en == ProductConstant::IMG_TRANS_Y)
                        <div id="swiper-container2" class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach ($prdObj->en_images as $prdImg)
                                @if ($prdImg->img_type == "main")
                                    <div class="swiper-slide">
                                        <img src={{ $prdImg->img_url_trans}}>
                                        <span class="badge fs-5">대표 이미지</span>
                                    </div>
                                @endif
                                @endforeach
                                @foreach ($prdObj->en_images as $prdImg)
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
                                미번역
                            </span>
                        </div>
                    @endif
                </div>

                <hr style="margin-top: 20px">
                <div class="row mb-12">
                    <div class="col">
                        @if($prdObj->status != ProductConstant::PRD_STATUS_PUBLISH)
                            <h5>[기본 정보] <span class="bg-danger rounded text-white px-2 py-1 fs-6">{{ ProductConstant::PRD_STATUS[$prdObj->status] }}</span></h5>
                        @else
                            <h5>[기본 정보]</h5>
                        @endif
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품ID</div>
                        <div class="col-md-8"><a href="https://detail.1688.com/offer/{{ $prdObj->offer_id }}.html" target="_blank">{{ $prdObj->offer_id }}</a></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(중문)</div>
                        <div class="col-md-8">{{ $prdObj->prd_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(국문) - 원본</div>
                        <div class="col-md-8">
                            @if ($prdObj->forbidden_prd_name == null)
                                {{ $prdObj->prd_name_kr }}
                            @else
                                {{ $prdObj->forbidden_prd_name->origin_text }}
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(국문) - 금칙어 적용</div>
                        <div class="col-md-8">{{ $prdObj->prd_name_kr }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(영문)</div>
                        <div class="col-md-8">{{ $prdObj->prd_name_en }}</div>
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
                                    <th scope="col">옵션명(중문)</th>
                                    <th scope="col">옵션명(국문)</th>
                                    <th scope="col">옵션명(영문)</th>
                                    <th scope="col">W 공급가(위안)</th>
                                    <th scope="col">W 공급가(원)</th>
                                    <th scope="col">적용 환율(원)</th>
                                    <th scope="col">일반 판매가(원)</th>
                                    <th scope="col">MD 판매가(원)</th>
                                    <th scope="col">중량(kg)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prdObj->options as $option)
                                    <tr class="text-center @if($option->is_except == OptionConstants::IS_EXCEPT_Y) line-through @endif">
                                        <td>
                                            {{ $option->sku_id }}
                                        </td>
                                        <td>
                                            {{ $option->option_name }}
                                        </td>
                                        <td>
                                            {{ $option->option_name_kr }}
                                        </td>
                                        <td>
                                            {{ $option->option_name_en }}
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
                                        <td>
                                            {{ number_format($option->weight) }}
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
                        <h5>[고시정보(중문)]</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <tbody>
                                @foreach ($prdObj->notices as $gosiKey => $gosi)
                                    @if ( $gosiKey % 4 == 0)
                                        <tr>
                                    @endif

                                        <th class="bg-light">
                                            @if ($gosi->is_except == GosiConstants::IS_EXCEPT_Y)
                                                <del>{{ $gosi->attribute_name }}</del>
                                            @else
                                                {{ $gosi->attribute_name }}
                                            @endif
                                        </th>
                                        <td>
                                            @if ($gosi->is_except == GosiConstants::IS_EXCEPT_Y)
                                                <del>{{ $gosi->attribute_value }}</del>
                                            @else
                                                {{ $gosi->attribute_value }}
                                            @endif
                                        </td>

                                    @if (($gosiKey + 1) % 4 == 0 || $loop->last)
                                        @php $remainingCols = 4 - (($gosiKey + 1) % 4); @endphp
                                        @if ($loop->last && $remainingCols > 0 && $remainingCols < 4)
                                            <td colspan="{{ $remainingCols * 2 }}"></td>
                                        @endif
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
                        <h5>[고시정보(국문)]</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <tbody>
                                @foreach ($prdObj->notices as $gosiKey => $gosi)
                                    @if ( $gosiKey % 4 == 0)
                                        <tr>
                                    @endif

                                        <th class="bg-light">
                                            @if ($gosi->is_except == GosiConstants::IS_EXCEPT_Y)
                                                <del>{{ $gosi->attribute_name_kr }}</del>
                                            @else
                                                {{ $gosi->attribute_name_kr }}
                                            @endif
                                        </th>
                                        <td>
                                            @php
                                                $origin_value_text = $gosi->attribute_value_kr;
                                                foreach ($prdObj->forbidden_notice_values as $origin_notice_value) {
                                                    if( $origin_notice_value->origin_text && $origin_notice_value->trans_text == $gosi->attribute_value_kr ){
                                                        $origin_value_text = $origin_notice_value->origin_text;
                                                    }
                                                }
                                            @endphp
                                            <small>원본: {{ $origin_value_text }}</small><br>
                                            <small>금칙어 적용:
                                                @if ($gosi->is_except == GosiConstants::IS_EXCEPT_Y)
                                                    <del>{{ $gosi->attribute_value_kr }}</del>
                                                @else
                                                    {{ $gosi->attribute_value_kr }}
                                                @endif
                                            </small>
                                        </td>

                                    @if (($gosiKey + 1) % 4 == 0 || $loop->last)
                                        @php $remainingCols = 4 - (($gosiKey + 1) % 4); @endphp
                                        @if ($loop->last && $remainingCols > 0 && $remainingCols < 4)
                                            <td colspan="{{ $remainingCols * 2 }}"></td>
                                        @endif
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
                        <h5>[고시정보(영문)]</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <tbody>
                                @foreach ($prdObj->notices as $gosiKey => $gosi)
                                    @if ( $gosiKey % 4 == 0)
                                        <tr>
                                    @endif

                                        <th class="bg-light">
                                            @if ($gosi->is_except == GosiConstants::IS_EXCEPT_Y)
                                                <del>{{ $gosi->attribute_name_en }}</del>
                                            @else
                                                {{ $gosi->attribute_name_en }}
                                            @endif
                                        </th>
                                        <td>
                                            @if ($gosi->is_except == GosiConstants::IS_EXCEPT_Y)
                                                <del>{{ $gosi->attribute_value_en }}</del>
                                            @else
                                                {{ $gosi->attribute_value_en }}
                                            @endif
                                        </td>

                                    @if (($gosiKey + 1) % 4 == 0 || $loop->last)
                                        @php $remainingCols = 4 - (($gosiKey + 1) % 4); @endphp
                                        @if ($loop->last && $remainingCols > 0 && $remainingCols < 4)
                                            <td colspan="{{ $remainingCols * 2 }}"></td>
                                        @endif
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
                        <h5>[영문 제품상세 원본]</h5>
                        <div class="d-flex justify-content-center">
                            <div class="text-center prd-desc">
                                {!! $prdObj->prd_desc_en_origin !!}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>[영문 제품상세 번역]</h5>
                        @if ($prdObj->trans_status_en == ProductConstant::IMG_TRANS_Y)
                            <div class="d-flex justify-content-center">
                                <div class="text-center prd-desc" >
                                    {!! $prdObj->prd_desc_en !!}
                                </div>
                            </div>
                        @else
                            <div class="text-center mt-3">
                                <span class="untranslated-text">
                                    미번역
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="me-5 mb-4 fixed-bottom d-flex flex-column align-items-stretch" style="left: auto;">
            <button type="button" class="btn btn-danger btn-xl text-white mb-2 btn-recollect">
                재 수집 요청
            </button>
            <button type="button" class="btn btn-primary btn-xl text-white mb-2 btn-inspect-status">
                검수상태 변경
            </button>
            <button type="button" class="btn btn-danger btn-xl text-white mb-2 btn-edit-status">
                판매상태 변경
            </button>
            @if ($prdObj->status != ProductConstant::PRD_STATUS_EXCEPT)    
                <button type="button" class="btn btn-secondary btn-xl text-white mb-2 btn-edit-product">
                    상품정보 관리
                </button>
                {{-- <a class="btn btn-primary btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="all">
                    전체 이미지<br>번역요청
                </a>
                <a class="btn btn-warning btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="thumbnail">
                    썸네일 이미지<br>번역요청
                </a>
                <a class="btn btn-success btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="desc">
                    상세 이미지<br>번역요청
                </a>
                <a class="btn btn-danger btn-xl text-white text-decoration-none mb-2 btn-edit-img" type="detail">이미지 수정</a> --}}
            @endif
        </div>

        <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel">판매 상태 변경</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">판매 상태</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status1" value="{{ ProductConstant::PRD_STATUS_PUBLISH }}" @if($prdObj->status == ProductConstant::PRD_STATUS_PUBLISH) checked @endif>
                                            <label class="form-check-label" for="status1">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_PUBLISH] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status2" value="{{ ProductConstant::PRD_STATUS_STOP }}" @if($prdObj->status == ProductConstant::PRD_STATUS_STOP) checked @endif>
                                            <label class="form-check-label" for="status2">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_STOP] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status3" value="{{ ProductConstant::PRD_STATUS_EXCEPT }}" @if($prdObj->status == ProductConstant::PRD_STATUS_EXCEPT) checked @endif>
                                            <label class="form-check-label" for="status3">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_EXCEPT] }}</label>
                                        </div>
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

        <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel2">검수상태 변경</h5>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">이미지</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_img_status" id="inspect_img_status1" value="{{ InspectConstant::IS_INSPECT_Y }}" @if($prdObj->img_inspect != null && $prdObj->img_inspect->is_inspect == InspectConstant::IS_INSPECT_Y) checked @endif>
                                            <label class="form-check-label" for="inspect_img_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_img_status" id="inspect_img_status2" value="{{ InspectConstant::IS_INSPECT_N }}" @if($prdObj->img_inspect == null || $prdObj->img_inspect->is_inspect == InspectConstant::IS_INSPECT_N) checked @endif>
                                            <label class="form-check-label" for="inspect_img_status2">미완료</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">상품정보</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_prd_status" id="inspect_prd_status1" value="{{ InspectConstant::IS_INSPECT_Y }}" @if($prdObj->prd_inspect != null && $prdObj->prd_inspect->is_inspect == InspectConstant::IS_INSPECT_Y) checked @endif>
                                            <label class="form-check-label" for="inspect_prd_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_prd_status" id="inspect_prd_status2" value="{{ InspectConstant::IS_INSPECT_N }}" @if($prdObj->prd_inspect == null || $prdObj->prd_inspect->is_inspect == InspectConstant::IS_INSPECT_N) checked @endif>
                                            <label class="form-check-label" for="inspect_prd_status2">미완료</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">정보고시</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_gosi_status" id="inspect_gosi_status1" value="{{ InspectConstant::IS_INSPECT_Y }}" @if($prdObj->gosi_inspect != null && $prdObj->gosi_inspect->is_inspect == InspectConstant::IS_INSPECT_Y) checked @endif>
                                            <label class="form-check-label" for="inspect_gosi_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_gosi_status" id="inspect_gosi_status2" value="{{ InspectConstant::IS_INSPECT_N }}" @if($prdObj->gosi_inspect == null || $prdObj->gosi_inspect->is_inspect == InspectConstant::IS_INSPECT_N) checked @endif>
                                            <label class="form-check-label" for="inspect_gosi_status2">미완료</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-inspect-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose2">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    $(document).ready(function(){

        var offer_id = "{{ $prdObj->offer_id }}";

        $('.btn-edit-status').click(function(){
            $("#htmlModal").modal('show');
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $('.btn-inspect-status').click(function(){
            $("#htmlModal2").modal('show');
        });

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $(".btn-inspect-save").click(function(){
            let offerIds = [offer_id];

            let inspect_img_status = $("input[name=inspect_img_status]:checked").val();
            let inspect_prd_status = $("input[name=inspect_prd_status]:checked").val();
            let inspect_gosi_status = $("input[name=inspect_gosi_status]:checked").val();

            if( confirm("검수상태를 변경 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.inspectStatusUpdate') }}",
                    "data"       : { 
                        offerIds,
                        inspect_img_status,
                        inspect_prd_status,
                        inspect_gosi_status,
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

        $(".btn-recollect").click(function(){
            let offerIds = [offer_id];

            if(confirm(`재 수집 시 저장 된 상품의 정보가 초기화 됩니다.\n재 수집을 진행하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.reCollectProduct') }}",
                    "data"       : { offer_ids: offerIds },
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
        });

        $(".btn-save").click(function(){
            let status   = $("input[name=status]:checked").val();

            if( confirm("판매상태를 변경 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.statusUpdate') }}",
                    "data"       : { offerIds: [offer_id], status },
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

        $('.btn-edit-product').click(function(){
            window.open(`/product/update/${offer_id}`, '_blank');
        });

        $('.btn-edit-img').click(function(e){
            e.preventDefault();

            let type         = $(this).attr("type");
            let trans_status = "{{ $prdObj->trans_status_en }}";

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
