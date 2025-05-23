<nav class="main-nav">
	<div class="container">
		<div class="left-side-navigation">
			<a class="logo" href="/"><h1>Western Outlaw</h1></a>
			@auth
			<div class="modal-wrapper">
				<button 
					class="btn add-brand-btn modal-btn"
					aria-haspopup="add a brand form" 
					aria-controls="add-brand-modal" 
					aria-expanded="false">
					<i class="fa-solid fa-plus"></i>
					Submit a Brand
				</button>
				<dialog id="add-brand-modal" class="add-brand-modal">
					<form action="" method="POST" class="action-form" data-action="add-brand" enctype="multipart/form-data" data-form-base="/add-brands" data-total-steps="3">
						@csrf
						<fieldset class="multi-field active">
							<header>
								<legend><h1>Brand Info</h1></legend>
								@include('components.close-modal')
							</header>
							<p>Enter brand details below.</p>

							<div class="form-group">
								<label for="title" class="title">
									<span>Brand Title</span>
									<input type="text" id="title" name="title">
								</label>
								<x-form-error name="title" />
							</div>

							<div class="form-group">
								<label for="sub_title" class="sub_title">
									<span>Sub Title</span>
									<input type="text" id="sub_title" name="sub_title">
								</label>
								<x-form-error name="sub_title" />
							</div>

							<div class="form-group">
								<label for="website" class="website">
									<span>Website Link</span>
									<input type="url" id="website" name="website">
								</label>
								<x-form-error name="website" />
							</div>

							<div class="row">
								<div class="form-group">
									<label for="location" class="location">
										<span>Location</span>
										<input type="text" id="location" name="location">
									</label>
									<x-form-error name="location" />
								</div>
								
								<div class="form-group">
									<label for="launch_date" class="launch_date">
										<span>Launch Date</span>
										<input type="date" id="launch_date" name="launch_date">
									</label>
									<x-form-error name="launch_date" />
								</div>
							</div>

							<div class="form-group">
								<label for="description" class="description">
									Description
									<textarea id="description" name="description" rows="4"></textarea>
								</label>
								<x-form-error name="description" />
							</div>

							<div class="btn-container">
								<button class="btn update" type="submit" id="nextBtn1" data-step="1">Next</button>
							</div>
						</fieldset>

						<fieldset class="multi-field brand-image-field">
							<header>
								<legend><h1>Upload Images</h1></legend>
								@include('components.close-modal')
							</header>
							<p>Submit at most 4 photos of your brand/product.</p>
							<p>The first image will be your featured image</p>

							<div class="multiple-photos">
								<label for="brand-image">
									<div class="media-input brand">
										<label for="brand-image" class="media-label" tabindex="0">
											<span>Drag or upload</span> <i class="fa-solid fa-cloud-arrow-up"></i>
										</label>
										<input type="file" accept="image/*" data-selector="multiple-photos" data-multiple="true" data-max-files="4" name="photos[]" multiple id="brand-image" aria-label="Drag and Drop or upload media">
										<div class="media-preview brand"></div>
										<div class="upload-info">
											<p>Formats: JPG, PNG</p>
											<P>Max Size: 5MB</P>
										</div>
										<button class="clear-media-btn brand" style="display: none;">
											<i class="fa-solid fa-trash-can"></i>
										</button>
									</div>
								</label>
							</div>
							<x-form-error name='brand_image'></x-form-error>
							<p class="number-files"><span class="files-digit">0</span>/4</p>
							
							<div class="btn-container">
								{{-- <button class="btn cancel" type="button" id="prevBtn1">Back</button> --}}
								<button class="btn update" type="submit" id="nextBtn2" data-step="2">Next</button>
							</div>
						</fieldset>

						<fieldset class="multi-field">
							<header>
								<legend><h1>Select Category</h1></legend>
								@include('components.close-modal')
							</header>
							<p>Choose up to 3 categories for your brand.</p>
							@php
							$categories = [
								'Footwear', 'Accessories', 'Outerwear', 'Casual', 'Formal',
								'Activewear', 'Streetwear', 'Minimalist', 'Vintage', 'Preppy',
								'Seasonal', 'Luxury', 'Sustainable'
							];
							@endphp
							<div class="category-list multiple-categories" data-limit="3">
								@foreach ($categories as $category)
								<div class="form-group">
									<label class="category-button" for="checkbox-{{ $category }}">
										<input
											type="checkbox"
											name="categories[]"
											value="{{ $category }}"
											class="category-checkbox"
											id="checkbox-{{ $category }}"
											data-selector="multiple-categories"
										>
										<span>{{ $category }}</span>
									</label>
								</div>
								@endforeach
							</div>

							<div class="btn-container">
								{{-- <button class="btn cancel" type="button" id="prevBtn2">Back</button> --}}
								<button class="btn update final" type="submit" data-step="3">Submit</button>
							</div>
						</fieldset>
					</form> 
				</dialog>
      </div>
			@endauth
		</div>

		<div class="right-side-navigation">
			<div class="search-container">
				<x-nav-links href="{{ route('search') }}" :active="request()->is('search')">
					<i class="fa-solid fa-magnifying-glass"></i>
				</x-nav-links>
			</div>
			<nav class="desktop-nav" aria-label="Desktop navigation right">
				@guest
				<ul>
					<li><x-nav-links href="{{ route('login') }}" :active="request()->is('signin')"><i class="fa-solid fa-arrow-right-to-bracket"></i> Sign In</x-nav-links></li>
				</ul>
				@endguest
				@auth
				<details>
					<summary>
						@include('components.profile-image')
					</summary>
					<div class="dropdown-menu">
						<div class="profile item">
							<a href="#">
								<i class="fa-solid fa-address-card"></i>
								My Profile
							</a>
						</div>
						<div class="saved item">
							<a href="#">
								<i class="fa-solid fa-user-astronaut"></i>
								Saved Brands
							</a>
						</div>
						<div class="account-settings item">
							<a href="/account/edit">
								<i class="fa-solid fa-user-gear"></i>
								Account Settings
							</a>
						</div>
						<div class="logout item">
							<form method="POST" action="/logout">
								@csrf
								<button class="btn logout-btn" type="submit">
									<i class="fa-solid fa-arrow-right-to-bracket"></i>
									Log Out
								</button>
							</form>
						</div>
					</div>
				</details>
				@endauth
			</nav>
		</div>
	</div>
</nav>
