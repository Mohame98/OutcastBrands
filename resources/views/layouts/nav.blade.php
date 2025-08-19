<nav class="main-nav">
	<div class="container">
		<div class="left-side-navigation">
			<a class="logo" href="/" title="Home">
				<span>
					&lt;Outcast&gt;
				</span>
				<span>
					&lt;Brands&gt;
				</span>
			</a>
			@auth
				@include('components.add-brand')
			@endauth
		</div>

		<div class="right-side-navigation">
			<div class="search-link-container">
				<x-nav-links href="{{ route('search') }}" :active="request()->is('search')" title="Search Brands">
					<i class="fa-solid fa-magnifying-glass"></i>
					<span class="search-text">Search</span>
				</x-nav-links>
			</div>
			<nav class="navigation-menu" aria-label="Navigation right">
				@guest
				<ul class="login-links">
					<li>
						<div class="modal-wrapper signup">
							<button 
								class="btn signup-btn modal-btn"
								aria-haspopup="Sign Up form" 
								aria-controls="signup-modal" 
								title="Sign Up"
								aria-expanded="false">
									<i class="fa-solid fa-user-plus"></i>
									Sign Up
							</button>
							<dialog id="signup-modal" class="signup-modal">
								@include('authentication.pages.signup')
							</dialog>
						</div>
					</li>

					<li>
						<div class="modal-wrapper signin">
							<button 
								class="btn signin-btn white-btn modal-btn"
								aria-haspopup="Sign In form" 
								aria-controls="signin-modal" 
								title="Log In"
								aria-expanded="false">
									<i class="fa-solid fa-arrow-right-to-bracket"></i> 
									Log In
							</button>
							<dialog id="signin-modal" class="signin-modal">
								@include('authentication.pages.signin')
							</dialog>
						</div>
					</li>

					
				</ul>
				<div class="btn mobile-menu-btn" onclick="mobileMenuToggle()">
					<i class="fa-solid fa-bars"></i>
				</div>
				<script>
					function mobileMenuToggle() {
						const menuLoginLinks = document.querySelector(".login-links");
						menuLoginLinks.classList.toggle("responsive");
						document.body.classList.toggle("menu-open");
					}
				</script>
				@endguest
				@auth
					<button
						class="profile-dropdown-menu-btn btn"
						id="profile-dropdown-menu-btn"
						popovertarget="profile-dropdown-menu"
						popovertargetaction="toggle"
						aria-haspopup="menu"
						aria-controls="profile-dropdown-menu"
						aria-expanded="false"
						title="Open profile menu"
					>
						@include('components.profile-image')
					</button>

					<nav
						id="profile-dropdown-menu"
						class="profile-dropdown-menu popover"
						popover
						aria-label="Profile dropdown menu"
					>
						<ul class="dropdown-menu">
							<li class="profile item">
								<a href="{{ route('profile.show', auth()->user()) }}" title="My Profile">
									<i class="fa-solid fa-address-card"></i>
									My Profile
								</a>
							</li>
							<li class="saved item">
								<a href="{{ route('profile.saved-brands') }}" title="Saved Brands">
									<i class="fa-solid fa-user-astronaut"></i>
									Saved Brands
								</a>
							</li>
							<li class="account-settings item">
								<a href="{{ route('account.edit') }}" title="Account Settings">
									<i class="fa-solid fa-user-gear"></i>
									Account Settings
								</a>
							</li>
							<li class="logout item">
								<form method="POST" action="{{ route('logout') }}" title="Logout">
									@csrf
									<button class="btn logout-btn" type="submit">
										<i class="fa-solid fa-arrow-right-to-bracket"></i>
										Log Out
									</button>
								</form>
							</li>
						</ul>
					</nav>
				@endauth
			</nav>
		</div>
	</div>
</nav>
