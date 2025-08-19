<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            <span class="app-brand-logo">
                <img src="{{asset('sme-logo.png')}}" alt="{{ config('app.name', 'SME') }}" width="55%">
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->is('home') ? 'active open' : '' }}">
            <a href="{{ url('home') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li> 



        <li class="menu-item {{ request()->is('company*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-building-house"></i>
                    <div data-i18n="User Lookup">User Lookup</div>
            </a>
           
            <ul class="menu-sub">

                <li class="menu-item {{ request()->is('exhibitor-users') ? 'active' : '' }}">
                    <a href="{{ url('exhibitor-users') }}" class="menu-link">
                        <div data-i18n="Exhibitor Users List"> Exhibitor</div>
                    </a>
                </li>
            </ul>
            
             <ul class="menu-sub">

                <li class="menu-item {{ request()->is('speaker') ? 'active' : '' }}">
                    <a href="{{ url('speaker') }}" class="menu-link">
                        <div data-i18n="Speaker List">Speaker</div>
                    </a>
                </li>
            </ul>
            
        </li>


    </ul>
</aside