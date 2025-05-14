<x-main-layout>
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
            <label for="email">Email</label>
            <input type="email" name="email" id="email" :value="old('email')" placeholder="Enter email" aria-label="Enter email" required>
            <x-form-error name='email'></x-form-error>
          </div>

          <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" aria-label="Enter new password" required>
            <x-form-error name='password'></x-form-error>
          </div>

          <div class="form-group">
            <label for="password_confirmation">New Password confirmation</label>
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Enter new password confirmation" aria-label="Enter new password confirmation" required>
            <x-form-error name='password_confirmation'></x-form-error>
          </div>

          <button class="main-button" type="submit">Reset Password</button>
        </fieldset>
      </form>
    </div>
  </section>
</x-main-layout>