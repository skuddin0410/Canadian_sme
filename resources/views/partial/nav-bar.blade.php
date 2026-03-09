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

		@if(Auth::check() && (Auth::user()->hasRole('Admin')))
		@php
			$notifications = \App\Models\GeneralNotification::where('user_id', auth()->id())
				->orderBy('created_at', 'desc')
				->limit(20)
				->get();
			$notificationCount = \App\Models\GeneralNotification::where('user_id', auth()->id())
				->where('is_read', 0)
				->count();
		@endphp
		<!-- Notification -->
		<li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
			<a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
				<i class="bx bx-bell bx-sm"></i>
				@if($notificationCount > 0)
				<span class="badge bg-danger rounded-pill badge-notifications">{{ $notificationCount }}</span>
				@endif
			</a>
			<ul class="dropdown-menu dropdown-menu-end py-0">
				<li class="dropdown-menu-header border-bottom">
					<div class="dropdown-header d-flex align-items-center py-3">
						<h5 class="text-body mb-0 me-auto">Notifications</h5>
						<a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" title="Mark all as read"><i class="bx fs-4 bx-envelope-open"></i></a>
					</div>
				</li>
				<li class="dropdown-notifications-list scrollable-container">
					<ul class="list-group list-group-flush">
						@forelse($notifications as $notification)
						<li class="list-group-item list-group-item-action dropdown-notifications-item {{ $notification->is_read ? '' : 'unread-notification' }}">
							<div class="d-flex">
								<div class="flex-shrink-0 me-3">
									<div class="avatar">
										@if($notification->related_type == 'failed_login')
										<span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-error"></i></span>
										@else
										<span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-bell"></i></span>
										@endif
									</div>
								</div>
								<div class="flex-grow-1">
									<h6 class="mb-1">{{ $notification->title }}</h6>
									<p class="mb-0">{{ $notification->body }}</p>
									<small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
								</div>

							</div>
						</li>
						@empty
						<li class="list-group-item list-group-item-action dropdown-notifications-item">
							<div class="text-center p-3">
								<small class="text-muted">No new notifications</small>
							</div>
						</li>
						@endforelse
					</ul>
				</li>
			</ul>
		</li>
		<!--/ Notification -->
		@endif

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

	document.addEventListener('click', function(e) {
		const markAllBtn = e.target.closest('.dropdown-notifications-all');
		if (markAllBtn) {
			e.preventDefault();
			
			const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
			
			fetch('{{ route("notifications.markAllAsRead") }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': csrfToken,
					'Accept': 'application/json'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					const items = document.querySelectorAll('.unread-notification');
					items.forEach(item => {
						item.classList.remove('unread-notification');
					});
					
					updateNotificationCountDisplay();
				}
			})
			.catch(error => console.error('Error:', error));
		}
	});

	function updateNotificationCountDisplay() {
		const badge = document.querySelector('.badge-notifications');
		if (badge) {
			badge.style.display = 'none';
		}
	}
</script>