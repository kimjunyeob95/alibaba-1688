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
                <li class="breadcrumb-item">상품 수집 관리</li>
                <li class="breadcrumb-item active" aria-current="page">기본 정보로 수집</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">
                <form id="searchFrm">
                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">상품 검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="productCollectionId">Paller ID</option>
                                            <option value="categoryId">Category ID</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                        <input type="text" class="form-control" id="keyword" name="keyword" placeholder="검색어 입력" value="">
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정렬</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="sort">
                                            <option value="monthSold|desc">판매량 내림차순</option>
                                            <option value="monthSold|asc">판매량 오름차순</option>
                                            <option value="price|desc">가격 내림차순</option>
                                            <option value="price|asc">가격 오름차순</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=50>50개 노출</option>
                                            <option value=30>30개 노출</option>
                                            <option value=20>20개 노출</option>
                                            <option value=10>10개 노출</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='/product/keywordQuery'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
    
                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 100px">No</th>
                                <th scope="col" style="width: 150px">제품ID</th>
                                <th scope="col">제품명</th>
                                <th scope="col">제품명(번역)</th>
                                <th scope="col" style="width: 100px">원본이미지</th>
                                <th scope="col" style="width: 100px">판매량(월)</th>
                                <th scope="col" style="width: 150px" class="text-center">
                                    1688 소비자가<br>
                                    옵션가격<br>
                                    온채널가<br>
                                    소비자가
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td>
                                        {{ number_format(($totalCnt - $offset) - $index) }}
                                    </td>
                                    <td>
                                        {{ $data->offer_id }}
                                    </td>
                                    <td>
                                        {{ $data->prd_name }}
                                    </td>
                                    <td>
                                        {{ $data->prd_name_trans }}
                                    </td>
                                    <td>
                                        <img class="lazy-img preview-image" data-src="{{ $data->main_img->img_url_origin }}" width=60 height=60/>
                                    </td>
                                    <td class="text-center">
                                        @if (count($data->options) > 0)
                                            @php
                                                $option = $data->options[0];
                                            @endphp
                                                {{ $option->price_1688 }}(元)<br>
                                                {{ number_format($option->option_price) }}(원)<br>
                                                {{ number_format($option->onch_price) }}(원)<br>
                                                {{ number_format($option->cus_price) }}(원)<br>
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{-- {{ $datas->links("vendor.pagination.bootstrap-4") }} --}}
            </div>
        </div>

    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $(".btn-detail").click(function(){
            let offer_id = $(this).attr("offerid");
            location.href = `/product/${offer_id}`;
        })
    })
</script>

@endsection
