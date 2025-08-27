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
                <i class="menu-icon tf-icons bx bx-home-circle" style="font-size: 26px;"></i>
                <div data-i18n="Dashboard">Overview</div>
            </a>
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

                <li class="menu-item {{ request()->is('company/details') ? 'active' : '' }}">
                    <a href="{{ route('company.details') }}" class="menu-link">
                        <div data-i18n="events">Features</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('brand*') ? 'active open' : '' }} {{ request()->is('splash*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);"  class="menu-link menu-toggle">
                        <div data-i18n="Booth Management">Branding</div>
                    </a>
                     <ul class="menu-sub">
                        <a href="{{route('brand')}}" class="menu-link {{ request()->is('brand*') ? 'active' : '' }}" >
                           <div data-i18n="Booth Management">App Branding</div>
                        </a>
                         <a href="{{route('booths.index')}}" class="menu-link">
                           <div data-i18n="Booth Management">App Menu</div>
                        </a>

                        <a href="{{route('splash')}}" class="menu-link {{ request()->is('splash*') ? 'active' : '' }}">
                           <div data-i18n="Booth Management">Splash Screen</div>
                        </a>
                     </ul>
                </li>
            </ul>
        </li>
        

        <li class="menu-item {{ request()->is('attendee-users*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class=" menu-link menu-toggle">
                 <i class="menu-icon  fa fa-list"></i>
                <div data-i18n="tickets">People</div>
            </a>
            <ul class="menu-sub">
            <li class="menu-item {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }}">
                <a href="#" class="menu-link">
                    <i class="menu-icon tf-icons fas fa-user-friends"></i>
                    <div data-i18n="ticket-categories"> User Groups</div>
                </a>
            </li>
              <li class="menu-item {{ request()->is('attendee-users*') ? 'active' : '' }}">
                    <a href="{{ url('attendee-users') }}" class="menu-link">
                        <i class="menu-icon tf-icons fas fa-user"></i>
                        <div data-i18n="Attendee">Attendee </div>
                    </a>
                </li>
            </ul>
        </li>


         <li class="menu-item {{ request()->is('admin-users*') ? 'active open' : '' }} {{ request()->is('attendee-users*') ? 'active' : '' }} {{ request()->is('exhibitor-users*') ? 'active open' : '' }} {{ request()->is('speaker*') ? 'active open' : '' }} {{ request()->is('sponsors*') ? 'active open' : '' }} {{ request()->is('events*') ? 'active open' : '' }} {{ request()->is('categories*') ? 'active open' : '' }} {{ request()->is('pages*') ? 'active open' : '' }} {{ request()->is('calendar*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="events">Content</div>
            </a>

           <ul class="menu-sub">
                    <li class="menu-item {{ request()->is('calendar*') ? 'active' : '' }}">
                        <a href="{{ route('calendar.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons fa fa-calendar  me-1"></i>
                            <div data-i18n="Schedules">Schedules </div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->is('admin-users*') ? 'active' : '' }}">
                        <a href="{{ url('admin-users') }}" class="menu-link">
                            <i class="menu-icon tf-icons fa fa-users  me-1"></i>
                            <div data-i18n="events">Team </div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->is('exhibitor-users*') ? 'active' : '' }}">
                       <a href="{{ url('exhibitor-users') }}" class="menu-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="menu-icon tf-icons bi bi-person-square me-1" viewBox="0 0 16 16">
                          <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                          <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1v-1c0-1-1-4-6-4s-6 3-6 4v1a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                        </svg>
                        <div data-i18n="Users">Exhibitor </div>
                       </a>
                    </li>
                    <li class="menu-item {{ request()->is('speaker*') ? 'active' : '' }}">
                       <a href="{{ url('speaker') }}" class="menu-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="menu-icon tf-icons bi bi-speaker me-1" viewBox="0 0 16 16">
                          <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                          <path d="M8 4.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4m0 3a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-3.5 1.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                        </svg>
                        <div data-i18n="Users">Speaker </div>
                       </a>
                    </li>

                     <li class="menu-item {{ request()->is('sponsors*') ? 'active' : '' }}">
                       <a href="{{ url('sponsors') }}" class="menu-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="menu-icon tf-icons bi bi-buildings-fill  me-1" viewBox="0 0 16 16">
                          <path d="M15 .5a.5.5 0 0 0-.724-.447l-8 4A.5.5 0 0 0 6 4.5v3.14L.342 9.526A.5.5 0 0 0 0 10v5.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V14h1v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zM2 11h1v1H2zm2 0h1v1H4zm-1 2v1H2v-1zm1 0h1v1H4zm9-10v1h-1V3zM8 5h1v1H8zm1 2v1H8V7zM8 9h1v1H8zm2 0h1v1h-1zm-1 2v1H8v-1zm1 0h1v1h-1zm3-2v1h-1V9zm-1 2h1v1h-1zm-2-4h1v1h-1zm3 0v1h-1V7zm-2-2v1h-1V5zm1 0h1v1h-1z"/>
                        </svg>
                        <div data-i18n="Users">Sponsors </div>
                       </a>
                     </li>

                    <li class="menu-item {{ request()->is('categories*') ? 'active open' : '' }}">
                        <a href="{{ route('categories.index') }}" class="menu-link">

                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="menu-icon tf-icons bi bi-calendar-event me-1" viewBox="0 0 16 16">
                          <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                          <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                        </svg>
                        <div data-i18n="categories">Categories & Tags</div>
                        </a>
                    </li>

                    <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
                        <a href="{{ route('pages.index') }}" class="menu-link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="menu-icon tf-icons bi bi-file-earmark-richtext-fill me-1" viewBox="0 0 16 16">
                              <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M7 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0m-.861 1.542 1.33.886 1.854-1.855a.25.25 0 0 1 .289-.047l1.888.974V9.5a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V9s1.54-1.274 1.639-1.208M5 11h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1m0 2h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1"/>
                            </svg>
                            <div data-i18n="Coupons">Webview</div>
                        </a>
                    </li>

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

                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="menu-icon tf-icons bi bi-ticket" viewBox="0 0 16 16">
                          <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5z"/>
                        </svg>
                        <div data-i18n="ticket-categories"> Ticket categories</div>
                    </a>
                </li>
                 <li class="menu-item {{ request()->is('admin/ticket-types*') ? 'active open' : '' }}">
                    <a href="{{ route('admin.ticket-types.index') }}" class="menu-link">

                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="menu-icon tf-icons bi bi-database-add" viewBox="0 0 16 16">
                          <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0"/>
                          <path d="M12.096 6.223A5 5 0 0 0 13 5.698V7c0 .289-.213.654-.753 1.007a4.5 4.5 0 0 1 1.753.25V4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16c.536 0 1.058-.034 1.555-.097a4.5 4.5 0 0 1-.813-.927Q8.378 15 8 15c-1.464 0-2.766-.27-3.682-.687C3.356 13.875 3 13.373 3 13v-1.302c.271.202.58.378.904.525C4.978 12.71 6.427 13 8 13h.027a4.6 4.6 0 0 1 0-1H8c-1.464 0-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10q.393 0 .774-.024a4.5 4.5 0 0 1 1.102-1.132C9.298 8.944 8.666 9 8 9c-1.464 0-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777M3 4c0-.374.356-.875 1.318-1.313C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4"/>
                        </svg>
                        <div data-i18n="ticket-types"> Ticket</div>
                    </a>
                </li>

            </ul>
        </li>
        
        <li class="menu-item {{ request()->is('leads*') ? 'active open' : '' }} {{ request()->is('newsletters*') ? 'active open' : '' }} {{ request()->is('audit*') ? 'active open' : '' }}  {{ request()->is('audit*') ? 'active open' : '' }} {{ request()->is('role-permission-matrix*') ? 'active open' : '' }} {{ request()->is('form-builder*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                 <i class="menu-icon tf-icons fa fa-list" style="font-size: 24px;"></i>
                <div data-i18n="formbuilder">Settings</div>
            </a>
            <ul class="menu-sub">

                <li class="menu-item {{ request()->is('leads*') ? 'active open' : '' }}">
                    <a href="{{route('leads.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-store-alt"></i>
                            <div data-i18n="Booth Management">Lead Management</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('newsletters*') ? 'active open' : '' }}">
                    <a href="{{ url('newsletters') }}" class="menu-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="menu-icon tf-icons bi bi-envelope" viewBox="0 0 16 16">
                          <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
                        </svg>
                        <div data-i18n="newsletters">Newsletter</div>
                    </a>
                </li>


               
                <li class="menu-item {{ request()->is('audit*') ? 'active open' : '' }}">
                    <a href="{{ route('audit.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="audit">Audit</div>
                    </a>
                </li>
               

            
                <li class="menu-item {{ request()->is('role-permission-matrix*') ? 'active open' : '' }}">
                    <a href="{{ route('roles.matrix') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                        <div data-i18n="audit">Roles</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('form-builder*') ? 'active open' : '' }}">
                    <a href="{{ route('form-builder.index') }}" class="menu-link">
                          <i class="menu-icon tf-icons fa fa-plus"></i>
                        <div data-i18n="events">FormBuilder</div>
                    </a>
                </li>
                
            </ul>
        </li>
       

    </ul>
</aside>
