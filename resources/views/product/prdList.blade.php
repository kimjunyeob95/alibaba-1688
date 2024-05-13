@php
    use App\Constants\ProductConstant;
    use App\Constants\WConstant;
    use App\Constants\InspectConstant;
    $exchangeRate = env("1688_EXCHANGE_RATE", 200);
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
                <li class="breadcrumb-item">판매 상품 관리</li>
                <li class="breadcrumb-item active" aria-current="page">전체 상품(KOR)</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="w_type" value={{ $w_type }}>
                    <input type="hidden" name="trans_status" value={{ $trans_status }}>
                    <input type="hidden" name="mapping_status" value={{ $mapping_status }}>
                    <input type="hidden" name="prd_status" value={{ $prd_status }}>
                    <input type="hidden" name="mdPrice_status" value={{ $mdPrice_status }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 현황</th>
                                    <td colspan="3">
                                        <div class="d-flex text-center">
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 100px;">
                                                전체<br>
                                                {{ number_format($totalCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 100px;">
                                                번역완료<br>
                                                {{ number_format($transYCnt) }}건
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center flex-fill p-2 border rounded" style="height: 100px;">
                                                번역 미완료<br>
                                                {{ number_format($transNCnt) }}건
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">W type</th>
                                    <td colspan="3">
                                        <button type="button" name="w_type" class="btn-status btn btn-md {{ $w_type == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="w_type" class="btn-status btn btn-md {{ $w_type == WConstant::WAPP_W1 ? "btn-primary" : "btn-dark" }}"
                                        value="{{ WConstant::WAPP_W1 }}">W1</button>
                                        <button type="button" name="w_type" class="btn-status btn btn-md {{ $w_type == WConstant::WAPP_W2 ? "btn-primary" : "btn-dark" }}"
                                        value="{{ WConstant::WAPP_W2}}">W2</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 번역</th>
                                    <td colspan="3">
                                        <button type="button" name="trans_status" class="btn-status btn btn-md {{ $trans_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="trans_status" class="btn-status btn btn-md {{ $trans_status == ProductConstant::TRANS_STATUS_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::TRANS_STATUS_Y }}">완료</button>
                                        <button type="button" name="trans_status" class="btn-status btn btn-md {{ $trans_status == ProductConstant::TRANS_STATUS_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::TRANS_STATUS_N }}">미완료</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">카테고리 맵핑</th>
                                    <td colspan="3">
                                        <button type="button" name="mapping_status" class="btn-status btn btn-md {{ $mapping_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="mapping_status" class="btn-status btn btn-md {{ $mapping_status == ProductConstant::MAPPING_STATUS_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MAPPING_STATUS_Y }}">맵핑</button>
                                        <button type="button" name="mapping_status" class="btn-status btn btn-md {{ $mapping_status == ProductConstant::MAPPING_STATUS_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MAPPING_STATUS_N }}">미맵핑</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">판매 상태</th>
                                    <td colspan="3">
                                        <button type="button" name="prd_status" class="btn-status btn btn-md {{ $prd_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="prd_status" class="btn-status btn btn-md {{ $prd_status == ProductConstant::PRD_STATUS_PUBLISH ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::PRD_STATUS_PUBLISH }}">정상판매</button>
                                        <button type="button" name="prd_status" class="btn-status btn btn-md {{ $prd_status == ProductConstant::PRD_STATUS_STOP ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::PRD_STATUS_STOP }}">판매중지</button>
                                        {{-- <button type="button" name="prd_status" class="btn-status btn btn-md {{ $prd_status == ProductConstant::PRD_STATUS_EXCEPT ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::PRD_STATUS_EXCEPT }}">판매제외</button> --}}
                                        <button type="button" name="prd_status" class="btn-status btn btn-md {{ $prd_status == ProductConstant::PRD_STATUS_MISS ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::PRD_STATUS_MISS }}">정보누락</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">판매가 설정</th>
                                    <td colspan="3">
                                        <button type="button" name="mdPrice_status" class="btn-status btn btn-md {{ $mdPrice_status == "" ? "btn-primary" : "btn-dark" }}"
                                        value="">전체</button>
                                        <button type="button" name="mdPrice_status" class="btn-status btn btn-md {{ $mdPrice_status == ProductConstant::MD_PRICE_Y ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MD_PRICE_Y }}">설정</button>
                                        <button type="button" name="mdPrice_status" class="btn-status btn btn-md {{ $mdPrice_status == ProductConstant::MD_PRICE_N ? "btn-primary" : "btn-dark" }}"
                                        value="{{ ProductConstant::MD_PRICE_N }}">미설정</button>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="offer_id" @if($search_cls == "offer_id") selected @endif>제품 ID</option>
                                            <option value="prd_name" @if($search_cls == "prd_name") selected @endif>상품명</option>
                                            <option value="option_name" @if($search_cls == "option_name") selected @endif>옵션명</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="여러 상품을 동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정렬</th>
                                    <td>
                                        <select class="form-select" name="sort" style="width: 200px">
                                            <option value="updated_at|desc" @if($sort == "updated_at|desc") selected @endif>수정일 내림차순</option>
                                            <option value="updated_at|asc" @if($sort == "updated_at|asc") selected @endif>수정일 오름차순</option>
                                            <option value="created_at|desc" @if($sort == "created_at|desc") selected @endif>등록일 내림차순</option>
                                            <option value="created_at|asc" @if($sort == "created_at|asc") selected @endif>동록일 오름차순</option>
                                            <option value="start_quantity|desc" @if($sort == "start_quantity|desc") selected @endif>최소구매수량 내림차순</option>
                                            <option value="start_quantity|asc" @if($sort == "start_quantity|asc") selected @endif>최소구매수량 오름차순</option>
                                            <option value="option_price|desc" @if($sort == "option_price|desc") selected @endif>W 공급가 내림차순</option>
                                            <option value="option_price|asc" @if($sort == "option_price|asc") selected @endif>W 공급가 오름차순</option>
                                            <option value="md_price|desc" @if($sort == "md_price|desc") selected @endif>MD 판매가 내림차순</option>
                                            <option value="md_price|asc" @if($sort == "md_price|asc") selected @endif>MD 판매가 오름차순</option>
                                        </select>
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
                                        <button type="button" onclick="location.href='/product/list'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
                
                <div class="mt-3 d-flex justify-content-end">
                    <button class="btn btn-md btn-outline-primary me-2" id="btn-inspect-select">검수상태 변경</button>
                    <button class="btn btn-md btn-outline-danger me-2" id="btn-status-select">판매상태 변경</button>
                    <button class="btn btn-md btn-outline-dark me-2" id="btn-select">선택번역 요청</button>
                </div>
    
                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="text-center" style="width: 50px">
                                    <label class="form-check-label" for="allCheckbox">선택</label>
                                    <input class="form-check-input" type="checkbox" id="allCheckbox">
                                </th>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 50px">W</th>
                                <th scope="col" style="width: 150px">
                                    제품ID<br>
                                    (카테고리ID)
                                </th>
                                <th scope="col">제품명(국문)</th>
                                <th scope="col" style="width: 50px">최소 구매 수량</th>
                                <th scope="col" style="width: 100px">원본이미지</th>
                                <th scope="col" style="width: 100px">번역이미지</th>
                                <th scope="col" style="width: 150px" class="text-center">
                                    W 공급가<br>
                                    (환율: {{ number_format($exchangeRate) }}원)
                                </th>
                                <th scope="col" style="width: 120px" class="text-center">
                                    일반 판매가(원)
                                </th>
                                <th scope="col" style="width: 120px" class="text-center">
                                    MD 판매가(원)
                                </th>
                                <th scope="col" style="width: 100px" class="text-center">이미지<br>번역여부</th>
                                <th style="width: 100px" class="text-center">관리</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td class="text-center">
                                        <input class="form-check-input chk-inp" type="checkbox" value="{{ $data->offer_id }}">
                                    </td>
                                    <td>
                                        {{ number_format(($datas->total() - $offset) - $index) }}
                                    </td>
                                    <td>
                                        {{ $data->w_type }}
                                    </td>
                                    <td>
                                        <a href="https://detail.1688.com/offer/{{ $data->offer_id }}.html" target="_blank">{{ $data->offer_id }}</a>
                                        @if ($data->mapping_status == ProductConstant::MAPPING_STATUS_Y)
                                            <br>
                                            <span>({{ $data->category_id }})</span>
                                        @endif
                                        @if ($data->mapping_status == ProductConstant::MAPPING_STATUS_N)
                                            <button class="btn btn-sm btn-outline-success btn-modal mt-1" cateid={{ $data->category_id }}>맵핑하기</button>
                                            <br>
                                            <span class="text-danger">*카테고리 미맵핑</span>
                                            <span class="text-danger">({{ $data->category_id }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $data->prd_name_kr }}
                                        @if ($data->status != ProductConstant::PRD_STATUS_PUBLISH)
                                            <span class="bg-danger rounded text-white px-2 py-1 fs-6">{{ ProductConstant::PRD_STATUS[$data->status] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($data->start_quantity) }}
                                    </td>
                                    <td>
                                        <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_origin }}" width=60 height=60/>
                                    </td>
                                    <td>
                                        @if( $data->main_img->img_url_trans )
                                            <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_trans }}" width=60 height=60/>
                                        @else
                                            <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=60 height=60>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                                {{ $option->price_1688 }}(위안)<br>
                                                {{ number_format($option->option_price) }}(원)
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                                {{ number_format(calcWSalePrice($option->option_price)) }} 
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                            @if ($option->md_price)
                                                @php
                                                    $salePrice = calcWSalePrice($option->option_price); 
                                                    $saleHigh  = compareWSalePrice($salePrice, $option->md_price);
                                                @endphp
                                                @if ($saleHigh === true)
                                                    <button class="btn btn-sm btn-primary btn-md-modi" offerid={{ $data->offer_id }} saleprice={{ $salePrice }} mdprice={{ $option->md_price }}>{{ number_format($option->md_price) }}</button>
                                                @else
                                                    <button class="btn btn-sm btn-danger btn-md-modi" offerid={{ $data->offer_id }} saleprice={{ $salePrice }} mdprice={{ $option->md_price }}>{{ number_format($option->md_price) }}</button>
                                                @endif
                                            @else
                                                <button class="btn btn-sm btn-warning btn-md-modi" offerid={{ $data->offer_id }} saleprice={{ calcWSalePrice($option->option_price) }} mdprice=0>MD 가격 설정</button>
                                            @endif
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ ProductConstant::IMG_TRANS_STATUS[$data->trans_status] }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-success btn-detail" offerid={{ $data->offer_id }}>상세보기</button>
                                        <button class="btn btn-sm btn-outline-primary btn-trans-img mt-2" offerid={{ $data->offer_id }}>번역요청</button>
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
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel">카테고리 맵핑</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="chkCateIds[]" />
    
                        <div>
                            <div class="d-flex align-items-center">
                                <label class="fs-7">W 카테고리</label>
                            </div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100 cate-1688-list">
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col">
                                        <label class="fs-7">WApp 카테고리</label>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_first" level="1">
                                            <option value="">1차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_second" level="2">
                                            <option value="">2차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_third" level="3">
                                            <option value="">3차 분류</option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <select class="form-control select-opt-w" name="w_cate_fourth" level="4">
                                            <option value="">4차 분류</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">WApp 카테고리 키워드</label>
                                    </div>
                                    <div class="col">
                                        <input type="text" class="form-control" name="w_cate_keyword" placeholder="검색어를 입력하세요." value="">
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="text-left">
                                <button type="button" class="btn btn-primary btn-w-cate-search">검색</button>
                            </div>
                            <hr>

                            <table class="table table-white bg-white w-cate-table">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 200px">WApp 1차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 2차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 3차 카테고리</th>
                                        <th scope="col" style="width: 200px">WApp 4차 카테고리</th>
                                        <th scope="col" style="width: 200px">맵핑 코드</th>
                                        <th scope="col" style="width: 100px">맵핑</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel2">MD 판매가 설정</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="offer_ids[]" />
                        <input type="hidden" name="sale_price" />

                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">일반 판매가</label>
                                    </div>
                                    <div class="col sale-price">
                                        
                                    </div>
                                </div>
                            </div>
                            <hr>

                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">MD 판매가</label>
                                    </div>
                                    <div class="col">
                                        <input type="number" class="form-control" name="md_price" placeholder="MD 판매가를 입력하세요." value="">
                                        {{-- <p class="text-danger mt-3 md-danger" style="display: none">* MD 판매가는 일반 판매가 보다 높게 입력해야 합니다.</p> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-primary btn-md-price-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose2">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal3" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel3" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel3">판매 상태 변경</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="offer_ids[]" />

                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">판매 상태</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status1" value="{{ ProductConstant::PRD_STATUS_PUBLISH }}" checked>
                                            <label class="form-check-label" for="status1">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_PUBLISH] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status2" value="{{ ProductConstant::PRD_STATUS_STOP }}">
                                            <label class="form-check-label" for="status2">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_STOP] }}</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="status" id="status3" value="{{ ProductConstant::PRD_STATUS_EXCEPT }}">
                                            <label class="form-check-label" for="status3">{{ ProductConstant::PRD_STATUS[ProductConstant::PRD_STATUS_EXCEPT] }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-status-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose3">닫기</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal4" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel4" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="htmlModalLabel4">검수상태 변경</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="offer_ids[]" />

                        <div>
                            <div class="d-flex justify-content-evenly px-3">
                                <div class="row w-100">
                                    <div class="col-2">
                                        <label class="fs-7">이미지</label>
                                    </div>
                                    <div class="col d-flex justify-content-around">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_img_status" id="inspect_img_status1" value="{{ InspectConstant::IS_INSPECT_Y }}">
                                            <label class="form-check-label" for="inspect_img_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_img_status" id="inspect_img_status2" value="{{ InspectConstant::IS_INSPECT_N }}" checked>
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
                                            <input class="form-check-input" type="radio" name="inspect_prd_status" id="inspect_prd_status1" value="{{ InspectConstant::IS_INSPECT_Y }}">
                                            <label class="form-check-label" for="inspect_prd_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_prd_status" id="inspect_prd_status2" value="{{ InspectConstant::IS_INSPECT_N }}" checked>
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
                                            <input class="form-check-input" type="radio" name="inspect_gosi_status" id="inspect_gosi_status1" value="{{ InspectConstant::IS_INSPECT_Y }}">
                                            <label class="form-check-label" for="inspect_gosi_status1">검수완료</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="inspect_gosi_status" id="inspect_gosi_status2" value="{{ InspectConstant::IS_INSPECT_N }}" checked>
                                            <label class="form-check-label" for="inspect_gosi_status2">미완료</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary btn-inspect-save">저장</button>
                        <button type="button" class="btn btn-secondary htmlModalClose4">닫기</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $(".btn-md-modi").click(function(){
            let offerIds  = [$(this).attr("offerid")];
            let mdPrice   = Number($(this).attr("mdprice"));
            let salePrice = Number($(this).attr("saleprice"));

            $(".sale-price").html(`<p>${salePrice.toLocaleString('ko-KR')}원</p>`);
            $('input[name="offer_ids[]"]').val(offerIds);
            $("input[name=sale_price]").val(salePrice);
            $("input[name=md_price]").val(mdPrice);
            $("#htmlModal2").modal('show');
        });

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $('.btn-md-price-save').click(function(){
            let mdPrice   = Number($("input[name=md_price]").val());
            let salePrice = $("input[name=sale_price]").val();
            let offerIds  = $('input[name="offer_ids[]"]').val().split(",");

            let confirmTxt = "MD 판매자가를 설정하시겠습니까?";
            if( mdPrice != 0 && mdPrice <= salePrice ){
                confirmTxt = "MD 판매가가 일반 판매가보다 낮게 입력되었습니다.\n입력한 가격을 MD 판매가로 등록하시겠습니까?";
            }

            if(confirm(confirmTxt)){
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"        : "{{ route('w.product.mdPriceUpdate') }}",
                    "data"    : { offerIds, mdPrice },
                    beforeSend: function () {
                        $("#loadingOverlay").show();
                    },
                    complete  : function(xhr, status) {
                        $("#loadingOverlay").hide();
                    },
                    success : function (resp) {
                        alert(resp.msg);
                        location.reload();
                    },
                    error: function (request) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $(".btn-modal").click(function(){
            let cateId = $(this).attr("cateid");
            $("#loadingOverlay").show();

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('w.category.getInfos') }}",
                "data"    : { categoryIds: [cateId] },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(`.cate-1688-list`).html("");
                    let category_ids = [];
                    resp.data.cateResult.map(function(obj){
                        category_ids.push(obj.category_id);
                        $(`.cate-1688-list`).append(`<p>- ${obj.cate_name}</p>`)
                    });
                    $('input[name="chkCateIds[]"]').val(category_ids);

                    $(`.select-opt-w[level=1]`).html(`<option value="">1차 분류</option>`);
                    resp.data.wCateDepth1.map(function(obj){
                        $(`.select-opt-w[level=1]`).append(`<option value="${obj.cate_first}">${obj.cate_first}</option>`)
                    });
                    $("#htmlModal").modal('show');
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $('.select-opt-w').change(function(){
            let selectedLevel = parseInt($(this).attr('level'));

            if( selectedLevel < 4 ){
                let cate_name = "";
                $('.select-opt-w').each(function(key, ele) {
                    var level = parseInt($(this).attr('level'));
                    if (selectedLevel < level) {
                        $(this).html(`<option value="">${level}차 분류</option>`);
                    }
                    if (selectedLevel >= level) {
                        if( key == 0 ){
                            cate_name = $(this).val();
                        }else{
                            cate_name += "," + $(this).val();
                        }
                    }
                });

                if( cate_name != "" ){
                    $("#loadingOverlay").show();
                    $.ajax({
                        "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        "type"    : "POST",
                        "url"     : "{{ route('w.category.getWDepth') }}",
                        "data"    : { 
                            level    : selectedLevel,
                            cate_name: cate_name,
                        },
                        beforeSend: function () {},
                        complete  : function(xhr, status) {
                            $("#loadingOverlay").hide();
                        },
                        success : function (resp) {
                            $(`.select-opt-w[level=${selectedLevel+1}]`).html(`<option value="">${selectedLevel+1}차 분류</option>`);
                            if( selectedLevel == 1 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_second ){
                                        $(`.select-opt-w[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_second}">${obj.cate_second}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 2 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_third ){
                                        $(`.select-opt-w[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_third}">${obj.cate_third}</option>`)
                                    }
                                })
                            } else if( selectedLevel == 3 ){
                                resp.data.map(function(obj){
                                    if( obj.cate_fourth ){
                                        $(`.select-opt-w[level=${selectedLevel+1}]`).append(`<option value="${obj.cate_fourth}">${obj.cate_fourth}</option>`)
                                    }
                                })
                            }
                        },
                        error: function (request) {
                            let { error } = JSON.parse(request.responseText);
                            alert(error.message);
                        }
                    });
                }
            }
        });

        $(".btn-w-cate-search").click(function(){
            let cate_first = $("select[name=w_cate_first]").val();
            let cate_second    = $("select[name=w_cate_second]").val();
            let cate_third     = $("select[name=w_cate_third]").val();
            let cate_fourth    = $("select[name=w_cate_fourth]").val();
            let w_cate_keyword = $("input[name=w_cate_keyword]").val();
            
            if( cate_first == "" && w_cate_keyword == "" ){
                return alert("1차 분류 또는 검색어를 입력하세요.");
            }

            $("#loadingOverlay").show();
            
            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"    : "POST",
                "url"        : "{{ route('w.category.getW') }}",
                "data"    : { 
                    cate_first,
                    cate_second,
                    cate_third,
                    cate_fourth,
                    w_cate_keyword,
                },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $(".w-cate-table tbody").html("");

                    resp.data.map(function(obj){
                        $(`.w-cate-table tbody`).append(`
                            <tr>
                                <td>
                                    ${obj.cate_first}
                                </td>
                                <td>
                                    ${obj.cate_second}
                                </td>
                                <td>
                                    ${obj.cate_third}
                                </td>
                                <td>
                                    ${obj.cate_fourth}
                                </td>
                                <td>
                                    ${obj.mapping_code}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-save" value="${obj.id}">적용</button>
                                </td>
                            </tr
                        `);
                    });
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });

        });

        $(document).on('click', '.btn-save', function(){
            let category_ids = $('input[name="chkCateIds[]"]').val();
            let w_cate_id    = Number($(this).attr("value"));

            $("#loadingOverlay").show();
            $.ajax({
                "headers": {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"   : "POST",
                "url"    : "{{ route('w.category.wMapping') }}",
                "data"   : { 
                    category_ids,
                    w_cate_id
                },
                beforeSend: function () {},
                complete  : function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    alert(resp.msg);
                    location.reload();
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $(".btn-detail").click(function(){
            let offer_id = $(this).attr("offerid");
            location.href = `/product/${offer_id}`;
        });

        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $(".btn-status").click(function(){
            let name = $(this).attr("name");
            $(`input[name=${name}]`).val($(this).val());
            $("#searchFrm").submit();            
        });

        $(".btn-trans-img").click(function(){
            let offerIds = [$(this).attr("offerid")];

            if(confirm(`해당 상품을 번역 요청 하시겠습니까?`)){
                $("#loadingOverlay").show();

                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('genuio.imgTransRequest') }}",
                    "data"       : { offerIds },
                    beforeSend: function () {
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
        
        $("#btn-select").click(function(){
            let offerIds = [];

            $(".chk-inp:checked").each(function(index, element){
                offerIds.push($(this).val());
            });

            if(offerIds.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            if(confirm(`${offerIds.length}건의 상품을 번역 요청 하시겠습니까?`)){
                $("#loadingOverlay").show();

                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('genuio.imgTransRequest') }}",
                    "data"       : { offerIds },
                    beforeSend: function () {
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

        $("#btn-status-select").click(function(){
            let offerIds = [];

            $(".chk-inp:checked").each(function(index, element){
                offerIds.push($(this).val());
            });

            if(offerIds.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            $("#htmlModal3").modal('show');
        });

        $("#btn-inspect-select").click(function(){
            let offerIds = [];

            $(".chk-inp:checked").each(function(index, element){
                offerIds.push($(this).val());
            });

            if(offerIds.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            $("#htmlModal4").modal('show');
        });

        $(".btn-status-save").click(function(){
            let offerIds = [];

            $(".chk-inp:checked").each(function(index, element){
                offerIds.push($(this).val());
            });

            if(offerIds.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

            let status = $("input[name=status]:checked").val();

            if( confirm("판매상태를 변경 하시겠습니까?") ){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.statusUpdate') }}",
                    "data"       : { offerIds, status },
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

        $(".htmlModalClose3").click(function(){
            $("#htmlModal3").modal('hide');
        });

        $(".btn-inspect-save").click(function(){
            let offerIds = [];

            $(".chk-inp:checked").each(function(index, element){
                offerIds.push($(this).val());
            });

            if(offerIds.length < 1){
                return alert("선택 된 상품이 없습니다.");
            }

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

        $(".htmlModalClose4").click(function(){
            $("#htmlModal4").modal('hide');
        });
        
    })
</script>

@endsection
