<x-main-layout>
  <section class="edit-account">
    <div class="container">
      <header>
        <h1>Account</h1>
      </header>
      {{-- <div class="image" style="background-image: url('{{ asset('storage/' . auth()->user()->profile_image) }}')" >
      </div> --}}

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
            class="action-form" 
            data-action="change-profile-image">
              @csrf
              @method('PATCH')
              <fieldset>
                <header>
                  <legend><h1>Profile Image</h1></legend>
                  <i class="fa-solid fa-xmark close-modal"></i>
                </header>
                
                <label for="profile-image">
                  <x-form-error name='profile_image'></x-form-error>
                  <div class="media-input">
                    <label for="profile-image" class="media-label" tabindex="0">
                      <span>Drag or upload</span> <i class="fa-solid fa-cloud-arrow-up"></i>
                    </label>
                    <input type="file" name="profile_image" id="profile-image" aria-label="Drag and Drop or upload media">
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
					  <form action="{{ route('username.change') }}" method="POST" class="user action-form" data-action="change-username">
              @csrf
              @method('PATCH')
              <fieldset>
                <header>
                  <legend><h1>Change Username</h1></legend>
                  <i class="fa-solid fa-xmark close-modal"></i>
                </header>
                <p>Choose a unique username that represents you.</p>
                <div>
                  <x-form-error name='username'></x-form-error>
                  <label for="username">New Username</label>
                  <input type="text" name="username" id="change-username" aria-label="Enter your new username"
                  value="{{ old('username', auth()->user()->username) }}" maxlength="90" required>
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
            aria-haspopup="Search Bar" 
            aria-controls="search-modal" 
            aria-expanded="false">
            <span>
              <span>Password</span>
              <span>Change your Password</span>
            </span>
            <i class="fa-solid fa-chevron-right"></i>
          </button>
          <dialog id="password-modal" class="password-modal">
					  <form action="{{ route('password.change') }}" method="POST" class="action-form" data-action="change-password">
              @csrf
              @method('PATCH')
              <fieldset>
                <header>
                  <legend><h1>Change Password</h1></legend>
                  <i class="fa-solid fa-xmark close-modal"></i>
                </header>
                <p>Updating your password. Use at least 8 characters for safety.</p>
                <div>
                  <x-form-error name="current_password" />
                  <label for="current_password">Current Password</label>
                  <input type="password" name="current_password" id="current_password" aria-label="Enter your current password">
                </div>

                <div>
                  <label for="password">New Password</label>
                  <input type="password" name="password" id="password"  aria-label="Enter new password">
                  <x-form-error name="password" />
                </div>

                <div>
                  <label for="password_confirmation">Confirm New Password</label>
                  <input type="password" name="password_confirmation" id="password_confirmation" aria-label="Enter password confirmation" >
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
            aria-haspopup="Search Bar" 
            aria-controls="search-modal" 
            aria-expanded="false">
            <span>
              <span>Delete Account</span>
              <span>All information will be deleted</span>
            </span>
            <i class="fa-solid fa-chevron-right"></i>
          </button>
          <dialog id="password-modal" class="password-modal">
            <form action="{{ route('account.delete') }}" method="POST" class="action-form">
              @csrf
              @method('DELETE')
              <fieldset>
                <header>
                  <legend><h1>Confirm Account Deletion</h1></legend>
                  <i class="fa-solid fa-xmark close-modal"></i>
                </header>
                
                <div>
                  <x-form-error name="delete_current_password" />
                  <label for="delete_current_password">Confirm your password to delete your account:</label>
                  <input type="password" name="delete_current_password" id="delete_current_password" required>
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