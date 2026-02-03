<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
<div class="app-brand demo" style="display: flex; justify-content: center; align-items: center; width: 100%;">
    <a href="/" class="app-brand-link">
        <span class="app-brand-logo">
            <!-- <img src="{{asset('sme-logo.png')}}" alt="{{ config('app.name', 'SME') }}" width="70%"> -->
            <img style="margin-left: 12px; margin-top: 12px;" src="{{asset('eventzen-logo.svg')}}" alt="{{ config('app.name', 'SME') }}" width="50%">
        </span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
    </a>
</div>


    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->is('admin/home') ? 'active open' : '' }}">
            <a href="{{ route('home') }}" class="menu-link">
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="Dashboard">Overview</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('admin/form-builder*') ? 'active open' : '' }} {{ request()->is('admin/registration-settings*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="events">Registration</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('admin/registration-settings*') ? 'active open' : '' }}">
                    <a href="{{ route("registration-settings") }}" class="menu-link">
                        <div data-i18n="events">Registration Settings</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('admin/form-builder*') ? 'active open' : '' }}">
                    <a href="{{ route('form-builder.index') }}" class="menu-link">
                        <div data-i18n="events">Registration Forms</div>
                    </a>
                </li>
            </ul>
        </li>


        <li class="menu-item {{ request()->is('admin/events*') ? 'active open' : '' }} {{ request()->is('admin/brand*') ? 'active open' : '' }} {{ request()->is('admin/splash*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="events">Setup</div>
            </a>
            <ul class="menu-sub">
                <!-- <li class="menu-item {{ request()->is('admin/events*') ? 'active open' : '' }}">
                    <a href="{{ route("events.edit",["event"=> 1 ]) }}" class="menu-link">
                        <div data-i18n="events">Basic Info</div>
                    </a>
                </li> -->

                <li class="menu-item {{ request()->is('admin/events*') ? 'active open' : '' }}">
                    <a href="{{ route("events.index",) }}" class="menu-link">
                        <div data-i18n="events">Event Management</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('admin/brand*') ? 'active open' : '' }} {{ request()->is('admin/splash*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"  class="menu-link menu-toggle">
                        <div data-i18n="Booth Management">Branding</div>
                    </a>
                     <ul class="menu-sub">
                        <a href="{{route('brand')}}" class="menu-link {{ request()->is('admin/brand*') ? 'active open' : '' }}" >
                           <div data-i18n="Booth Management">App Branding</div>
                        </a>
                        {{--  <a href="{{route('booths.index')}}" class="menu-link">
                           <div data-i18n="Booth Management">App Menu</div>
                        </a>--}}
                        <a href="{{route('splash')}}" class="menu-link {{ request()->is('admin/splash*') ? 'active open' : '' }}">
                           <div data-i18n="Booth Management">Splash Screen</div>
                        </a>
                     </ul>
                </li>
            </ul>
        </li>
        

        <li class="menu-item {{ request()->is('admin/attendee-users*') ? 'active open' : '' }} {{ request()->is('admin/usergroup*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class=" menu-link menu-toggle">
                 <i class="menu-icon  fa fa-list"></i>
                <div data-i18n="tickets">People</div>
            </a>
            <ul class="menu-sub">
            <li class="menu-item {{ request()->is('admin/admin/ticket-categories*') ? 'active open' : '' }}">
                <a href="{{route('usergroup.index')}}" class="menu-link">
                    <div data-i18n="ticket-categories"> User Groups</div>
                </a>
            </li>
              <li class="menu-item {{ request()->is('admin/attendee-users*') ? 'active' : '' }}">
                    <a href="{{ route('attendee-users.index') }}" class="menu-link">
                        <div data-i18n="Attendee">Attendee </div>
                    </a>
                </li>
               <li class="menu-item {{ request()->is('admin/newbadges*') ? 'active' : '' }}">
                    <a href="{{ route('newbadges.index') }}" class="menu-link">
                        <div data-i18n="Badges">Badges </div>
                    </a>
                </li> 
            </ul>
        </li>


         <li class="menu-item {{ request()->is('admin/admin-users*') ? 'active open' : '' }}  {{ request()->is('admin/exhibitor-users*') ? 'active open' : '' }} {{ request()->is('admin/speaker*') ? 'active open' : '' }} {{ request()->is('admin/sponsors*') ? 'active open' : '' }}  {{ request()->is('admin/categories*') ? 'active open' : '' }} {{ request()->is('admin/webview*') ? 'active open' : '' }} {{ request()->is('admin/calendar*') ? 'active open' : '' }} {{ request()->is('admin/booths*') ? 'active open' : '' }} {{ request()->is('admin/event-guides*') ? 'active open' : '' }}  {{ request()->is('admin/event-guides.showGallery*') ? 'active open' : '' }}  {{ request()->is('admin/landing-page-settings*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="events">Content</div>
            </a>

           <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('admin/calendar*') ? 'active open' : '' }}">
                        <a href="{{ route('calendar.index') }}" class="menu-link">
                        
                            <div data-i18n="Schedules">Schedules </div>
                        </a>
                    </li>

                   {{--  <li class="menu-item {{ request()->is('admin/admin-users*') ? 'active' : '' }}">
                        <a href="{{ url('admin-users') }}" class="menu-link">
                            <i class="menu-icon tf-icons fa fa-users  me-1"></i>
                            <div data-i18n="events">Team </div>
                        </a>
                    </li> --}}
                    <li class="menu-item {{ request()->is('admin/speaker*') ? 'active open' : '' }}">
                       <a href="{{ route('speaker.index') }}" class="menu-link">
                        <div data-i18n="Users">Speaker </div>
                       </a>
                    </li>

                    <li class="menu-item {{ request()->is('admin/exhibitor-users*') ? 'active open' : '' }}">
                       <a href="{{ route('exhibitor-users.index') }}" class="menu-link">
                        <div data-i18n="Users">Exhibitor </div>
                       </a>
                    </li>
                    
                     <li class="menu-item {{ request()->is('admin/sponsors*') ? 'active open' : '' }}">
                       <a href="{{ route('sponsors.index') }}" class="menu-link">
                        <div data-i18n="Users">Sponsors </div>
                       </a>
                     </li>

                    <li class="menu-item {{ request()->is('admin/categories*') ? 'active open' : '' }}">
                        <a href="{{ route('categories.index') }}" class="menu-link">
                        <div data-i18n="categories">Categories & Tags</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->is('admin/webview*') ? 'active open' : '' }}">
                        <a href="{{ route('webview') }}" class="menu-link">
                            <div data-i18n="Coupons">Webview</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->is('admin/landing-page-settings*') ? 'active open' : '' }}">
                        <a href="{{ route('landing-page-settings') }}" class="menu-link">
                            <div data-i18n="Coupons">Landing Setting</div>
                        </a>
                    </li>

                     <li class="menu-item {{ request()->is('admin/event-guides*') ? 'active open' : '' }}">
                        <a href="{{ route('event-guides.index') }}" class="menu-link">
                            <div data-i18n="Coupons">Event Guide</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->is('admin/event-guides.showGallery*') ? 'active open' : '' }}">
                        <a href="{{route('event-guides.showGallery')}}" class="menu-link">
                            <div data-i18n="Gallery">Gallery</div>
                           
                        </a>
                    </li>
                    <!-- <li class="menu-item {{ request()->is('admin/booths*') ? 'active open' : '' }}">
                    <a href="{{route('booths.index')}}" class="menu-link">
                       <div data-i18n="Booth Management">Booths</div>
                    </a>
                    </li> -->

                </ul>

         </li>   

    

         <li class="menu-item {{ request()->is('admin/tickets*') ? 'active open' : '' }} {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }} {{ request()->is('admin/ticket-types*') ? 'active open' : '' }} {{ request()->is('admin/ticket-inventory*') ? 'active open' : '' }} {{ request()->is('admin/ticket-pricing*') ? 'active open' : '' }}">
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
        
        <li class="menu-item {{ request()->is('admin/leads*') ? 'active open' : '' }} {{ request()->is('admin/email-templates*') ? 'active open' : '' }} {{ request()->is('admin/audit*') ? 'active open' : '' }}  {{ request()->is('admin/audit*') ? 'active open' : '' }} {{ request()->is('admin/role-permission-matrix*') ? 'active open' : '' }} {{ request()->is('admin/user-connections*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="formbuilder">Settings</div>
            </a>
            <ul class="menu-sub">

                <li class="menu-item {{ request()->is('admin/leads*') ? 'active open' : '' }}">
                    <a href="{{route('leads.index')}}" class="menu-link">
                            <div data-i18n="Booth Management">Lead Management</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('admin/user-connections*') ? 'active open' : '' }}">
                    <a href="{{route('user-connections.index')}}" class="menu-link">
                            <div data-i18n="Booth Management">Connection Lead</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('admin/email-templates*') ? 'active open' : '' }}">
                    <a href="{{ route('email-templates.index') }}" class="menu-link">
                        <div data-i18n="newsletters">Email Template</div>
                    </a>
                </li>       
                <li class="menu-item {{ request()->is('admin/audit*') ? 'active open' : '' }}">
                    <a href="{{ route('audit.index') }}" class="menu-link">
                        <div data-i18n="audit">Audit</div>
                    </a>
                </li>
                
            </ul>
        </li>
        

        <li class="menu-item {{ request()->is('admin/supports') ? 'active open' : '' }}">
            <a href="{{ route('supports.index') }}" class="menu-link">
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="Support">Support</div>
            </a>
        </li>

    </ul>
</aside>
