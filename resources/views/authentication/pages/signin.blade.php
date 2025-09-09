<section class="signin">
  <div class="container">
    <form method="POST" action="{{ route('login') }}" class="action-form" data-action="sign-in">
      @csrf
      <fieldset>
        <header class="modal-headers">
          <legend><h1>Log In</h1></legend>
          @include('components.close-modal')
        </header>
    
        <div class="form-group">
          <label for="signin_email">
            <span>Email</span>
            <input type="email" name="signin_email" id="signin_email" value="{{ old('email') }}" autocomplete="on" placeholder="Enter Email" aria-label="Enter email" required autofocus>
          </label>
          <x-form-error name='signin_email'></x-form-error>
        </div>

        <div class="password-field form-group">
          <label for="signin_password">
            <span>Password</span>
            <div class="password-input-container">
              <input type="password" name="signin_password" id="signin_password" placeholder="Enter password" aria-label="Enter password" class="password-input" required>
              @include('components.toggle-password')
            </div>
          </label>
          <x-form-error name='signin_password'></x-form-error>
        </div>
  
        <button class="btn main-button" type="submit">Log In</button>
      </fieldset>
    </form>
    <p>Dont have an account? 
      <button class="btn second-sign-up-btn underline" title="Sign Up">
        Sign Up
      </button>
    </p>

    <div class="modal-wrapper forgot-password">
      <button 
        class="btn forgot-password-btn modal-btn underline"
        aria-haspopup="forgot-password form" 
        aria-controls="forgot-password-modal" 
        title="Forgot Password"
        aria-expanded="false">
          Forgot Your Password?
      </button>
      <dialog id="forgot-password-modal" class="forgot-password-modal">
        @include('authentication.password-recovery.forgot-password')
      </dialog>
    </div>
    
  </div>
</section> 