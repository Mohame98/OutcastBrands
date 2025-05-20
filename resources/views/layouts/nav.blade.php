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
					Add a Brand
				</button>
				<dialog id="add-brand-modal" class="add-brand-modal">
					<form action="" method="POST" class="action-form" data-action="add-brand" enctype="multipart/form-data">
						@csrf
						<fieldset class="active">
							<header>
								<legend><h1>Brand Info</h1></legend>
								<i class="fa-solid fa-xmark close-modal"></i>
							</header>
							<p>Enter brand details below.</p>

							<div class="form-step">
								<label for="title">Brand Title</label>
								<input type="text" id="title" name="title">
								<x-form-error name="title" />
							</div>

							<div class="form-step">
								<label for="sub_title">Sub Title</label>
								<input type="text" id="sub_title" name="sub_title">
								<x-form-error name="sub_title" />
							</div>

							<div class="form-step">
								<label for="website">Website Link</label>
								<input type="url" id="website" name="website">
								<x-form-error name="website" />
							</div>

							<div class="row">
								<div class="form-step">
									<label for="location">Location</label>
									<input type="text" id="location" name="location">
									<x-form-error name="location" />
								</div>
								
								<div class="form-step">
									<label for="launch_date">Launch Date</label>
									<input type="date" id="launch_date" name="launch_date">
									<x-form-error name="launch_date" />
								</div>
							</div>

							<div class="form-step">
								<label for="description">Description</label>
								<textarea id="description" name="description" rows="4"></textarea>
								<x-form-error name="description" />
							</div>

							<div class="btn-container">
								<button class="btn update" type="submit" id="nextBtn1" data-step="1">Next</button>
							</div>
						</fieldset>

						<fieldset class="brand-image-field">
							<header>
								<legend><h1>Upload Images</h1></legend>
							</header>
							<p>Submit at most 4 photos of your brand/product.</p>
							<p>The first image will be the featured image</p>

							<label for="brand-image">
								<div class="media-input brand">
									<label for="brand-image" class="media-label" tabindex="0">
                    <span>Drag or upload</span> <i class="fa-solid fa-cloud-arrow-up"></i>
                  </label>
									<input type="file" accept="image/*" data-multiple="true" data-max-files="4" data-max-size="1242880" name="photos[]" multiple id="brand-image" aria-label="Drag and Drop or upload media">
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
							<x-form-error name='brand_image'></x-form-error>
							<p class="number-files"><span class="files-digit">0</span>/4</p>
							
							<div class="btn-container">
								{{-- <button class="btn cancel" type="button" id="prevBtn1">Back</button> --}}
								<button class="btn update" type="submit" id="nextBtn2" data-step="2">Next</button>
							</div>
						</fieldset>

						<fieldset>
							<header>
								<legend><h1>Select Category</h1></legend>
							</header>
							<p>Choose up to 3 categories for your brand.</p>
							@php
							$categories = [
								'Footwear', 'Accessories', 'Outerwear', 'Casual', 'Formal',
								'Activewear', 'Streetwear', 'Minimalist', 'Vintage', 'Preppy',
								'Seasonal', 'Luxury', 'Sustainable'
							];
							@endphp
							<div class="category-list" data-limit="3">
								@foreach ($categories as $category)
								<label class="category-button">
									<input
										type="checkbox"
										name="categories[]"
										value="{{ $category }}"
										class="category-checkbox"
      						>
									<span>{{ $category }}</span>
								</label>
								@endforeach
							</div>

							<script>
								  document.addEventListener('DOMContentLoaded', () => {
										const container = document.querySelector('.category-list');
										const checkboxes = container.querySelectorAll('.category-checkbox');
										const maxAllowed = parseInt(container.dataset.limit || 3);

										checkboxes.forEach(checkbox => {
											checkbox.addEventListener('change', () => {
												const checkedCount = container.querySelectorAll('.category-checkbox:checked').length;

												if (checkedCount >= maxAllowed) {
													checkboxes.forEach(cb => {
														if (!cb.checked) cb.disabled = true;
													});
												} else {
													checkboxes.forEach(cb => cb.disabled = false);
												}
											});
										});
									});
							</script>
							
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
					<i class="fa-solid fa-magnifying-glass"></i>Search...
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
