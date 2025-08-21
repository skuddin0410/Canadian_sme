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

        <li class="menu-item {{ request()->is('company/details') ? 'active open' : '' }} {{ request()->is('booths*') ? 'active open' : '' }} {{ request()->is('company/media-gallery') ? 'active' : '' }} {{ request()->is('trainings') ? 'active' : '' }} {{ request()->is('company/media-gallery') ? 'active' : '' }} {{ request()->is('company/videos') ? 'active' : '' }} {{ request()->is('product-categories*') ? 'active' : '' }} {{ request()->is('products*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div data-i18n="events">Profile Overview</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }}">
                    <a href="{{ route('events.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div data-i18n="events">Profile Information</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('company/details') ? 'active' : '' }}">
                    <a href="{{ route('company.details') }}" class="menu-link">
                        <i class="menu-icon tf-icons fa fa-building"></i>
                        <div data-i18n="events">Company Information</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('booths*') ? 'active' : '' }}">
                    <a href="{{route('booths.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons fa fa-address-book"></i>
                        <div data-i18n="Booth Management">Booth Management</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('company/media-gallery') ? 'active' : '' }}">
                    <a href="{{ route('company.media.gallery') }}" class="menu-link">
                         <i class="menu-icon tf-icons fa fa-file-image"></i>
                        <div data-i18n="Gallery">Image Gallery</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('company/videos') ? 'active' : '' }}">
                    <a href="{{ route('company.videos.gallery') }}" class="menu-link">
                        <i class="menu-icon tf-icons fa fa-file-video"></i>
                        <div data-i18n="Video Gallery">Video Gallery</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('trainings') ? 'active' : '' }}">
                    <a href="{{ route('trainings.index') }}" class="menu-link">
                         <i class="menu-icon tf-icons fa fa-building"></i>
                        <div data-i18n="Marketing Materials">Marketing Materials</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('product-categories*') ? 'active' : '' }}">
                    <a href="{{route('product-categories.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons fa fa-tags"></i>
                        <div data-i18n="Product Categories">Product Categories</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is('products*') ? 'active open' : '' }}">
                    <a href="{{ route('products.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons fa fa-list-alt"></i>
                        <div data-i18n="Products">Products</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ request()->is('admin-users') ? 'active open' : '' }}">
            <a href="{{ url('admin-users') }}" class="menu-link">
                <i class="menu-icon tf-icons fa fa-users"></i>
                <div data-i18n="events">Team Managment</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('attendee') ? 'active' : '' }}">
            <a href="{{ url('attendee-users') }}" class="menu-link">
                <i class="menu-icon tf-icons fa fa-user"></i>
                <div data-i18n="Attendee">Attendee Managment</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('exhibitor-users') ? 'active open' : '' }}">
           <a href="{{ url('exhibitor-users') }}" class="menu-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="menu-icon tf-icons bi bi-person-square" viewBox="0 0 16 16">
              <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
              <path d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1v-1c0-1-1-4-6-4s-6 3-6 4v1a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
            </svg>
            <div data-i18n="Users">Exhibitor Managment</div>
           </a>
        </li>
        <li class="menu-item {{ request()->is('speaker') ? 'active open' : '' }}">
           <a href="{{ url('speaker') }}" class="menu-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="menu-icon tf-icons bi bi-speaker" viewBox="0 0 16 16">
              <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
              <path d="M8 4.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4m0 3a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-3.5 1.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
            </svg>
            <div data-i18n="Users">Speaker Managment</div>
           </a>
        </li>

         <li class="menu-item {{ request()->is('speaker') ? 'active open' : '' }}">
           <a href="{{ url('speaker') }}" class="menu-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="menu-icon tf-icons bi bi-speaker" viewBox="0 0 16 16">
              <path d="M12 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
              <path d="M8 4.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5M8 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4m0 3a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m-3.5 1.5a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
            </svg>
            <div data-i18n="Users">Sponsors Managment</div>
           </a>
        </li>
        

         <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
            <a href="{{ route('pages.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Coupons">Pages</div>
            </a>
        </li>
         

      
        <li class="menu-item {{ request()->is('leads*') ? 'active open' : '' }}">
            <a href="{{route('leads.index')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-store-alt"></i>
                    <div data-i18n="Booth Management">Lead Management</div>
            </a>
        </li>

       

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


          <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }} {{ request()->is('categories*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="formbuilder">FormBuilder</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }}">
                    <a href="{{ route('form-builder.index') }}" class="menu-link">
                        <div data-i18n="events">FormBuilder</div>
                    </a>
                </li>
                
            </ul>
        </li>
       

   
        <li class="menu-item {{ request()->is('tickets*') ? 'active open' : '' }} {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }} {{ request()->is('admin/ticket-types*') ? 'active open' : '' }} {{ request()->is('admin/ticket-inventory*') ? 'active open' : '' }} {{ request()->is('admin/ticket-pricing*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="tickets">Tickets</div>
            </a>
            <ul class="menu-sub">
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

       
 
        <li class="menu-item {{ request()->is('users*') ? 'active open' : '' }} {{ request()->is('exhibitor-users*') ? 'active open' : '' }} {{ request()->is('speaker*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Blogs">Users</div>
            </a>
            <ul class="menu-sub">
                @if(Auth::user()->hasRole('Admin') )
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
    
    
        <li class="menu-item {{ request()->is('newsletters*') ? 'active open' : '' }}">
            <a href="{{ url('newsletters') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
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
       
  
    </ul>
</aside>
