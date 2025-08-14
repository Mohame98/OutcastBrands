
<section class="forgot-password">
  <div class="container">
    <form action="{{ route('password.email') }}" method="POST">
      @csrf
      <fieldset>
        <header class="modal-headers">
          <legend><h1>Forgot password</h1></legend>
          @include('components.close-modal')
        </header>

        <p>Enter your email to receive the password reset link</p>
    
        <div class="form-group">
          <label for="forgot-password-email">Email</label>
          <input type="email" name="email" id="forgot-password-email" value="{{ old('email') }}" placeholder="Enter email" aria-label="Enter email" required>
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
