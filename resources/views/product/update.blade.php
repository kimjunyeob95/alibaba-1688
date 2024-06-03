@php
    use App\Constants\ProductConstant;
    use App\Constants\WConstant;
    use App\Constants\ImageConstant;
    use App\Constants\GosiConstants;
    use App\Constants\ExceptConstant;
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
                <li class="breadcrumb-item">
                    <a href="/">상품 리스트</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">상품 수정</li>
            </ol>
        </nav>

        <div class="container-fluid">
            <div class="row my-4 bg-white py-3">
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
                        <div class="col-md-3 text-center">제품명(국문)</div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="prd_name_kr" placeholder="" value="{{ $prdObj->prd_name_kr }}">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-3 text-center">제품명(영문)</div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="prd_name_en" placeholder="" value="{{ $prdObj->prd_name_en }}">
                        </div>
                    </div>
                </div>

                <hr style="margin-top: 20px">
                <div class="row mt-3">
                    <div class="col">
                        <h5>[옵션]</h5>
                        <p class="text-danger">* 적용할 옵션을 선택하세요.</p>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-white bg-white">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th scope="col" class="text-center" style="width: 50px">
                                        <label class="form-check-label" for="allCheckbox">선택</label>
                                        <input class="form-check-input" type="checkbox" id="allCheckbox">
                                    </th>
                                    <th scope="col">skuID</th>
                                    <th scope="col">옵션명(중문)</th>
                                    <th scope="col">옵션명(국문)</th>
                                    <th scope="col">옵션명(영문)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prdObj->options as $option)
                                    <tr class="text-center">
                                        <td class="text-center">
                                            @if ($option->is_except == "N")
                                                <input class="form-check-input chk-inp" type="checkbox" value="{{ $option->id }}" checked>
                                            @else
                                                <input class="form-check-input chk-inp" type="checkbox" value="{{ $option->id }}">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $option->sku_id }}
                                        </td>
                                        <td>
                                            {{ $option->option_name }}
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="option_name_kr" placeholder="" value="{{ $option->option_name_kr }}">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" name="option_name_en" placeholder="" value="{{ $option->option_name_en }}">
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
                        <h5>[고시정보(국문)]</h5>
                        <p class="text-danger">* 상세 페이지에 노출할 정보를 선택하세요.</p>
                        <button type="button" class="btn btn-success btn-gosi-all">일괄 선택</button>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-white bg-white">
                            <tbody>
                                @foreach ($prdObj->notices as $gosiKey => $gosi)
                                    @php
                                        $disabled = false;
                                        if( $gosi->except_data != null && $gosi->except_data->is_except == ExceptConstant::IS_EXCEPT_Y ){
                                            $disabled = true;
                                        }
                                    @endphp

                                    @if ( $gosiKey % 4 == 0)
                                        <tr>
                                    @endif

                                    <th class="bg-light">
                                        <input @if($disabled) disabled @endif class="form-check-input chk-gosi-inp gosi-kr" type="checkbox" value="{{ $gosi->id }}" @if( $gosi->is_except == GosiConstants::IS_EXCEPT_N ) checked @endif>
                                        <input @if($disabled) disabled @endif type="text" class="form-control" name="attribute_name_kr" placeholder="" value="{{ $gosi->attribute_name_kr }}">
                                    </th>
                                    <td style="vertical-align: bottom">
                                        <input @if($disabled) disabled @endif type="text" class="form-control" name="attribute_value_kr" placeholder="" value="{{ $gosi->attribute_value_kr }}">
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
                        <p class="text-danger">* 상세 페이지에 노출할 정보를 선택하세요.</p>
                        <button type="button" class="btn btn-success btn-gosi-all">일괄 선택</button>
                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-white bg-white">
                            <tbody>
                                @foreach ($prdObj->notices as $gosiKey => $gosi)
                                    @php
                                        $disabled = false;
                                        if( $gosi->except_data != null && $gosi->except_data->is_except == ExceptConstant::IS_EXCEPT_Y ){
                                            $disabled = true;
                                        }
                                    @endphp

                                    @if ( $gosiKey % 4 == 0)
                                        <tr>
                                    @endif

                                    <th class="bg-light">
                                        <input @if($disabled) disabled @endif class="form-check-input chk-gosi-inp gosi-en" type="checkbox" value="{{ $gosi->id }}" @if( $gosi->is_except == GosiConstants::IS_EXCEPT_N ) checked @endif>
                                        <input @if($disabled) disabled @endif type="text" class="form-control" name="attribute_name_en" placeholder="" value="{{ $gosi->attribute_name_en }}">
                                    </th>
                                    <td style="vertical-align: bottom">
                                        <input @if($disabled) disabled @endif type="text" class="form-control" name="attribute_value_en" placeholder="" value="{{ $gosi->attribute_value_en }}">
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

                <div class="col text-center">
                    <button type="button" class="btn btn-primary btn-lg btn-save">저장</button>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript">

    $(document).ready(function(){
        var offer_id = "{{ $prdObj->offer_id }}";
        var w_type   = "{{ $prdObj->w_type }}";

        $('.btn-save').click(function(){
            let prd_name_kr = $("input[name=prd_name_kr]").val();
            let prd_name_en = $("input[name=prd_name_en]").val() ?? "";

            if( prd_name_kr == "" ){
                return alert("제품명(국문)을 입력하세요.");
            }
            if( prd_name_en == "" ){
                return alert("제품명(영문)을 입력하세요.");
            }

            let optionList = [];
            let gosiKrList = [];
            let gosiEnList = [];
            let validate   = true;
            let errorMsg   = "";

            @if ($prdObj->w_type == "W1")
                let endpoint = "{{ route('w.product.update') }}";
            @else
                let endpoint = "{{ route('w2.product.update') }}";
            @endif

            $(".chk-inp").each(function(index, element){
                let is_except = "Y";
                let option_name_kr = "";
                let option_name_en = "";

                if( $(this).is(":checked") ){
                    is_except = "N";

                    option_name_kr = $(this).parent().siblings('td').find('input[name="option_name_kr"]').val();
                    option_name_en = $(this).parent().siblings('td').find('input[name="option_name_en"]').val();

                    if( option_name_kr.trim() == "" ){
                        validate = false;
                        errorMsg = "선택 한 옵션의 옵션명(국문)을 입력하세요.";
                    }
                    if( option_name_en.trim() == "" ){
                        validate = false;
                        errorMsg = "선택 한 옵션의 옵션명(영문)을 입력하세요.";
                    }
                }
                optionList.push({
                    id            : $(this).val(),
                    is_except     : is_except,
                    option_name_kr: option_name_kr,
                    option_name_en: option_name_en,
                });
            });

            if( validate === false ){
                return alert(errorMsg);
            }

            $(".chk-gosi-inp.gosi-kr").each(function(index, element){
                let is_except          = "Y";
                let attribute_name_kr  = $(this).next('input[name="attribute_name_kr"]').val();
                let attribute_value_kr = $(this).parent().next('td').find('input[name="attribute_value_kr"]').val();

                if( $(this).is(":checked") ){
                    is_except = "N";
                }
                gosiKrList.push({
                    id                : $(this).val(),
                    is_except         : is_except,
                    attribute_name_kr : attribute_name_kr,
                    attribute_value_kr: attribute_value_kr,
                });
            });

            $(".chk-gosi-inp.gosi-en").each(function(index, element){
                let is_except          = "Y";
                let attribute_name_en  = $(this).next('input[name="attribute_name_en"]').val();
                let attribute_value_en = $(this).parent().next('td').find('input[name="attribute_value_en"]').val();

                if( $(this).is(":checked") ){
                    is_except = "N";

                }
                gosiEnList.push({
                    id                : $(this).val(),
                    is_except         : is_except,
                    attribute_name_en : attribute_name_en,
                    attribute_value_en: attribute_value_en,
                });
            });

            if( confirm("저장하시겠습니까?") ){
                $.ajax({
                    "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"    : "POST",
                    "url"     : endpoint,
                    "data"    : { 
                        offer_id,
                        prd_name_kr: prd_name_kr.trim(),
                        prd_name_en: prd_name_en.trim(),
                        optionList,
                        gosiKrList,
                        gosiEnList,
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

        $('.btn-gosi-all').click(function(){
            if( $(".chk-gosi-inp:checked").length > 0 ) {
                $(".chk-gosi-inp").not(":disabled").prop("checked", false);
            } else {
                $(".chk-gosi-inp").not(":disabled").prop("checked", true);
            }
        });

        $('.chk-gosi-inp').click(function(){
            let value = $(this).val();

            if( $(this).is(":checked") ){
                $(`.chk-gosi-inp[value=${value}]`).prop("checked", true);
            } else {
                $(`.chk-gosi-inp[value=${value}]`).prop("checked", false);
            }
        });
    })
</script>

@endsection
