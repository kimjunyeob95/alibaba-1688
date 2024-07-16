@php
    use App\Constants\OnchannelConstant;
    use App\Constants\EasySellConstant;
@endphp
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
                주문 관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/wapp/order/list">
                        주문 리스트
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

        <li class="nav-title">채널 관리</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                상품 전송 현황
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/easySell/product/list/{{ EasySellConstant::TYPE_W }}">
                        이지셀: 더블유
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/easySell/product/list/{{ EasySellConstant::TYPE_DROPHUB }}">
                        이지셀: Drop Hub
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/onchannel/product/list/{{ OnchannelConstant::PRD_CHANNEL }}">
                        온채널: 일반상품
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/onchannel/product/list/{{ OnchannelConstant::PRD_CHANNEL_PRIVATE }}">
                        온채널: 사입상품
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
                    <a class="nav-link" href="/easySell/category">
                        이지셀 카테고리 맵핑
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/onchannel/category">
                        온채널 카테고리 맵핑
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/category/send/mall">
                        전송 카테고리 관리
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-title">SAI</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                큐 관리
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/sai/queue/wapp">
                        WApp 큐 관리
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/sai/queue/onchannel">
                        온채널 큐 관리
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-title">W App Swagger</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                </svg>
                Swagger List
            </a>
            <ul class="nav-group-items">
                <li class="nav-item">
                    <a class="nav-link" href="/api/v1/w/swagger" target="_blank">
                        WApp Swagger
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/api/genuio/swagger" target="_blank">
                        SAI Swagger
                    </a>
                </li>
            </ul>
        </li>
    </ul>
    <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
</div>
