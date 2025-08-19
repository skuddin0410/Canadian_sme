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
        
        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
         <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="{{ route('pages.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Pages</div>
            </a>
        </li>
         
       @endif

       @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
       <li class="menu-item {{ request()->is('booths*') ? 'active open' : '' }}">
            <a href="{{route('booths.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store-alt"></i>
                    <div data-i18n="Booth Management">Booth Management</div>
            </a>
        </li>
        <li class="menu-item {{ request()->is('leads*') ? 'active open' : '' }}">
            <a href="{{route('leads.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store-alt"></i>
                    <div data-i18n="Booth Management">Lead Management</div>
            </a>
        </li>
       @endif
       
        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
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
                <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }}">
                    <a href="{{ route('categories.index') }}" class="menu-link">
                    <div data-i18n="events">Categories & Tags</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif 

          @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
        <li class="menu-item {{ request()->is('tickets*') ? 'active open' : '' }} {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }} {{ request()->is('admin/ticket-types*') ? 'active open' : '' }} {{ request()->is('admin/ticket-inventory*') ? 'active open' : '' }} {{ request()->is('admin/ticket-pricing*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="tickets">Tickets</div>
            </a>
            <ul class="menu-sub">
               {{--  <li class="menu-item {{ request()->is('tickets*') ? 'active open' : '' }}">
                    <a href="{{ route('ticket.dashboard') }}" class="menu-link">
                        <div data-i18n="tickets">Tickets Dashboard</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }}">
                    <a href="{{ route('admin.ticket-categories.index') }}" class="menu-link">
                        <div data-i18n="ticket-categories"> Ticket categories</div>
                    </a>
                </li>
                 <li class="menu-item {{ request()->is('admin/ticket-types*') ? 'active open' : '' }}">
                    <a href="{{ route('admin.ticket-types.index') }}" class="menu-link">
                        <div data-i18n="ticket-types"> Ticket Types</div>
                    </a>
                </li>

            </ul>
        </li>
        @endif
        

  <!--       @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
        <li class="menu-item {{ request()->is('coupons*') ? 'active open' : '' }}">
            <a href="{{ url('coupons') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Coupons</div>
            </a>
        </li>
        @endif -->

 
       
       @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
        <li class="menu-item {{ request()->is('users*') ? 'active open' : '' }} {{ request()->is('exhibitor-users*') ? 'active open' : '' }} {{ request()->is('speaker*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Blogs">Users</div>
            </a>
            <ul class="menu-sub">
                @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
                <li class="menu-item {{ request()->is('users') ? 'active open' : '' }}">
                   <a href="{{ url('users') }}" class="menu-link">
                    <div data-i18n="Users">All Users</div>
                   </a>
                </li>
                <li class="menu-item {{ request()->is('exhibitor-users') ? 'active open' : '' }}">
                   <a href="{{ url('exhibitor-users') }}" class="menu-link">
                    <div data-i18n="Users">Exhibitor</div>
                   </a>
                </li>
                <li class="menu-item {{ request()->is('speaker') ? 'active open' : '' }}">
                   <a href="{{ url('speaker') }}" class="menu-link">
                    <div data-i18n="Users">Speaker</div>
                   </a>
                </li>
                @endif
            </ul>
        </li>
        @endif
        
        @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
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
             <ul class="menu-sub">
                
                <li class="menu-item {{ request()->is('admin-users') ? 'active open' : '' }}">
                   <a href="{{ url('newsletters') }}" class="menu-link">
                    <div data-i18n="newsletters">Newsletter</div>
                   </a>
                </li>
            </ul>
        </li>   
        @endif
        

         @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
        <li class="menu-item {{ request()->is('audit*') ? 'active open' : '' }}">
            <a href="{{ route('audit.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="audit">Audit</div>
            </a>
        </li>
        @endif

         @if(Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Event Admin'))
        <li class="menu-item {{ request()->is('role-permission-matrix*') ? 'active open' : '' }}">
            <a href="{{ route('roles.matrix') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="audit">Roles</div>
            </a>
        </li>
        @endif
  
    </ul>
</aside>