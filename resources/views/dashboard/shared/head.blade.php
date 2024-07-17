<header class="header header-sticky mb-4">
    <div class="container-fluid">
        <button class="header-toggler px-md-0 me-md-3" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()">
            <svg class="icon icon-lg">
                <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-menu"></use>
            </svg>
        </button>
        <div class="d-flex ms-auto">
            <button class="btn btn-outline-danger" id="btn-logout" type="button">
                <svg class="icon">
                    <use xlink:href="/vendors/@coreui/icons/svg/free.svg#cil-account-logout"></use>
                </svg>
                Logout
            </button>
        </div>
    </div>
</header>
