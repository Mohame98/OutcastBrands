<x-main-layout>
  @section('title', 'Edit Profile')
  <section class="edit-account">
    <div class="container">
      <div class="account-settings-flex">
        @include('components.setting-links')
        <div class="edit-account-container">
          <h2>Profile</h2>
          <h3>Choose how you appear on OutcastBrands</h3>

          <div class="modal-wrapper">
            <button 
              class="btn edit-account-btn modal-btn"
              aria-haspopup="Change profile image input" 
              aria-controls="profile-image-modal" 
              aria-expanded="false">
              <span>
                <span>Profile Image</span>
                <span>Upload an image for your profile</span>
              </span>
              <i class="fa-solid fa-chevron-right"></i>
            </button>

            <dialog id="profile-image-modal" class="profile-image-modal">
              <form action="{{ route('profileImg.change') }}" method="POST" enctype="multipart/form-data" 
              class="action-form" data-action="change-profile-image" data-submission="true">
                @csrf
                @method('PATCH')
                <fieldset>
                  <header class="modal-headers">
                    <legend><h1>Profile Image</h1></legend>
                    @include('components.close-modal')
                  </header>

                  <p>No image input will result in removing your profile image</p>
                  
                  <label for="profile_image">
                    <div class="media-input">
                      <label for="profile_image" class="media-label" tabindex="0">
                        <span>Drag or upload</span> <i class="fa-solid fa-cloud-arrow-up"></i>
                      </label>
                      <input type="file" name="profile_image" id="profile_image" aria-label="Drag and Drop or upload media" accept="image/*"
                        accept=".png, .jpg, .jpeg"
                      >
                      <div class="media-preview"></div>
                      <div class="upload-info">
                        <p>Formats: JPG, PNG</p>
                        <P>Max Size: 1MB</P>
                      </div>
                      <button
                        id="clear-media"
                        class="clear-media-btn"
                        style="display: none"
                      >
                        <i class="fa-solid fa-trash-can"></i>
                      </button>
                    </div>
                  </label>
                
                  <x-form-error name='profile_image'></x-form-error>
                
                  <div class="btn-container">
                    <button type="button" class="btn cancel close-modal">
                      Cancel
                    </button>
                    <button class="btn update" type="submit">
                      Update
                    </button>
                  </div>
                </fieldset>
              </form> 
            </dialog>
          </div>

          <div class="modal-wrapper">
            <button 
              class="btn edit-account-btn modal-btn"
              aria-haspopup="Edit your bio" 
              aria-controls="edit-bio-modal" 
              aria-expanded="false">
              <span>
                <span>Biography</span>
                <span>Tell us about yourself</span>
              </span>
              <i class="fa-solid fa-chevron-right"></i>
            </button>
            <dialog id="edit-bio-modal" class="edit-bio-modal">
              <form action="{{ route('bio.change') }}" method="POST" class="action-form" data-submission="true" data-action="change-bio">
                @csrf
                @method('PATCH')
                <fieldset>
                  <header class="modal-headers">
                    <legend><h1>Change your bio</h1></legend>
                    @include('components.close-modal')
                  </header>

                  <p>A few words go a long way. What do you want people to remember about you?</p>
                
                  <div class="form-group">
                    <label for="bio">
                      <span>Change Bio</span>
                      <textarea 
                        name="bio" id="bio" aria-label="Change your profile's biography" rows="4" autofocus
                        maxlength="250"
                        pattern="/^[\p{L}\p{N}\s.,!?"\'\-()]+$/u"
                        title="Enter a Bio up to 250 characters"
                      >
                        {!! old('bio', auth()->user()->bio) !!}
                      </textarea>
                    </label>  
                    <x-form-error name="bio" />
                  </div>
                  
                  <div class="btn-container">
                    <button type="button" class="btn cancel close-modal">
                      Cancel
                    </button>
                    <button class="btn update" type="submit">
                      Update
                    </button>
                  </div>
                </fieldset>
              </form> 
            </dialog>
          </div>

          <div class="modal-wrapper">
            <button 
              class="btn edit-account-btn modal-btn"
              aria-haspopup="Add your instagram link" 
              aria-controls="add-instagram-modal" 
              aria-expanded="false">
              <span>
                <span>Instagram</span>
                <span>Show off your instagram</span>
              </span>
              <i class="fa-solid fa-chevron-right"></i>
            </button>
            <dialog id="add-instagram-modal" class="add-instagram-modal">
              <form action="{{ route('instagram.change') }}" method="POST" class="action-form" data-submission="true" data-action="change-instagram">
                @csrf
                @method('PATCH')
                <fieldset>
                  <header class="modal-headers">
                    <legend><h1>Instagram</h1></legend>
                    @include('components.close-modal')
                  </header>
                  
                  <div class="form-group">
                    <label for="instagram">
                      <span>Add your instagram link</span>
                      <input autofocus type="text" name="instagram" id="instagram" aria-label="update your profiles instagram link" value="{{ old('instagram', auth()->user()->instagram) }}"
                        maxlength="255"
                        pattern="^(https?:\/\/)?(www\.)?instagram\.com\/[a-zA-Z0-9_]+\/?$"
                        title="Enter a valid Instagram profile URL"
                      >
                    </label>               
                    <x-form-error name="instagram" />
                  </div>
                  
                  <div class="btn-container">
                    <button type="button" class="btn cancel close-modal">
                      Cancel
                    </button>
                    <button class="btn update" type="submit">
                      Update
                    </button>
                  </div>
                </fieldset>
              </form> 
            </dialog>
          </div>

          <div class="modal-wrapper">
            <button 
              class="btn edit-account-btn modal-btn"
              aria-haspopup="Edit your location" 
              aria-controls="edit-location-modal" 
              aria-expanded="false">
              <span>
                <span>Location</span>
                <span>Where are you located?</span>
              </span>
              <i class="fa-solid fa-chevron-right"></i>
            </button>
            <dialog id="edit-location-modal" class="edit-location-modal">
              <form action="{{ route('location.change') }}" method="POST" class="action-form" data-submission="true" data-action="change-location">
                @csrf
                @method('PATCH')
                <fieldset>
                  <header class="modal-headers">
                    <legend><h1>Add your location</h1></legend>
                    @include('components.close-modal')
                  </header>
                  
                  <div class="form-group">
                    <label for="user_location">
                      <span>Represent your city</span>
                      <input autofocus type="text" name="user_location" id="user_location" aria-label="change your profile's location" value="{{ old('user_location', auth()->user()->user_location) }}" 
                        maxlength="60"
                        pattern="/^[\p{L}\p{N} .,'’\-()]+$/u"
                        title="Enter you location"
                      >
                    </label>
                    <x-form-error name="user_location" />
                  </div>
                  
                  <div class="btn-container">
                    <button type="button" class="btn cancel close-modal">
                      Cancel
                    </button>
                    <button class="btn update" type="submit">
                      Update
                    </button>
                  </div>
                </fieldset>
              </form> 
            </dialog>
          </div>
        </div>
    </div>
  </section>
</x-main-layout>