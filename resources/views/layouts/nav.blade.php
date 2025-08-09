<nav class="main-nav">
	<div class="container">
		<div class="left-side-navigation">
			<a class="logo" href="/">
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
				<x-nav-links href="{{ route('search') }}" :active="request()->is('search')">
					<i class="fa-solid fa-magnifying-glass"></i>
					<span class="search-text">Search</span>
				</x-nav-links>
			</div>
			<div class="desktop-nav" aria-label="Desktop navigation right">
				@guest
				<ul class="login-links">
					<li>
						<x-nav-links href="{{ route('login') }}" :active="request()->is('signin')">
							<i class="fa-solid fa-arrow-right-to-bracket"></i> 
							Sign In
						</x-nav-links>
					</li>

					<li>
						<x-nav-links href="{{ route('signup') }}" :active="request()->is('signup')">
							<i class="fa-solid fa-user-plus"></i>
							Sign Up
						</x-nav-links>
					</li>
				</ul>
				<div class="modal-wrapper menu">
          <button 
            class="btn mobile-menu-btn modal-btn"
            aria-haspopup="open mobile menu" 
            aria-controls="mobile-menu-modal" 
            aria-expanded="false">
            <i class="fa-solid fa-bars"></i>
          </button>

          <dialog id="mobile-menu-modal" class="mobile-menu-modal">
						 <header class="modal-headers">
							<h2>Login</h2>
							@include('components.close-modal')
						</header>
						<p>Log in to share your brand, customize your profile, and unlock exclusive features.</p>

           	<ul class="login-links mobile">
							<li>
								<x-nav-links href="{{ route('login') }}" :active="request()->is('signin')">
									<i class="fa-solid fa-arrow-right-to-bracket"></i> 
									Sign In
								</x-nav-links>
							</li>

							<li>
								<x-nav-links href="{{ route('signup') }}" :active="request()->is('signup')">
									<i class="fa-solid fa-user-plus"></i>
									Sign Up
								</x-nav-links>
							</li>
						</ul>
          </dialog>
        </div>
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
								<a href="{{ route('profile.show', auth()->user()) }}">
									<i class="fa-solid fa-address-card"></i>
									My Profile
								</a>
							</li>
							<li class="saved item">
								<a href="{{ route('profile.saved-brands') }}">
									<i class="fa-solid fa-user-astronaut"></i>
									Saved Brands
								</a>
							</li>
							<li class="account-settings item">
								<a href="{{ route('account.edit') }}">
									<i class="fa-solid fa-user-gear"></i>
									Account Settings
								</a>
							</li>
							<li class="logout item">
								<form method="POST" action="{{ route('logout') }}">
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
			</div>
		</div>
	</div>
</nav>
