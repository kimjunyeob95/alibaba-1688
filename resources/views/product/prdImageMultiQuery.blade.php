@php
    use App\Constants\CollectConstatnt;
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
                <li class="breadcrumb-item">W</li>
                <li class="breadcrumb-item">상품</li>
                <li class="breadcrumb-item">상품 수집 관리</li>
                <li class="breadcrumb-item active" aria-current="page">상품 Image로 수집</li>
            </ol>
        </nav>

        <div class="row my-4 bg-white py-3">
            <div class="col-12 mb-3">
                <form id="searchFrm">
                    <input type="hidden" id="imageIds" name="imageIds">

                    <div class="card">
                        <div class="card-header">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px">Image 등록</th>
                                    <td colspan="2">
                                        <input type="file" class="form-control" id="imgFile" accept="image/*" multiple>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">Image ID</th>
                                    <td colspan="2">
                                        <span id="span-imgId">{{ $imageId }}</span>
                                    </td>
                                </tr>
                                <tr class="align-middle">
                                    <th style="width: 120px">정렬</th>
                                    <td style="width: 200px">
                                        <select class="form-select" name="sort">
                                            <option value="monthSold|desc" @if($sort == "monthSold|desc") selected @endif>판매량 내림차순</option>
                                            <option value="monthSold|asc" @if($sort == "monthSold|asc") selected @endif>판매량 오름차순</option>
                                            <option value="price|desc" @if($sort == "price|desc") selected @endif>가격 내림차순</option>
                                            <option value="price|asc" @if($sort == "price|asc") selected @endif>가격 오름차순</option>
                                        </select>
                                    </td>
                                    <td colspan="2">
                                    </td>
                                </tr>
                                <tr class="align-middle text-left">
                                    <td colspan="6">
                                        <button type="button" onclick="location.href='/product/imageMultiQuery'" class="btn btn-md btn-light btn-reset">초기화</button>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h4>상품 수집 옵션</h4>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr class="align-middle">
                                    <th style="width: 120px;">미수집 상품</th>
                                    <td class="d-flex align-items-center">
                                        <div class="d-flex align-items-center me-4">
                                            <label class="bg-light px-2 py-1 rounded me-2">상품 수집</label>
                                            <div class="btn-group" role="group" aria-label="상품 수집">
                                                <input type="radio" class="btn-check" name="nCollectOption" id="nCollectNone" value="{{ CollectConstatnt::COLLECT_NONE }}" checked>
                                                <label class="btn btn-outline-primary" for="nCollectNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_NONE] }}</label>
                    
                                                <input type="radio" class="btn-check" name="nCollectOption" id="nCollect" value="{{ CollectConstatnt::COLLECT_PRODUCT }}">
                                                <label class="btn btn-outline-primary" for="nCollect">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_PRODUCT] }}</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <label class="bg-light px-2 py-1 rounded me-2">번역 요청</label>
                                            <div class="btn-group" role="group" aria-label="번역 요청">
                                                <input type="radio" class="btn-check" name="nTranslateOption" id="nTranslateNone" value="{{ CollectConstatnt::TRANSLATE_NONE }}" checked>
                                                <label class="btn btn-outline-primary" for="nTranslateNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_NONE] }}</label>
                    
                                                <input type="radio" class="btn-check" name="nTranslateOption" id="nTranslateAll" value="{{ CollectConstatnt::TRANSLATE_ALL }}">
                                                <label class="btn btn-outline-primary" for="nTranslateAll">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_ALL] }}</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="align-middle">
                                    <th style="width: 120px;">수집완료 상품</th>
                                    <td class="d-flex align-items-center">
                                        <div class="d-flex align-items-center me-4">
                                            <label class="bg-light px-2 py-1 rounded me-2">상품 수집</label>
                                            <div class="btn-group" role="group" aria-label="상품 수집">
                                                <input type="radio" class="btn-check" name="yCollectOption" id="yCollectNone" value="{{ CollectConstatnt::COLLECT_NONE }}" checked>
                                                <label class="btn btn-outline-primary" for="yCollectNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_NONE] }}</label>
                    
                                                <input type="radio" class="btn-check" name="yCollectOption" id="yCollect" value="{{ CollectConstatnt::COLLECT_PRODUCT }}">
                                                <label class="btn btn-outline-primary" for="yCollect">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::COLLECT_PRODUCT] }}</label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <label class="bg-light px-2 py-1 rounded me-2">번역 요청</label>
                                            <div class="btn-group" role="group" aria-label="번역 요청">
                                                <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateNone" value="{{ CollectConstatnt::TRANSLATE_NONE }}" checked>
                                                <label class="btn btn-outline-primary" for="yTranslateNone">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_NONE] }}</label>
                    
                                                <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateAll" value="{{ CollectConstatnt::TRANSLATE_ALL }}">
                                                <label class="btn btn-outline-primary" for="yTranslateAll">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_ALL] }}</label>

                                                <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateStatusY" value="{{ CollectConstatnt::TRANSLATE_STATUS_Y }}">
                                                <label class="btn btn-outline-primary" for="yTranslateStatusY">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_STATUS_Y] }}</label>

                                                <input type="radio" class="btn-check" name="yTranslateOption" id="yTranslateStatusN" value="{{ CollectConstatnt::TRANSLATE_STATUS_N }}">
                                                <label class="btn btn-outline-primary" for="yTranslateStatusN">{{ CollectConstatnt::COLLECT_NAME_KR[CollectConstatnt::TRANSLATE_STATUS_N] }}</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                            </table>
                            <div class="d-flex justify-content-start">
                                <button type="button" class="btn btn-md btn-outline-dark me-2" id="btn-select">선택상품 수집</button>
                                <button type="button" class="btn btn-md btn-outline-success" id="btn-all">전체상품 수집</button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
<script type="text/javascript">

    $(document).ready(function(){
        $("#imgFile").change(async function(){
            $("#loadingOverlay").show();

            let isValid = true;
            let formData = new FormData();

            for (let i = 0; i < this.files.length; i++) {
                let file = this.files[i];

                // 파일 타입 검사 (이미지 파일인지 확인)
                if (file.type.indexOf('image') == -1) {
                    isValid = false;
                    alert('이미지 파일만 업로드 가능합니다.');
                    break;
                }

                if (file.size > 300000) {
                    file = await resizeImage(file, 290 * 1024);
                }

                formData.append('imgFile[]', file);
            }

            if (!isValid) {
                // 파일이 유효하지 않을 경우, 필요한 초기화 작업 수행
                this.value = '';
                $("#imageIds").val('');
                $("#loadingOverlay").hide();
                return false;
            }

            $.ajax({
                "headers" : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                "type" : "POST",
                "url" : "{{ route('w.product.createImgId') }}",
                "data" : formData,
                "processData": false,
                "contentType": false,
                beforeSend : function () {},
                complete: function(xhr, status) {
                    $("#loadingOverlay").hide();
                },
                success : function (resp) {
                    $("#imageIds").val(resp.result);
                    $("#span-imgId").text(resp.result);
                },
                error: function (request) {
                    let { error } = JSON.parse(request.responseText);
                    alert(error.message);
                }
            });
        });

        $("#btn-all").click(function(){
            let imageIds         = $("#imageIds").val();
            let formData         = $("#searchFrm").serializeArray();
            let nCollectOption   = $('input[name=nCollectOption]:checked').val();
            let nTranslateOption = $('input[name=nTranslateOption]:checked').val();
            let yCollectOption   = $('input[name=yCollectOption]:checked').val();
            let yTranslateOption = $('input[name=yTranslateOption]:checked').val();

            // formData를 객체로 변환
            let formDataObject = {};
            formData.forEach(item => {
                formDataObject[item.name] = item.value;
            });

            // collectParams 객체 생성
            formDataObject.collectParams = {
                nCollectOption  : nCollectOption,
                nTranslateOption: nTranslateOption,
                yCollectOption  : yCollectOption,
                yTranslateOption: yTranslateOption
            };

            if( imageIds.trim() == "" ){
                return alert("이미지 ID가 없습니다. 이미지를 등록하세요.");
            }
            if( imageIds.trim().split(",").length < 1 ){
                return alert("이미지 ID가 없습니다. 이미지를 등록하세요.");
            }

            if(confirm(`해당 이미지ID로 상품을 수집하시겠습니까?`)){
                $.ajax({
                    "headers"    : {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    "type"       : "POST",
                    "url"        : "{{ route('w.product.collectImageQuery') }}",
                    "data"       : formDataObject,
                    beforeSend: function () {
                    },
                    complete: function () {
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
    })
</script>

@endsection
