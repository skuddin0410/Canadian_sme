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
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="Dashboard">Overview</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('form-builder*') ? 'active open' : '' }} {{ request()->is('registration-settings*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="events">Registration</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('registration-settings*') ? 'active open' : '' }}">
                    <a href="{{ route("registration-settings") }}" class="menu-link">
                        <div data-i18n="events">Registration Settings</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('form-builder*') ? 'active open' : '' }}">
                    <a href="{{ route('form-builder.index') }}" class="menu-link">
                        <div data-i18n="events">Registration Forms</div>
                    </a>
                </li>
            </ul>
        </li>


        <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }} {{ request()->is('brand*') ? 'active open' : '' }} {{ request()->is('splash*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="events">Setup</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }}">
                    <a href="{{ route("events.edit",["event"=> 1 ]) }}" class="menu-link">
                        <div data-i18n="events">Basic Info</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('brand*') ? 'active open' : '' }} {{ request()->is('splash*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"  class="menu-link menu-toggle">
                        <div data-i18n="Booth Management">Branding</div>
                    </a>
                     <ul class="menu-sub">
                        <a href="{{route('brand')}}" class="menu-link {{ request()->is('brand*') ? 'active open' : '' }}" >
                           <div data-i18n="Booth Management">App Branding</div>
                        </a>
                        {{--  <a href="{{route('booths.index')}}" class="menu-link">
                           <div data-i18n="Booth Management">App Menu</div>
                        </a>--}}
                        <a href="{{route('splash')}}" class="menu-link {{ request()->is('splash*') ? 'active open' : '' }}">
                           <div data-i18n="Booth Management">Splash Screen</div>
                        </a>
                     </ul>
                </li>
            </ul>
        </li>
        

        <li class="menu-item {{ request()->is('attendee-users*') ? 'active open' : '' }} {{ request()->is('usergroup*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class=" menu-link menu-toggle">
                 <i class="menu-icon  fa fa-list"></i>
                <div data-i18n="tickets">People</div>
            </a>
            <ul class="menu-sub">
            <li class="menu-item {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }}">
                <a href="{{url('usergroup')}}" class="menu-link">
                    <div data-i18n="ticket-categories"> User Groups</div>
                </a>
            </li>
              <li class="menu-item {{ request()->is('attendee-users*') ? 'active' : '' }}">
                    <a href="{{ url('attendee-users') }}" class="menu-link">
                        <div data-i18n="Attendee">Attendee </div>
                    </a>
                </li>
            </ul>
        </li>


         <li class="menu-item {{ request()->is('admin-users*') ? 'active open' : '' }}  {{ request()->is('exhibitor-users*') ? 'active open' : '' }} {{ request()->is('speaker*') ? 'active open' : '' }} {{ request()->is('sponsors*') ? 'active open' : '' }}  {{ request()->is('categories*') ? 'active open' : '' }} {{ request()->is('webview*') ? 'active open' : '' }} {{ request()->is('calendar*') ? 'active open' : '' }} {{ request()->is('booths*') ? 'active open' : '' }} {{ request()->is('event-guides*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="events">Content</div>
            </a>

           <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('calendar*') ? 'active open' : '' }}">
                        <a href="{{ route('calendar.index') }}" class="menu-link">
                        
                            <div data-i18n="Schedules">Schedules </div>
                        </a>
                    </li>

                   {{--  <li class="menu-item {{ request()->is('admin-users*') ? 'active' : '' }}">
                        <a href="{{ url('admin-users') }}" class="menu-link">
                            <i class="menu-icon tf-icons fa fa-users  me-1"></i>
                            <div data-i18n="events">Team </div>
                        </a>
                    </li> --}}
                    <li class="menu-item {{ request()->is('speaker*') ? 'active open' : '' }}">
                       <a href="{{ url('speaker') }}" class="menu-link">
                        <div data-i18n="Users">Speaker </div>
                       </a>
                    </li>

                    <li class="menu-item {{ request()->is('exhibitor-users*') ? 'active open' : '' }}">
                       <a href="{{ url('exhibitor-users') }}" class="menu-link">
                        <div data-i18n="Users">Exhibitor </div>
                       </a>
                    </li>
                    
                     <li class="menu-item {{ request()->is('sponsors*') ? 'active open' : '' }}">
                       <a href="{{ url('sponsors') }}" class="menu-link">
                        <div data-i18n="Users">Sponsors </div>
                       </a>
                     </li>

                    <li class="menu-item {{ request()->is('categories*') ? 'active open' : '' }}">
                        <a href="{{ route('categories.index') }}" class="menu-link">
                        <div data-i18n="categories">Categories & Tags</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->is('webview*') ? 'active open' : '' }}">
                        <a href="{{ route('webview') }}" class="menu-link">
                            <div data-i18n="Coupons">Webview</div>
                        </a>
                    </li>
                     <li class="menu-item {{ request()->is('event-guides*') ? 'active open' : '' }}">
                        <a href="{{ url('event-guides') }}" class="menu-link">
                            <div data-i18n="Coupons">Event Guide</div>
                        </a>
                    </li>
                    <!-- <li class="menu-item {{ request()->is('booths*') ? 'active open' : '' }}">
                    <a href="{{route('booths.index')}}" class="menu-link">
                       <div data-i18n="Booth Management">Booths</div>
                    </a>
                    </li> -->

                </ul>

         </li>   

    

         <li class="menu-item {{ request()->is('tickets*') ? 'active open' : '' }} {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }} {{ request()->is('admin/ticket-types*') ? 'active open' : '' }} {{ request()->is('admin/ticket-inventory*') ? 'active open' : '' }} {{ request()->is('admin/ticket-pricing*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class=" menu-link menu-toggle">
                 <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="tickets">Tickets Managment</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }}">
                    <a href="{{ route('admin.ticket-categories.index') }}" class="menu-link">
                        <div data-i18n="ticket-categories"> Ticket categories</div>
                    </a>
                </li>
                 <li class="menu-item {{ request()->is('admin/ticket-types*') ? 'active open' : '' }}">
                    <a href="{{ route('admin.ticket-types.index') }}" class="menu-link">
                        <div data-i18n="ticket-types"> Ticket</div>
                    </a>
                </li>

            </ul>
        </li>
        
        <li class="menu-item {{ request()->is('leads*') ? 'active open' : '' }} {{ request()->is('newsletters*') ? 'active open' : '' }} {{ request()->is('audit*') ? 'active open' : '' }}  {{ request()->is('audit*') ? 'active open' : '' }} {{ request()->is('role-permission-matrix*') ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="formbuilder">Settings</div>
            </a>
            <ul class="menu-sub">

                <li class="menu-item {{ request()->is('leads*') ? 'active open' : '' }}">
                    <a href="{{route('leads.index')}}" class="menu-link">
                            <div data-i18n="Booth Management">Lead Management</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('newsletters*') ? 'active open' : '' }}">
                    <a href="{{ url('newsletters') }}" class="menu-link">
                        <div data-i18n="newsletters">Newsletter</div>
                    </a>
                </li>


               
                <li class="menu-item {{ request()->is('audit*') ? 'active open' : '' }}">
                    <a href="{{ route('audit.index') }}" class="menu-link">
                        <div data-i18n="audit">Audit</div>
                    </a>
                </li>
               

            
                {{-- <li class="menu-item {{ request()->is('role-permission-matrix*') ? 'active open' : '' }}">
                    <a href="{{ route('roles.matrix') }}" class="menu-link">
                        <div data-i18n="audit">Roles</div>
                    </a>
                </li> --}}
                
            </ul>
        </li>
       

    </ul>
</aside>
