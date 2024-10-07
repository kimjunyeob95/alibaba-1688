@php
    use App\Constants\WmsConstant;
    use App\Constants\BonaeraConstant;
    use App\Models\ProductOptionData;
@endphp
@extends('dashboard.base')

@section('styles')
<style>
    .table th {
        background-color: #ebedef;
        color: #343a40;
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
                <li class="breadcrumb-item">WMS</li>
                <li class="breadcrumb-item">입고 관리</li>
                <li class="breadcrumb-item active" aria-current="page">입고 상세</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
                <div class="row mb-12">
                    <div class="text-center">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="4">입고정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col" style="width: 10%">입고번호</th>
                                    <td>{{ $data->stock_no }}</td>
                                    <th scope="col" style="width: 10%">W 주문번호</th>
                                    <td>{{ $data->order_id }}</td>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 10%">상품명</th>
                                    <td colspan="3"> (<a href="https://detail.1688.com/offer/{{ $data->offer_id }}.html" target="_blank">{{ $data->offer_id }}</a>) {{ $data->product->prd_name_kr }}</td>
                                </tr>
                                <tr>
                                    <th scope="col" style="width: 10%">배송정보</th>
                                    <td colspan="3">
                                        @if(!empty($data->logistics_last))
                                            {{ $data->logistics_last->logistics_bill_no }}
                                            {{ $data->logistics_last->logistics_company_name }}
                                            <button class="btn btn-sm btn-primary btn-delivery text-white" orderid={{ $data->order_id }}>배송정보조회</button>
                                        @else
                                            배송정보없음
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="9">입고상품 정보</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col" style="width: 10%">아이템코드</th>
                                    <th scope="col" style="width: 10%">옵션</th>
                                    <th scope="col" style="width: 6%">입고상태</th>
                                    <th scope="col" style="width: 10%">HS code</th>
                                    <th scope="col" style="width: 6%">신청</th>
                                    <th scope="col" style="width: 6%">입고</th>
                                    <th scope="col" style="width: 6%">폐기</th>
                                    <th scope="col" style="width: 6%">출고</th>
                                    <th scope="col" style="width: 6%">재고</th>
                                </tr>
                                @foreach ($data->in_options as $in_option)
                                    <tr>
                                        <td>{{ $in_option->it_code }}</td>
                                        <td>
                                            @php
                                                $w_option = ProductOptionData::where("id", $in_option->option_id)->first();
                                            @endphp
                                            @if (!empty($w_option))
                                                <small class="d-block mt-1">
                                                    @if ($w_option->sku_img_url)
                                                        <img class="lazy-img preview-image" data-src="{{ $w_option->sku_img_url }}" width=30 height=30/>
                                                    @else
                                                        <img class="lazy-img preview-image" data-src='/assets/img/no_img.png'width=30 height=30>
                                                    @endif
                                                    {{ $w_option->option_name_kr}}
                                                </small>
                                            @else
                                                <small class="d-block mt-1">
                                                    WApp에 옵션이 없음 option_id: {{ $in_option->option_id }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ( $in_option->status == BonaeraConstant::WAREHOUSE_STATUS_PENDING )
                                                <span class="bg-dark rounded text-white px-2 py-1 fs-6">{{ BonaeraConstant::WAREHOUSE_STATUS[$in_option->status] }}</span>
                                            @elseif ( $in_option->status == BonaeraConstant::WAREHOUSE_STATUS_RECEIVED )
                                                <span class="bg-success rounded text-white px-2 py-1 fs-6">{{ BonaeraConstant::WAREHOUSE_STATUS[$in_option->status] }}</span>
                                            @elseif ( $in_option->status == BonaeraConstant::WAREHOUSE_STATUS_DISPOSED )
                                                <span class="bg-danger rounded text-white px-2 py-1 fs-6">{{ BonaeraConstant::WAREHOUSE_STATUS[$in_option->status] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $in_option->hs_code }}</td>
                                        <td>{{ number_format($in_option->quantity) }}</td>
                                        <td>{{ number_format($in_option->received_qty) }}</td>
                                        <td>{{ number_format($in_option->discarded_qty) }}</td>
                                        <td>{{ number_format($in_option->shipped_qty) }}</td>
                                        <td>{{ number_format($in_option->received_qty - ($in_option->discarded_qty + $in_option->shipped_qty)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th colspan="3">입고상품 이미지</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th scope="col" style="width: 20%">아이템코드</th>
                                    <th scope="col" style="width: 60%">이미지</th>
                                    <th scope="col" style="width: 20%">메모</th>
                                </tr>
                                @foreach ($data->in_options as $in_option)
                                    <tr>
                                        <td>{{ $in_option->it_code }}</td>
                                        <td>
                                            @if (!empty($in_option->imgs->toArray()))    
                                                @foreach ($in_option->imgs as $img)
                                                    @if ($img)
                                                        <img class="lazy-img preview-image" data-src="{{ $img->img_url }}" width=50 height=50/>
                                                    @endif
                                                @endforeach
                                            @else
                                                입고이미지 없음
                                            @endif
                                        </td>
                                        <td>{{ $in_option->memo }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 mb-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-dark btn-update text-white" data-id={{ $data->id }}>입고정보 업데이트</button>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="htmlModal2" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <form id="modalFrm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="htmlModalLabel2">배송정보</h5>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="orderId" />
    
                            <div>
                                <div class="d-flex flex-column px-3 mt-3">
                                    <div class="mb-2">
                                        <div class="col">
                                            <label class="d-flex align-items-center w-100 ms-2">
                                                <span class="fs-5 fw-bold" style="width: 150px;">배송정보</span>
                                            </label>
                                        </div>
                                        <div class="col mt-2">
                                            <table class="table">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 33%">물류코드</th>
                                                        <th scope="col" style="width: 33%">배송사</th>
                                                        <th scope="col" style="width: 33%">운송장</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="delivery-tbody">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary btn-trace-delivery">배송추적</button>
                                    <button type="button" class="btn btn-secondary htmlModalClose2">닫기</button>
                                </div>
                                
                                <div class="div-trace">
                                    <div class="d-flex flex-column px-3 mt-3">
                                        <div class="mb-2">
                                            <div class="col">
                                                <label class="d-flex align-items-center w-100 ms-2">
                                                    <span class="fs-5 fw-bold" style="width: 150px;">추적정보</span>
                                                </label>
                                            </div>
                                            <div class="col mt-2">
                                                <table class="table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th scope="col" style="width: 10%">logisticsId</th>
                                                            <th scope="col" style="width: 70%">내용</th>
                                                            <th scope="col" style="width: 20%">처리시간</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="trace-tbody">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
<script type="text/javascript">
    $(document).ready(function() {

        $(".btn-update").click(function(){
            let ids = [$(this).data("id")];

            if(confirm(`입고정보를 업데이트 하시겠습니까?`)){
                $("#loadingOverlay").show();
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : "{{ route('w.wms.bonaeraInUpdate') }}",
                    "data"    : { ids: ids },
                    beforeSend: function () {},
                    complete  : function(xhr, status) {
                        $("#loadingOverlay").hide();
                    },
                    success : function (resp) {
                        alert(resp.msg);
                    },
                    error: function (request) {
                        let { error } = JSON.parse(request.responseText);
                        alert(error.message);
                    }
                });
            }
        });

        $(".htmlModalClose2").click(function(){
            $("#htmlModal2").modal('hide');
        });

        $('.btn-delivery').click(function(){
            let orderId = $(this).attr("orderid");

            $(".delivery-tbody").html("");
            $(".trace-tbody").html("");
            $("#htmlModal2 .div-trace").hide();
            $('input[name="orderId"]').val(orderId);

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/order/wapp/${orderId}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    let { logistics } = resp.data ?? [];
                    logistics?.map(function(ele, key) {
                        $('.delivery-tbody').append(`
                            <tr>
                                <td>${ele.logistics_code}</td>
                                <td>${ele.logistics_company_name}</td>
                                <td>${ele.logistics_bill_no}</td>
                            </tr>
                        `);
                    });

                    $("#htmlModal2").modal('show');
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $('.btn-trace-delivery').click(function(){
            let orderId = $('input[name="orderId"]').val();
            $('.trace-tbody').html("");

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "GET",
                "url"        : `/api/w/order/logistics/${orderId}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    if( resp.data?.length > 0 ){
                        $('#htmlModal2 .div-trace .trace-tbody').html("");
                        let logisticsId = resp.data[0].logisticsId;

                        resp.data.map(function(ele, key) {
                            let logisticsSteps = ele.logisticsSteps;

                            if( logisticsId != ele.logisticsId ){
                                $('.trace-tbody').append(`
                                    <tr style="height: 60px;" class="text-center">
                                        <th colspan=3>
                                        </th>
                                    </tr>
                                `);
                            }
                            logisticsSteps.map(function(ele2, key2) {
                                $('.trace-tbody').append(`
                                    <tr>
                                        <td>${ele.logisticsId}</td>
                                        <td>${ele2.remark}</td>
                                        <td>${ele2.acceptTime}</td>
                                    </tr>
                                `);
                            });
                        });

                        $("#htmlModal2 .div-trace").show();
                    } else {
                        return alert("추적 데이터가 없습니다.");
                    }
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });

        });
    });
</script>

@endsection
