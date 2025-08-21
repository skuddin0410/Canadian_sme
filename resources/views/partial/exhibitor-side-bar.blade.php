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

   <li class="menu-item {{ request()->is('products*') ||  request()->is('service*') ? 'active open' : '' }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
        <div data-i18n="Products & Services">Products & Services</div>
    </a>

    <ul class="menu-sub active">
        <li class="menu-item {{ request()->is('product-categories*') ? 'active' : '' }}">
            <a href="{{route('product-categories.index')}}" class="menu-link">
                <div data-i18n="Product Categories">Product Categories</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('products*') ? 'active open' : '' }}">
            <a href="{{ route('products.index') }}" class="menu-link">
                <div data-i18n="Products">Products</div>
            </a>
        </li>


        <li class="menu-item {{ request()->is('service-categories*') ? 'active' : '' }}">
            <a href="{{route('service-categories.index')}}" class="menu-link">
                <div data-i18n="Service Categories">Service Categories</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('service*') ||  request()->is('service-categories*') ? 'active' : '' }} }}">
            <a href="{{ route('services.index') }}" class="menu-link">
                <div data-i18n="Service">Service</div>
            </a>
        </li>

      

        <li class="menu-item {{ request()->is('technical-specs*') ? 'active' : '' }}">
            <a href="#" class="menu-link">
                <div data-i18n="Technical Specs">Technical Specs</div>
            </a>
        </li>
    </ul>
</li>

    <li class="menu-item {{ request()->is('users*') ? 'active open' : '' }} {{ request()->is('users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Blogs">Team</div>
            </a>
            
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('exhibitor-representative') ? 'active' : '' }}">
                    <a href="{{ url('representative-users') }}" class="menu-link">
                        <div data-i18n="Representative">Representative</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('attendee') ? 'active' : '' }}">
                    <a href="{{ url('attendee-users') }}" class="menu-link">
                        <div data-i18n="Attendee">Attendee</div>
                    </a>
                </li>
            </ul>
           
    </li>

    <li class="menu-item {{ request()->is('branding/logo*') || request()->is('trainings*') || request()->is('company/media-gallery*') || request()->is('company/videos*') ? 'active open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
            <div data-i18n="Branding & Media">Branding & Media</div>
        </a>
        <ul class="menu-sub">

            {{-- Guidelines (you can update this route when implemented) --}}
            <li class="menu-item {{ request()->is('company/branding/banner') ? 'active' : '' }}">
                <a href="#" class="menu-link">
                    <div data-i18n="Banner Upload">Guidelines</div>
                </a>
            </li>

            {{-- Media Gallery --}}
            <li class="menu-item {{ request()->is('company/media-gallery') ? 'active' : '' }}">
                <a href="{{ route('company.media.gallery') }}" class="menu-link">
                    <div data-i18n="Gallery">Gallery</div>
                </a>
            </li>

            {{-- Video Gallery --}}
            <li class="menu-item {{ request()->is('company/videos') ? 'active' : '' }}">
                <a href="{{ route('company.videos.gallery') }}" class="menu-link">
                    <div data-i18n="Video Gallery">Video Gallery</div>
                </a>
            </li>

            {{-- Marketing Materials --}}
            <li class="menu-item {{ request()->is('trainings') ? 'active' : '' }}">
                <a href="{{ route('trainings.index') }}" class="menu-link">
                    <div data-i18n="Marketing Materials">Marketing Materials</div>
                </a>
            </li>

        </ul>
    </li>

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
                    <a href="{{ route('contacts.index') }}" class="menu-link">
                        <div data-i18n="Contact">Contact</div>
                    </a>
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