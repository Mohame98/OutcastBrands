<x-main-layout>
  @section('title', 'Reset Password')
  <section class="signup">
    <div class="container">
      <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <fieldset>
          <header>
            <legend><h1>Reset password</h1></legend>
          </header>
          
          <p>Please Enter your new password</p>
        
          <div class="form-group">
            <label for="reset_email">
              <span>Email</span>
              <input type="email" name="reset_email" id="reset_email" value="{{ old('email') }}" placeholder="Enter email" aria-label="Enter email"
                required
                maxlength="255"
                autocomplete="email"
                pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
                title="Please enter a valid email address"
              >
            </label>
            <x-form-error name='reset_email'></x-form-error>
          </div>

          <div class="password-field">
            <label for="reset_password">
              <span>New Password</span>
              <div class="password-input-container">
                <input type="password" name="reset_password" id="reset_password" placeholder="Enter new password" aria-label="Enter new password"  class="password-input" required
                  minlength="8"
                  maxlength="64"
                  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                  title="Include 8 characters, uppercase, lowercase, number, and special character"
                >
                @include('components.toggle-password')
              </div>
            </label>
            <x-form-error name='reset_password'></x-form-error>
          </div>

          <div class="password-field">
            <label for="reset_password_confirmation">
              <span>New Password confirmation</span>
              <div class="password-input-container">
                <input type="password" name="reset_password_confirmation" id="reset_password_confirmation" placeholder="Enter new password confirmation" aria-label="Enter new password confirmation"  class="password-input" required>
                @include('components.toggle-password')
              </div>
            </label>
            <x-form-error name='reset_password_confirmation'></x-form-error>
          </div>
          <button class="btn main-button" type="submit">Reset Password</button>
        </fieldset>
      </form>
    </div>
  </section>
</x-main-layout>