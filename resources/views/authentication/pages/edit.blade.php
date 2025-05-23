<x-main-layout>
  <section class="edit-account">
    <div class="container">
      <header>
        <h1>Account Settings</h1>
      </header>
      <div class="edit-account-container">
        <div class="account-email">
          <button 
            class="btn edit-account-btn">
            <span>
              <span>Email</span>
              <span>{{ auth()->user()->email }}</span>
            </span>
          </button>
        </div>

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
                <header>
                  <legend><h1>Profile Image</h1></legend>
                  @include('components.close-modal')
                </header>

                <p>No image input will result in removing your profile image</p>
                
                <label for="profile-image">
                  <div class="media-input">
                    <label for="profile-image" class="media-label" tabindex="0">
                      <span>Drag or upload</span> <i class="fa-solid fa-cloud-arrow-up"></i>
                    </label>
                    <input type="file" name="profile_image" id="profile-image" aria-label="Drag and Drop or upload media"  accept="image/*">
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
                <header>
                  <legend><h1>Change your bio</h1></legend>
                  @include('components.close-modal')
                </header>

                <p>A few words go a long way. What do you want people to remember about you?</p>
              
                <div class="form-group">
                  <label for="bio">
                    <span>Change Bio</span>
                    <textarea 
                      name="bio" id="bio" aria-label="Change your profile's biography" rows="4" maxlength="200">
                      {{ old('bio', auth()->user()->bio) }}
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
                <header>
                  <legend><h1>Instagram</h1></legend>
                  @include('components.close-modal')
                </header>
                
                <div class="form-group">
                  <label for="instagram" class="instagram">
                    <span>Add your instagram link</span>
                    <input type="text" name="instagram" id="instagram" aria-label="update your profiles instagram link" value="{{ old('instagram', auth()->user()->instagram) }}" required>
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
                <header>
                  <legend><h1>Add your location</h1></legend>
                  @include('components.close-modal')
                </header>
                
                <div class="form-group">
                  <label for="confirm_deletion">
                    <span>Represent your city</span>
                    <input type="text" name="user_location" id="user_location" aria-label="change your profile's location" value="{{ old('user_location', auth()->user()->user_location) }}" required>
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

        <div class="modal-wrapper">
          <button 
            class="btn edit-account-btn modal-btn"
            aria-haspopup="Change username input" 
            aria-controls="username-modal" 
            aria-expanded="false">
            <span>
              <span class="current-username">Username : 
                {{ auth()->user()->username }}
              </span>
              <span>Change your username</span>
            </span>
            <i class="fa-solid fa-chevron-right"></i>
          </button>
          <dialog id="username-modal" class="username-modal">
					  <form action="{{ route('username.change') }}" method="POST" class="user action-form" data-action="change-username" data-submission="true">
              @csrf
              @method('PATCH')
              <fieldset>
                <header>
                  <legend><h1>Change Username</h1></legend>
                  @include('components.close-modal')
                </header>
                <p>Choose a unique username that represents you.</p>

                <div class="form-group">
                  <label for="change-username">
                    <span>New Username</span>
                    <input type="text" name="username" id="change-username" aria-label="Enter your new username" value="{{ old('username', auth()->user()->username) }}" maxlength="90">
                  </label>
                  <x-form-error name='username' />
                  <small id="charCount"></small>
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
            aria-haspopup="Change password input" 
            aria-controls="password-modal" 
            aria-expanded="false">
            <span>
              <span>Password</span>
              <span>Change your Password</span>
            </span>
            <i class="fa-solid fa-chevron-right"></i>
          </button>
          <dialog id="password-modal" class="password-modal">
					  <form action="{{ route('password.change') }}" method="POST" class="action-form" data-action="change-password" data-submission="true">
              @csrf
              @method('PATCH')
              <fieldset>
                <header>
                  <legend><h1>Change Password</h1></legend>
                  @include('components.close-modal')
                </header>
                <p>Updating your password. Use at least 8 characters for safety.</p>
                <div class="password-field form-group"> 
                  <label for="current_password">
                    <span>Current Password</span>
                    <input type="password" class="password-input" name="current_password" id="current_password" aria-label="Enter your current password">
                  </label>
                  @include('components.toggle-password')
                  <x-form-error name="current_password" />
                </div>

                <div class="password-field form-group"> 
                  <label for="password">
                    <span>New Password</span>
                    <input type="password" class="password-input" name="password" id="password" aria-label="Enter new password">
                  </label>
                  @include('components.toggle-password')
                  <x-form-error name="password" />
                </div>

                <div class="password-field form-group"> 
                  <label for="password_confirmation">
                    <span>Confirm New Password</span>
                    <input type="password" class="password-input" name="password_confirmation" id="password_confirmation" aria-label="Enter password confirmation" >
                  </label>
                  @include('components.toggle-password')
                  <x-form-error name="password_confirmation" />
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
            aria-haspopup="Delete account input" 
            aria-controls="delete-account-modal" 
            aria-expanded="false">
            <span>
              <span>Delete Account</span>
              <span>All information will be deleted</span>
            </span>
            <i class="fa-solid fa-chevron-right"></i>
          </button>
          <dialog id="delete-account-modal" class="delete-account-modal">
            <form action="{{ route('account.delete') }}" method="POST" class="action-form" data-submission="true">
              @csrf
              @method('DELETE')
              <fieldset>
                <header>
                  <legend><h1>Confirm Account Deletion</h1></legend>
                  @include('components.close-modal')
                </header>
                
                <div class="password-field form-group">
                  <label for="confirm_deletion">
                    <span>Confirm your password to delete your account:</span>
                    <input type="password" name="confirm_deletion" id="confirm_deletion" aria-label="Confirm account deletion" class="password-input" required>
                  </label>
                  @include('components.toggle-password')
                  <x-form-error name="confirm_deletion" />
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