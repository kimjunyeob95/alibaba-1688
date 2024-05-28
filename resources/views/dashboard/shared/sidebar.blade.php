<div class="sidebar sidebar-dark sidebar-fixed" id="sidebar">
    <div class="sidebar-brand d-none d-md-flex">
        <img class="sidebar-brand-full" src="{{ url('/assets/img/logo.png') }}" width="118">
        <img class="sidebar-brand-narrow" src="{{ url('/assets/img/logo_icon.png') }}" width="30">
    </div>
    <ul class="sidebar-nav" data-coreui="navigation" data-simplebar="">
        <li class="nav-item">
            <a class="nav-link" href="/">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-speedometer"></use>
                </svg> Dashboard
            </a>
        </li>

        <li class="nav-title">W</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                상품
            </a>
            <ul class="nav-group-items">
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="javascript:;">
                        <svg class="nav-icon">
                            <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                        </svg>
                        상품 수집 관리
                    </a>
                    <ul class="nav-group-items">
                        <li class="nav-item">
                            <a class="nav-link" href="/product/queryProductDetail">
                                상품 ID로 수집
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/product/keywordQuery">
                                기본 정보로 수집
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/product/urlQuery">
                                상품상세 URL로 수집
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/product/imageQuery">
                                상품 단일 Image로 수집
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/product/imageMultiQuery">
                                상품 멀티 Image로 수집
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/product/collectLogs">
                                상품 수집 현황
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>

        {{-- <li class="nav-title">W2</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                상품
            </a>
            <ul class="nav-group-items">
                <li class="nav-group">
                    <a class="nav-link nav-group-toggle" href="javascript:;">
                        <svg class="nav-icon">
                            <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                        </svg>
                        상품 수집 관리
                    </a>
                    <ul class="nav-group-items">
                        <li class="nav-item">
                            <a class="nav-link" href="/product/w2/queryProductDetail">
                                상품 ID로 수집
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/product/w2/collectLogs">
                                상품 수집 현황
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li> --}}

        <li class="nav-title">W App</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                상품 관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/product/list">
                        전체 상품
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/product/except/list">
                        판매제외 상품 리스트
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                카테고리 관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/category">
                        맵핑 관리
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/category/weight/list">
                        표준 중량(배송비) 관리
                    </a>
                </li>
            </ul>
        </li>
        
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                금칙어 관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/forbiddenWord/list">
                        상품정보 관리
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/forbiddenWord/notice/list">
                        정보고시 관리
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                제외 관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/except/notice/list">
                        정보고시 관리
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-title">이지셀</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                상품관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/easySell/product/list">
                        상품 현황
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="/easySell/category">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                카테고리 관리
            </a>
        </li>
        <li class="nav-title">온채널</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                상품관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/onchannel/product/list">
                        상품 현황
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
