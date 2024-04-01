@php
    use App\Constants\ProductConstant;
@endphp
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
                <li class="breadcrumb-item">상품 리스트</li>
                <li class="breadcrumb-item active" aria-current="page">1688 수집 상품 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col">
                {{-- <div class="d-flex justify-content-between mb-3">
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div> --}}
    
                <div class="table-responsive">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 100px">No</th>
                                <th scope="col" style="width: 150px">제품ID</th>
                                <th scope="col">제품명</th>
                                <th scope="col">제품명(번역)</th>
                                <th scope="col" style="width: 100px">원본이미지</th>
                                <th scope="col" style="width: 100px">번역이미지</th>
                                <th scope="col" style="width: 150px" class="text-center">
                                    1688 소비자가<br>
                                    옵션가격<br>
                                    온채널가<br>
                                    소비자가
                                </th>
                                <th scope="col" style="width: 100px">이미지<br>번역여부</th>
                                <th style="width: 100px" class="text-center">관리</th> 
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
                                                {{ $option->price_1688 }}(元)<br>
                                                {{ number_format($option->option_price) }}(원)<br>
                                                {{ number_format($option->onch_price) }}(원)<br>
                                                {{ number_format($option->cus_price) }}(원)<br>
                                        @else
                                            <p class="text-danger">옵션없음</p>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ ProductConstant::IMG_TRANS_STATUS[$data->trans_status] }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline-success btn-detail" offerid={{ $data->offer_id }}>상세</button>
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
