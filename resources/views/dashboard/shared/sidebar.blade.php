@php
    use App\Constants\NavConstant;
    use App\Constants\AdminConstant;
    $adminLevel  = session('adminInfo')['level'] ?? AdminConstant::PUBLIC;
    $excludedNav = AdminConstant::EXCLUDED_NAV[$adminLevel] ?? [];
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
        <li class="nav-item">
            <a class="nav-link" target="_blank" href="https://www.notion.so/sellerhub/WApp-1688-Project-486f687e8485477994794335ddb5d1f3">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-link"></use>
                </svg> WApp 노션
            </a>
        </li>

        @foreach (NavConstant::NAV_LIST as $depth1Name => $depth1)
            @if (!in_array($depth1Name, $excludedNav))
                <li class="nav-title">{{ $depth1Name }}</li>
                @foreach ($depth1 as $depth2Name => $depth2)
                    @if (!in_array($depth2Name, $excludedNav))
                        @if (is_array($depth2))
                            <li class="nav-group">
                                <a class="nav-link nav-group-toggle" href="javascript:;">
                                    <svg class="nav-icon">
                                        @if (isset(NavConstant::NAV_ICON[$depth2Name]))
                                            <use xlink:href="{{ NavConstant::NAV_ICON[$depth2Name] }}"></use>
                                        @else
                                            <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                                        @endif
                                    </svg>
                                    {{ $depth2Name }}
                                </a>
                                <ul class="nav-group-items">
                                    @foreach ($depth2 as $depth3Name => $depth3)
                                        @if(!in_array($depth3Name, $excludedNav))
                                            @if (is_array($depth3))
                                                <li class="nav-group">
                                                    <a class="nav-link nav-group-toggle" href="javascript:;">
                                                        <svg class="nav-icon">
                                                            @if (isset(NavConstant::NAV_ICON[$depth3Name]))
                                                                <use xlink:href="{{ NavConstant::NAV_ICON[$depth3Name] }}"></use>
                                                            @else
                                                                <use testt="{{ $depth3Name}}" xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                                                            @endif
                                                        </svg>
                                                        {{ $depth3Name }}
                                                    </a>
                                                    <ul class="nav-group-items">
                                                        @foreach ($depth3 as $depth4Name => $depth4)
                                                            @if (!in_array($depth4Name, $excludedNav))
                                                                <li class="nav-item">
                                                                    <a class="nav-link" href="{{ $depth4 }}">
                                                                        {{ $depth4Name }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @else
                                                @if (!in_array($depth3Name, $excludedNav))
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="{{ $depth3 }}">
                                                            {{ $depth3Name }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endif
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            @if (!in_array($depth2Name, $excludedNav))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $depth2 }}">
                                        <svg class="nav-icon">
                                            @if (isset(NavConstant::NAV_ICON[$depth2Name]))
                                                <use xlink:href="{{ NavConstant::NAV_ICON[$depth2Name] }}"></use>
                                            @else
                                                <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-3d"></use>
                                            @endif
                                        </svg>
                                        {{ $depth2Name }}
                                    </a>
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach
            @endif
        @endforeach

        <li class="nav-title">W App Swagger</li>
        <li class="nav-group">
            <a class="nav-link nav-group-toggle" href="javascript:;">
                <svg class="nav-icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-link"></use>
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
