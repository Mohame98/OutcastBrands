<x-main-layout>
  <section class="signin">
    <div class="container">
      <form method="POST" action="{{ route('signin') }}">
        @csrf
        <fieldset>
          <header>
            <legend><h1>Sign In</h1></legend>
          </header>
      
          <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" :value="old('email')" autocomplete="on" placeholder="Enter Email" aria-label="Enter email">
            <x-form-error name='email'></x-form-error>
          </div>

          <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="pass" autocomplete="on" placeholder="Enter password" aria-label="Enter password">
            <x-form-error name='password'></x-form-error>
          </div>
    
          <button class="btn main-button" type="submit">Sign In</button>
        </fieldset>
      </form>
      <p>Dont have an account? <a href="{{ route('signup') }}">Sign Up</a></p>
      <a href="{{ route('password.request') }}">Forgot your password?</a>
    </div>
  </section> 
</x-main-layout>