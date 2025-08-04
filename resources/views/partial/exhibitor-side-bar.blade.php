<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="" class="app-brand-link">
            <span class="app-brand-logo">
                <img src="{{asset('sme-logo.png')}}" alt="{{ config('app.name', 'SME') }}" width="20%">
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

         <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Products & Servicest</div>
            </a>
        </li>

         <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Booth Management</div>
            </a>
        </li>
        
        <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Branding & Media</div>
            </a>
        </li>
        
        {{-- <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Company</div>
            </a>
        </li> --}}
        <li class="menu-item {{ request()->is('company*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-building-house"></i>
                    <div data-i18n="Company">Company</div>
            </a>
            <ul class="menu-sub">

                <li class="menu-item {{ request()->is('company/details') ? 'active' : '' }}">
                    <a href="{{ route('company.details') }}" class="menu-link">
                        <div data-i18n="Details">Details</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('company/contacts') ? 'active' : '' }}">
                    <a href="{{ route('company.contacts') }}" class="menu-link">
                        <div data-i18n="Contact">Contact</div>
                    </a>
                </li>
                 {{-- Branding and Media Submenu --}}
        <li class="menu-item {{ request()->is('company/branding*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <div data-i18n="Branding & Media">Branding & Media</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('company/branding/logo') ? 'active' : '' }}">
                    <a href="" class="menu-link">
                        <div data-i18n="Logo Upload">Logo Management</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('company/branding/banner') ? 'active' : '' }}">
                    <a href="" class="menu-link">
                        <div data-i18n="Banner Upload">Guidelines</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('company/branding/gallery') ? 'active' : '' }}">
                    <a href="" class="menu-link">
                        <div data-i18n="Gallery">Gallery</div>
                    </a>
                </li>
            </ul>
        </li>

            </ul>
        </li>

        

        <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Export & Integration</div>
            </a>
        </li>

        

    </ul>
</aside>