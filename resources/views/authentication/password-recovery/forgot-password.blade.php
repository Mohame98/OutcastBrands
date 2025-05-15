<x-main-layout>
  <section class="forgot-password">
    <div class="container">
      <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <fieldset>
          <header>
            <legend><h1>Forgot password</h1></legend>
          </header>

          <p>Enter your email to receive the password reset link</p>
      
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter email" aria-label="Enter email" required>
            <x-form-error name='email'></x-form-error>
          </div>

          <button class="btn main-button" type="submit">Send Password Reset Link</button>
        </fieldset>
      </form>
      <div class="signin-link">
        <a href="{{ route('login') }}">Back to SignIn</a>
      </div>
    </div>
  </section> 
</x-main-layout>