<nav class="main-nav">
	<div class="container">

		<div class="left-side-navigation">
			<a class="logo" href="/"><h1>Western Outlaw</h1></a>
			<nav class="desktop-nav" aria-label="Desktop navigation left">
				<ul>
					<li><a href="">Popular</a></li>
					<li><a href="">Newest</a></li>
				</ul>
			</nav>
		</div>

		<div class="right-side-navigation">
			<div class="search-container">
				<div class="modal-wrapper">
					<button class="btn modal-btn" title="Search" aria-haspopup="Search Bar" aria-controls="search-modal" aria-expanded="false">
						<i class="fa-solid fa-magnifying-glass"></i>
					</button>

					<dialog id="search-modal" class="search-modal">
						<form action="/search" method="GET">
							<fieldset>
								<header>
									<legend>Search</legend>
								</header>
								<input type="text" name="query" placeholder="Search...">
							</fieldset>
        		</form>
					</dialog>
				</div>
			</div>
			<nav class="desktop-nav" aria-label="Desktop navigation right">
				@guest
				<ul>
					<li><x-nav-links href="{{ route('login') }}" :active="request()->is('login')"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In</x-nav-links></li>
				</ul>
				@endguest
				@if(auth()->check() && auth()->user()->hasVerifiedEmail())
				@endif

				@auth
					<button style="background-image: url('{{ asset('storage/' . auth()->user()->profile_image) }}')" popovertarget="dropdown" class="btn" id="profile">
						<i class="fa-regular fa-user"></i>
					</button>
					<style>
						#profile{
							background-position: center;     
							background-size: cover;        
							background-repeat: no-repeat; 
							border-radius: var(--radius-circle);
							height: 50px;
							width: 50px; 
						}
					</style>
					<div popover id="dropdown" class="dropdown">
						<div class="account-profile">
							<a href="/account/edit">Profile</a>
						</div>
						<div class="logout">
							<form method="POST" action="/logout">
								@csrf
								<button type="submit">Log Out</button>
							</form>
						</div>
					</div>
				@endauth
			</nav>
		</div>

	</div>
</nav>
