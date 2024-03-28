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
                <li class="breadcrumb-item">
                    <a href="/">상품 수집 관리</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">상품 수집 현황 상세</li>
            </ol>
        </nav>
        
        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
                <div class="row mt-3">
                    <div class="col">
                        <h5>[{{ LogConstant::COLLECT_API[$data->type] }}] [{{ LogConstant::COLLECT_STATUS[$data->status] }}]</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th scope="col" style="width: 150px">제품ID</th>
                                    <th scope="col" style="width: 150px">수집 성공 여부</th>
                                    <th scope="col" style="width: *">에러 메세지</th>
                                    <th scope="col" style="width: 200px">수집 일자</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->details as $detail)
                                    <tr class="text-center">
                                        <td>
                                            {{ $detail->offer_id }}
                                        </td>
                                        <td>
                                            {{ LogConstant::COLLECT_DETAIL_STATUS[$detail->is_collect] }}
                                        </td>
                                        <td>
                                            {{ $detail->msg }}
                                        </td>
                                        <td>
                                            {{ $detail->created_at }}
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
