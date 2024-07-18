
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
        <style>
            .full-height {
                height: 100vh;
                background-color: #f4f5f6;
            }
            .login-container {
                width: 100%;
                margin: auto;
                padding: 20px;
                background: white;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }
            .card-body h1 {
                font-size: 1.5rem;
                margin-bottom: 20px;
            }
        </style>
    </head>
    <body class="bg-light min-vh-100 d-flex flex-row align-items-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="login-container">
                        <img src="{{ url('/assets/img/logo.png') }}" alt="Logo" class="logo">
                        <form action="{{ route('wapp.admin.signIn') }}" method="POST" id="loginFrm">
                            @csrf
                            <input type="hidden" name="redirect_url" value="{{ $redirect_url }}" />

                            <div class="card-body">
                                <h1>WApp Login</h1>
                                @if ($errors->has('error'))
                                    <small class="text-danger mb-3 d-block">* {{ $errors->first('error') }}</small>
                                @endif
                                <div class="input-group mb-3">
                                    <span class="input-group-text">
                                        <svg class="icon">
                                            <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-user"></use>
                                        </svg>
                                    </span>
                                    <input class="form-control" type="text" value="{{ old('user_id') }}" placeholder="아이디를 입력하세요" id="user_id" name="user_id" required>
                                </div>

                                <div class="input-group mb-4">
                                    <span class="input-group-text">
                                        <svg class="icon">
                                            <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-lock-locked"></use>
                                        </svg>
                                    </span>
                                    <input class="form-control" type="password" value="{{ old('password') }}" placeholder="비밀번호를 입력하세요" id="password" name="password" required>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <button class="btn btn-primary px-4" type="button" id="login">Login</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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

        <script type="text/javascript">

            $(document).ready(function(){

                function form_submit() {
                    let user_id  = $("#user_id").val();
                    let password = $("#password").val();

                    if( user_id.trim() == "" ){
                        alert("아이디를 입력하세요.");
                        return $("#user_id").focus();
                    }
                    if( password.trim() == "" ){
                        alert("비밀번호를 입력하세요.");
                        return $("#password").focus();
                    }

                    $("#loginFrm").submit();
                }

                $('#password').keypress(function(event) {
                    if (event.which === 13) { // 13은 엔터 키
                        form_submit();
                    }
                });

                $('#login').click(function(){
                    form_submit();
                });
            });
        </script>
    </body>
</html>
