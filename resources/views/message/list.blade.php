@php
    use App\Constants\MessageConstant;
    use App\Constants\MallConstant;

    $exchangeRate = config('1688_EXCHANGE_RATE', 200);
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
                <li class="breadcrumb-item">W</li>
                <li class="breadcrumb-item">메시지 관리</li>
                <li class="breadcrumb-item active" aria-current="page">메시지 리스트</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">

                <form id="searchFrm">
                    <input type="hidden" name="code" value={{ $code }}>

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">유형</th>
                                    <td colspan="2">
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="codeAll" name="code[]" value="" {{ $code == "" ? "checked" : "" }}>
                                                <label class="form-check-label" for="codeAll">전체</label>
                                            </div>
                                            @foreach (MessageConstant::MESSAGE_CODE_NAME as $key => $value)
                                            <div class="form-check form-check-inline me-2">
                                                <input class="form-check-input" type="checkbox" id="code{{ $key }}" name="code[]" value="{{ $key }}" {{ in_array($key, explode(",", $code)) ? "checked" : "" }}>
                                                <label class="form-check-label" for="code{{ $key }}">{{ $value }}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">검색</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="search_cls">
                                            <option value="order_id" @if($search_cls == "order_id") selected @endif>W 주문번호</option>
                                            <option value="channel_order_id" @if($search_cls == "channel_order_id") selected @endif>채널 주문번호</option>
                                        </select>
                                    </td>
                                    <td>
                                        <textarea class="form-control" id="keyword" name="keyword" placeholder="동시에 검색하려면 콤마(,) 혹은 엔터로 구분하여 입력 예) 552908136418,737834654023">{!! $keyword !!}</textarea>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">노출 수</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="pageSize">
                                            <option value=100 @if($pageSize == 100) selected @endif>100개 노출</option>
                                            <option value=200 @if($pageSize == 200) selected @endif>200개 노출</option>
                                            <option value=300 @if($pageSize == 300) selected @endif>300개 노출</option>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="3">
                                        <button type="button" class="btn btn-md btn-primary" id="form-submit">검색</button>
                                        <button type="button" onclick="location.href='{{ url()->current() }}'" class="btn btn-md btn-light btn-reset">초기화</button>
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
                                <th scope="col" style="width: 5%">No</th>
                                <th scope="col" style="width: 5%">코드타입</th>
                                <th scope="col" style="width: 10%">유형</th>
                                <th scope="col" style="width: 5%">채널</th>
                                <th scope="col" style="width: 20%">W주문번호</th>
                                <th scope="col" style="width: 20%">채널주문번호</th>
                                <th scope="col" style="width: 10%">생성 시간</th>
                                <th scope="col" style="width: 10%">payload</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paginator->items() as $index => $data)
                                <tr>
                                    <td>
                                        <label>
                                            {{ number_format(($paginator->total() - $offset) - $index) }}
                                        </label>
                                    </td>
                                    <td>
                                        <small>{{ $data->code }}</small>
                                    </td>
                                    <td>
                                        <small>{{ MessageConstant::MESSAGE_CODE_NAME[$data->code] }}</small>
                                    </td>
                                    <td>
                                        <small>{{ MallConstant::MALL_NAME[$data->order_base_obj->channel] }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->order_id }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->channel_order_id }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $data->created_at }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <button class="btn btn-sm btn-primary btn-log-detail text-white" log_id={{ $data->id }}>보기</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="htmlModal" tabindex="-1" role="dialog" aria-labelledby="htmlModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="htmlModalLabel">payload 정보</h5>
                        </div>
                        <div class="modal-body">
                            <div class="card">
                                <div class="card-body">
                                    <pre id="json-display" class="m-0"></pre>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-center">
                            <button type="button" class="btn btn-secondary htmlModalClose">닫기</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-center">
                {{ $paginator->links("vendor.pagination.bootstrap-4") }}
            </div>
        </div>
    </div>
<script type="text/javascript">
    $(document).ready(function() {
        $("#form-submit").click(function(){
            $("#searchFrm").submit();
        });

        $(".htmlModalClose").click(function(){
            $("#htmlModal").modal('hide');
        });

        $(".btn-log-detail").click(function(){
            let id = $(this).attr("log_id");

            $.ajax({
                "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type"       : "POST",
                "url"        : `/api/w/1688/message/${id}`,
                "data"       : {},
                beforeSend: function () {
                    $("#loadingOverlay").show();
                },
                complete: function () {
                    $("#loadingOverlay").hide();
                },
                success: function (resp) {
                    let json = JSON.parse(resp.data.request)
                    $("#json-display").text(JSON.stringify(json, null, 2));

                    $("#htmlModal").modal('show');
                },
                error: function error(request, status, _error) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);

                    location.reload();
                }
            });
        });

    });
</script>

@endsection
