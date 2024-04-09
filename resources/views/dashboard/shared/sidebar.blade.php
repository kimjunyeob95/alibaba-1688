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
        <li class="nav-title">상품</li>
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
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                상품 리스트
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/product/list">
                        1688 수집 상품 리스트
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
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
