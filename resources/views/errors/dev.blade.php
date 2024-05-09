
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
    </head>
    <body class="d-flex justify-content-center align-items-center" style="height: 100vh; background-color: #f4f5f6;">
        <div class="text-center">
            <h1 class="text-primary">WApp 업데이트 중...</h1>
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>

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
    </body>
</html>
