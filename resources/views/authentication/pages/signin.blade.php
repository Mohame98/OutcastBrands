<section class="signin">
  <div class="container">
    <form method="POST" action="{{ route('login') }}" class="action-form" data-action="sign-in">
      @csrf
      <fieldset>
        <header class="modal-headers">
          <legend><h1>Log In</h1>
            <div class="info-signin">
              {{-- <i class="fa-solid fa-circle-info"></i> --}}
              <span class="hover-caption">Close</span>
            </div>
            
          </legend>
          @include('components.close-modal')
        </header>
    
        <div class="form-group">
          <label for="signin_email">
            <span>Email</span>
            <input type="email" name="signin_email" id="signin_email" value="{{ old('signin_email') }}" autocomplete="on" placeholder="Enter Email" aria-label="Enter email" autofocus>
          </label>
          <x-form-error name='signin_email'></x-form-error>
        </div>

        <div class="form-group password-field">
          <label for="signin_password">
            <span>Password</span>
            <div class="password-input-container">
              <input type="password" name="signin_password" id="signin_password" placeholder="Enter password" aria-label="Enter password" class="password-input" >
              @include('components.toggle-password')
            </div>
          </label>
          <x-form-error name='signin_password'></x-form-error>
        </div>
  
        <button class="btn white-btn log" type="submit">Log In</button>
      </fieldset>
    </form>
    <p>
      Dont have an account? 
      <button 
        data-dialog-open="#signup-modal"
        class="btn second-sign-up-btn underline" 
        aria-controls="signup-modal"
        aria-expanded="false"
        title="Sign Up"
      >
        Sign Up
      </button>
    </p>

    <div class="modal-wrapper forgot-password">
      <button 
        data-dialog-open="#forgot-password-modal"
        class="btn forgot-password-btn modal-btn underline"
        aria-haspopup="forgot-password form" 
        aria-controls="forgot-password-modal" 
        title="Forgot Password"
        aria-expanded="false"
      >
        Forgot Your Password?
      </button>
      <dialog id="forgot-password-modal" class="forgot-password-modal">
        @include('authentication.password-recovery.forgot-password')
      </dialog>
    </div>
  </div>
</section> 