
<!DOCTYPE html>

<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>WApp Admin</title>

        <link href="/css/style.css" rel="stylesheet">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="/vendors/simplebar/css/simplebar.css">
        <link rel="stylesheet" href="/css/vendors/simplebar.css">
        <link href="/vendors/@coreui/chartjs/css/coreui-chartjs.css" rel="stylesheet">
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

        <link href="/css/custom.css" rel="stylesheet">
        @yield('styles')
        @yield('scripts')
    </head>
    <body>
        <div id="loadingOverlay" style="display: none">
            <div id="loadingButtonContainer">
                <button class="btn btn-primary" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" aria-hidden="false"></span>
                    Loading...
                </button>
            </div>
        </div>

        @include('dashboard.shared.sidebar')

        <div class="wrapper d-flex flex-column min-vh-100 bg-light">

            @include('dashboard.shared.head')

            <div class="body flex-grow-1 px-3">
                <div>
                    <!-- Modal -->
                    <div class="modal" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <img src="" id="previewImage" class="img-fluid" alt="Preview">
                                </div>
                            </div>
                        </div>
                    </div>

                    @yield("content")
                </div>

            </div>

            @include('dashboard.shared.footer')
        </div>

        <iframe id="mainIfr" name="mainIfr" width="0" height="0"></iframe>

        <!-- CoreUI JavaScript -->
        <script src="/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
        <script src="/vendors/simplebar/js/simplebar.min.js"></script>
        <!-- Plugins and scripts required by this view-->
        <script src="/vendors/chart.js/js/chart.min.js"></script>
        <script src="/vendors/@coreui/chartjs/js/coreui-chartjs.js"></script>
        <script src="/vendors/@coreui/utils/js/coreui-utils.js"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>

        <!-- Swiper JS -->
        <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

        <script src="/js/common.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {
                $(".calendar").datepicker({
                    numberOfMonths: 1,
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    nextText: '다음 달',
                    prevText: '이전 달',
                    numberOfMonths: [1, 1],
                    showAnim: "slide",
                    showMonthAfterYear: true,
                    dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
                    monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                    onSelect: function(dateText, inst) {
                    }
                });

                $("#btn-logout").click(function(){
                    if(confirm("로그아웃 하시겠습니까?")){
                        location.href = "{{ route('wapp.admin.logout') }}"
                    }
                });
            });
        </script>
    </body>
</html>
