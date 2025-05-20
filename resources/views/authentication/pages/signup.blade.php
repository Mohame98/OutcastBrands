<x-main-layout>
  <section class="signup">
    <div class="container">
      <form method="POST" action="{{ route('signup') }}">
        @csrf
        <fieldset>
          <header>
            <legend><h1>Create your account</h1></legend>
          </header>

          <div>
            <p>Join a network of fashion trend setters</p>
          </div>
          
          <div>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" value="{{ old('username') }}" autocomplete="on"
                placeholder="Enter username" aria-label="Enter username">
            <x-form-error name='username'></x-form-error>    
          </div>
          
          <div>
            <label for="email">Email address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="on"
                placeholder="eg.andrew@example.com" aria-label="Enter email">
            <x-form-error name='email'></x-form-error>      
          </div>

          <div class="row">
            <div class="password-field"> 
              <label for="password">Password</label>
              <input type="password" name="password" id="pass"
                  placeholder="Enter password" aria-label="Enter password" class="password-input">
              @include('components.toggle-password')
              <x-form-error name='password'></x-form-error>
            </div>
          
            <div class="password-field"> 
              <label for="password">Confirm Password</label>
              <input type="password" name="password_confirmation" id="c-pass"
                  placeholder="Re-enter password" aria-label="Confirm password" class="password-input">
              @include('components.toggle-password')
              <x-form-error name='password_confirmation'></x-form-error>
            </div>
          </div> 
          <button class="btn main-button" type="submit">Create account</button>
        </fieldset>
      </form>
      <p>Already have an account?<a href="{{ route('login') }}">Sign In</a></p>
    </div>
  </section>
</x-main-layout>
