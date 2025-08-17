
<section class="forgot-password">
  <div class="container">
    <form action="{{ route('password.email') }}" method="POST" class="action-form" data-action="forgot-password">
      @csrf
      <fieldset>
        <header class="modal-headers">
          <legend><h1>Forgot password</h1></legend>
          @include('components.close-modal')
        </header>

        <p>Enter your email to receive the password reset link</p>
    
        <div class="form-group">
          <label for="forgot_password_email">
            <span>Email</span>
            <input type="email" name="forgot_password_email" id="forgot_password_email" value="{{ old('email') }}" placeholder="Enter email" aria-label="Enter email" required>
          </label>
          <x-form-error name='email'></x-form-error>
        </div>

        <button class="btn main-button" type="submit">Send Password Reset Link</button>
      </fieldset>
    </form>
    <p>
      Back to
      <button class="btn forgot-second-sign-in-btn underline">
        Sign in
      </button>
    </p>
  </div>
</section> 
