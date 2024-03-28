@php
    use App\Constants\LogConstant;
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
                <li class="breadcrumb-item">상품 수집 관리</li>
                <li class="breadcrumb-item active" aria-current="page">상품 수집 현황</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">
                
                <div class="table-responsive mt-3">
                    <table class="table table-white bg-white">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 50px">No</th>
                                <th scope="col" style="width: 70px">요청</th>
                                <th scope="col" style="width: 150px;">요청정보</th>
                                <th scope="col" style="width: 50px">상세내역</th>
                                <th scope="col" style="width: 50px">현황</th>
                                <th scope="col" style="width: 100px">요청일자</th>
                                <th scope="col" style="width: 100px">완료일자</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $index => $data)
                                <tr>
                                    <td>
                                        {{ number_format(($totalCnt - $offset) - $index) }}
                                    </td>
                                    <td>
                                        {{ LogConstant::COLLECT_API[$data->type] }}
                                    </td>
                                    <td>
                                        <p style="word-break: break-all;">{{ $data->payload }}</p>
                                    </td>
                                    <td>
                                        {{ number_format($data->log_count) }}건
                                    </td>
                                    <td>
                                        {{ LogConstant::COLLECT_STATUS[$data->status] }}
                                    </td>
                                    <td>
                                        {{ $data->created_at }}
                                    </td>
                                    <td>
                                        {{ $data->completed_at }}
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
        
    })
</script>

@endsection
