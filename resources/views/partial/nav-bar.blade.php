<nav
	class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
	id="layout-navbar">
	<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
		<a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
		<i class="bx bx-menu bx-sm"></i>
		</a>
		
	</div>

	<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
			<div class="flex-grow-1">
				Hi,{{Auth::user()->full_name ?? '' }}
				@if(Auth::user())
				<br>
				 <span> Login as a : <small class="text-muted">{{Auth::user()->getRoleNames()->first() ?? ''}}</small></span>
				 
				 @endif
			</div>
		
		<ul class="navbar-nav flex-row align-items-center ms-auto">
		<!-- Place this tag where you want the button to render. -->

		<!-- User -->
		<li class="nav-item navbar-dropdown dropdown-user dropdown">
			<a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
			<div class="avatar avatar-online">
				<img src="{{asset('backend/images/no-avatar.jpg')}}" alt class="w-px-40 h-auto rounded-circle" />
			</div>
			</a>
			<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<a class="dropdown-item" href="#">
				<div class="d-flex">
					<div class="flex-shrink-0 me-3">
					<div class="avatar avatar-online">
						<img src="{{asset('backend/images/no-avatar.jpg')}}" alt class="w-px-40 h-auto rounded-circle" />
					</div>
					</div>
					<div class="flex-grow-1">
					<span class="fw-medium d-block">{{Auth::user()->name ?? ''}}</span>
					<small class="text-muted">{{Auth::user()->username ?? ''}}</small>
					</div>
				</div>
				</a>
			</li>
			<li>
				<div class="dropdown-divider"></div>
			</li>

			<li>
				<a class="dropdown-item" href="{{route('change.account.information')}}">
				<i class="bx bx-user me-2"></i>
				<span class="align-middle">Account Information</span>
				</a>
			</li>
			<li>
				<a class="dropdown-item" href="{{route('admin.change.password')}}">
				<i class="bx bx-cog me-2"></i>
				<span class="align-middle">Change Password</span>
				</a>
			</li>

			<li>
				<div class="dropdown-divider"></div>
			</li>
			<li>
				<a class="dropdown-item" href="{{route('logout')}}" onclick="clearLocalStorageAndLogout(event)">
					<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
					  @csrf
					</form>
				<i class="bx bx-power-off me-2"></i>
				<span class="align-middle">Log Out</span>
				</a>
			</li>
			</ul>
		</li>
		<!--/ User -->
		</ul>
	</div>
	</nav>

<script type="text/javascript">
    function clearLocalStorageAndLogout(event) {
        event.preventDefault(); // Prevent the default action

        // Clear the local storage
        localStorage.clear();
        
        // Now submit the logout form
        document.getElementById('logout-form').submit();
    }
</script>