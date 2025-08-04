<x-main-layout>
  <section class="edit-account">
    <div class="container">
      <div class="account-settings-flex">
        <div class="settings">
          <ul class="settings-links">
            <x-nav-links href="{{ route('account.edit') }}" :active="request()->is('account/edit')"> 
              Account
            </x-nav-links>

            <x-nav-links href="{{ route('account.profile') }}" :active="request()->is('account/profile')">
              Profile
            </x-nav-links>
          </ul>
        </div>

        <div class="edit-account-container">

          <h2>Account</h2>
          <h3>Update Your Account Personal Details or Delete Your Account</h3>
          <p>Signed In as: <span>{{ auth()->user()->email }}</span></p>

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
                <header class="modal-headers">
                  <legend><h1>Change Username</h1></legend>
                  @include('components.close-modal')
                </header>
                <p>Choose a unique username that represents you.</p>

                <div class="form-group">
                  <label for="username">
                    <span>New Username</span>
                    <input type="text" name="username" id="username" aria-label="Enter your new username" value="{{ old('username', auth()->user()->username) }}" maxlength="90">
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
                <header class="modal-headers">
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
                <header class="modal-headers">
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
  </section>
</x-main-layout>