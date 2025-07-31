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
        @if(Auth::user()->hasRole('Affiliate Manager'))
            <li class="menu-item {{ request()->is('affiliate/users') ? 'active open' : '' }}">
                <a href="{{ route('affiliate.users') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Users">Users</div>
                </a>
            </li>
        @endif   
        
        @if(Auth::user()->hasRole('Admin'))
         <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="{{ route('pages.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Pages</div>
            </a>
        </li>
       @endif

        @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }} {{ request()->is('categories*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="events">Events</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }}">
                    <a href="{{ route('events.index') }}" class="menu-link">
                        <div data-i18n="events">Events</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('categories*') ? 'active open' : '' }}">
                    <a href="{{ url('categories') }}" class="menu-link">
                        <div data-i18n="Categories">Categories</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif 
        

        @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('banners*') ? 'active open' : '' }}">
            <a href="{{ url('banners') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Banners">Banners</div>
            </a>
        </li>
        @endif

        @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('coupons*') ? 'active open' : '' }}">
            <a href="{{ url('coupons') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Coupons</div>
            </a>
        </li>
        @endif

 
       
       @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('users*') ? 'active open' : '' }} {{ request()->is('users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Blogs">Site Users</div>
            </a>
            <ul class="menu-sub">
                @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Accounts Manager'))
                <li class="menu-item {{ request()->is('users') ? 'active open' : '' }}">
                   <a href="{{ url('users') }}" class="menu-link">
                    <div data-i18n="Users">All Users</div>
                   </a>
                </li>
                @endif
          {{--       @if(Auth::user()->hasRole('Admin'))
                <li class="menu-item {{ request()->is('users/kyc') ? 'active open' : '' }}">
                   <a href="{{ url('users/kyc') }}" class="menu-link">
                    <div data-i18n="Users">KYC Uploaded Users</div>
                   </a>
                </li>
                @endif
                @if(Auth::user()->hasRole('Admin'))
                <li class="menu-item {{ request()->is('users/kyc/required') ? 'active open' : '' }}">
                    <a href="{{ url('users/kyc/required') }}" class="menu-link">
                        <div data-i18n="Categories">Pending KYC</div>
                    </a>
                </li>
                @endif --}} 
            </ul>
        </li>
        @endif
{{--         @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('affiliates*') ? 'active open' : '' }} {{ request()->is('affiliates*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Affiliate Managers">Affiliate Managers</div>
            </a>
            <ul class="menu-sub">
                
                <li class="menu-item {{ request()->is('affiliates') ? 'active open' : '' }}">
                   <a href="{{ url('affiliates') }}" class="menu-link">
                    <div data-i18n="affiliates">Lists</div>
                   </a>
                </li>
            </ul>
        </li> 
        @endif --}}
        
        @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('admin-users*') ? 'active open' : '' }} {{ request()->is('admin-users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Admin Users">Admin Users</div>
            </a>
            <ul class="menu-sub">
                
                <li class="menu-item {{ request()->is('admin-users') ? 'active open' : '' }}">
                   <a href="{{ url('admin-users') }}" class="menu-link">
                    <div data-i18n="admin-users">Lists</div>
                   </a>
                </li>
            </ul>
        </li>          
        @endif
        
        @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('testimonials*') ? 'active open' : '' }}">
            <a href="{{ url('testimonials') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Testimonials">Testimonials</div>
            </a>
        </li>
        @endif
        @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('faqs*') ? 'active open' : '' }}">
            <a href="{{ url('faqs') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="FAQs">FAQs</div>
            </a>
        </li>
        @endif
        @if(Auth::user()->hasRole('Admin'))
        <li class="menu-item {{ request()->is('home/settings') ? 'active open' : '' }} {{ request()->is('settings*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Settings">Settings</div>
            </a>
            <ul class="menu-sub">
               <li class="menu-item {{ request()->is('settings') ? 'active open' : '' }}">
                    <a href="{{ url('settings') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="Site Settings">Site settings</div>
                    </a>
                </li> 
                <li class="menu-item {{ request()->is('home/settings') ? 'active open' : '' }}">
                    <a href="{{ route('indexHome') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="Site Settings">Home page settings</div>
                    </a>
                </li>
            </ul>

         </li> 
         @endif     
    </ul>
</aside>