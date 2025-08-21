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
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        <li class="menu-item {{ request()->is('company/details') ? 'active open' : '' }} {{ request()->is('booths*') ? 'active open' : '' }} {{ request()->is('company/media-gallery') ? 'active' : '' }} {{ request()->is('trainings') ? 'active' : '' }} {{ request()->is('company/media-gallery') ? 'active' : '' }} {{ request()->is('company/videos') ? 'active' : '' }} {{ request()->is('product-categories*') ? 'active' : '' }} {{ request()->is('products*') ? 'active open' : '' }} {{ request()->is('account-information*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-user-circle" style="font-size: 24px;"></i>
                <div data-i18n="events">Profile Overview</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('account-information*') ? 'active open' : '' }}">
                    <a href="{{route('change.account.information')}}" class="menu-link">
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
           <a href="{{ url('sponsors') }}" class="menu-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="menu-icon tf-icons bi bi-buildings-fill" viewBox="0 0 16 16">
              <path d="M15 .5a.5.5 0 0 0-.724-.447l-8 4A.5.5 0 0 0 6 4.5v3.14L.342 9.526A.5.5 0 0 0 0 10v5.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V14h1v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5zM2 11h1v1H2zm2 0h1v1H4zm-1 2v1H2v-1zm1 0h1v1H4zm9-10v1h-1V3zM8 5h1v1H8zm1 2v1H8V7zM8 9h1v1H8zm2 0h1v1h-1zm-1 2v1H8v-1zm1 0h1v1h-1zm3-2v1h-1V9zm-1 2h1v1h-1zm-2-4h1v1h-1zm3 0v1h-1V7zm-2-2v1h-1V5zm1 0h1v1h-1z"/>
            </svg>
            <div data-i18n="Users">Sponsors Managment</div>
           </a>
        </li>
        
         <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }} {{ request()->is('categories*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="menu-icon tf-icons bi bi-calendar2-event" viewBox="0 0 16 16">
                  <path d="M11 7.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                  <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/>
                  <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/>
                </svg>
                <div data-i18n="events">Events Managment</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('events*') ? 'active open' : '' }}">
                    <a href="{{ route('events.index') }}" class="menu-link">

                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="menu-icon tf-icons bi bi-calendar2-event-fill" viewBox="0 0 16 16">
                          <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5m9.954 3H2.545c-.3 0-.545.224-.545.5v1c0 .276.244.5.545.5h10.91c.3 0 .545-.224.545-.5v-1c0-.276-.244-.5-.546-.5M11.5 7a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
                        </svg>
                        <div data-i18n="events">Event Lists</div>
                    </a>
                </li>

                <li class="menu-item {{ request()->is('categories*') ? 'active open' : '' }}">
                    <a href="{{ route('categories.index') }}" class="menu-link">

                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="menu-icon tf-icons bi bi-calendar-event" viewBox="0 0 16 16">
                      <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z"/>
                      <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z"/>
                    </svg>
                    <div data-i18n="categories">Categories & Tags</div>
                    </a>
                </li>
            </ul>
        </li>

         <li class="menu-item {{ request()->is('tickets*') ? 'active open' : '' }} {{ request()->is('admin/ticket-categories*') ? 'active open' : '' }} {{ request()->is('admin/ticket-types*') ? 'active open' : '' }} {{ request()->is('admin/ticket-inventory*') ? 'active open' : '' }} {{ request()->is('admin/ticket-pricing*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class=" menu-link menu-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="menu-icon tf-icons bi bi-ticket-detailed" viewBox="0 0 16 16">
                  <path d="M4 5.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0 5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M5 7a1 1 0 0 0 0 2h6a1 1 0 1 0 0-2z"/>
                  <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5z"/>
                </svg>
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
        
        <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }} {{ request()->is('leads*') ? 'active open' : '' }} {{ request()->is('newsletters*') ? 'active open' : '' }} {{ request()->is('audit*') ? 'active open' : '' }}  {{ request()->is('audit*') ? 'active open' : '' }} {{ request()->is('role-permission-matrix*') ? 'active open' : '' }} {{ request()->is('form-builder*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="menu-icon tf-icons bi bi-controller" viewBox="0 0 16 16">
                  <path d="M11.5 6.027a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m2.5-.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m-6.5-3h1v1h1v1h-1v1h-1v-1h-1v-1h1z"/>
                  <path d="M3.051 3.26a.5.5 0 0 1 .354-.613l1.932-.518a.5.5 0 0 1 .62.39c.655-.079 1.35-.117 2.043-.117.72 0 1.443.041 2.12.126a.5.5 0 0 1 .622-.399l1.932.518a.5.5 0 0 1 .306.729q.211.136.373.297c.408.408.78 1.05 1.095 1.772.32.733.599 1.591.805 2.466s.34 1.78.364 2.606c.024.816-.059 1.602-.328 2.21a1.42 1.42 0 0 1-1.445.83c-.636-.067-1.115-.394-1.513-.773-.245-.232-.496-.526-.739-.808-.126-.148-.25-.292-.368-.423-.728-.804-1.597-1.527-3.224-1.527s-2.496.723-3.224 1.527c-.119.131-.242.275-.368.423-.243.282-.494.575-.739.808-.398.38-.877.706-1.513.773a1.42 1.42 0 0 1-1.445-.83c-.27-.608-.352-1.395-.329-2.21.024-.826.16-1.73.365-2.606.206-.875.486-1.733.805-2.466.315-.722.687-1.364 1.094-1.772a2.3 2.3 0 0 1 .433-.335l-.028-.079zm2.036.412c-.877.185-1.469.443-1.733.708-.276.276-.587.783-.885 1.465a14 14 0 0 0-.748 2.295 12.4 12.4 0 0 0-.339 2.406c-.022.755.062 1.368.243 1.776a.42.42 0 0 0 .426.24c.327-.034.61-.199.929-.502.212-.202.4-.423.615-.674.133-.156.276-.323.44-.504C4.861 9.969 5.978 9.027 8 9.027s3.139.942 3.965 1.855c.164.181.307.348.44.504.214.251.403.472.615.674.318.303.601.468.929.503a.42.42 0 0 0 .426-.241c.18-.408.265-1.02.243-1.776a12.4 12.4 0 0 0-.339-2.406 14 14 0 0 0-.748-2.295c-.298-.682-.61-1.19-.885-1.465-.264-.265-.856-.523-1.733-.708-.85-.179-1.877-.27-2.913-.27s-2.063.091-2.913.27"/>
                </svg>
                <div data-i18n="formbuilder">Settings</div>
            </a>
            <ul class="menu-sub">

                <li class="menu-item {{ request()->is('pages*') ? 'active open' : '' }}">
                    <a href="{{ route('pages.index') }}" class="menu-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="menu-icon tf-icons bi bi-file-earmark-richtext-fill" viewBox="0 0 16 16">
                          <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M7 6.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0m-.861 1.542 1.33.886 1.854-1.855a.25.25 0 0 1 .289-.047l1.888.974V9.5a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V9s1.54-1.274 1.639-1.208M5 11h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1m0 2h3a.5.5 0 0 1 0 1H5a.5.5 0 0 1 0-1"/>
                        </svg>
                        <div data-i18n="Coupons">Cms Pages</div>
                    </a>
                </li>

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
    

    </ul>
</aside>
