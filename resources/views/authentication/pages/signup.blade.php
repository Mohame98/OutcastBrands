
<section class="signup">
  <div class="container">
    <form method="POST" action="{{ route('signup') }}" class="action-form" data-action="sign-up">
      @csrf
      <fieldset>
        <header class="modal-headers">
          <legend><h1>Create your account</h1></legend>
          @include('components.close-modal')
        </header>

        <div>
          <p>Join a network of fashion trend setters</p>
        </div>
        
        <div class="form-group">
          <label for="username">
            <span>Username</span>
            <input type="text" name="username" id="username" value="{{ old('username') }}" autocomplete="on"
              placeholder="Enter username" aria-label="Enter username" required>
          </label>  
          <x-form-error name='username'></x-form-error>    
        </div>
        
        <div class="form-group">
          <label for="email">
            <span>Email address</span>
            <input type="email" name="email" id="email" value="{{ old('email') }}" autocomplete="on"
              placeholder="eg.andrew@example.com" aria-label="Enter email" required>
          </label>    
          <x-form-error name='email'></x-form-error>      
        </div>

        {{-- <div class="row"> --}}
          <div class="password-field form-group"> 
            <label for="password">
              <span>Password</span>
              <div class="password-input-container">
                <input type="password" name="password" id="password"
                  placeholder="Enter password" aria-label="Enter password" class="password-input" required>
                @include('components.toggle-password')
              </div>
            </label>
            <x-form-error name='password'></x-form-error>
          </div>
        
          {{-- <div class="password-field"> 
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                placeholder="Re-enter password" aria-label="Confirm password" class="password-input" required>
            @include('components.toggle-password')
            <x-form-error name='password_confirmation'></x-form-error>
          </div> --}}
        {{-- </div>  --}}
        <button class="btn main-button" type="submit">Create account</button>
      </fieldset>
    </form>
    <p>
      Already have an account? 
      <button class="btn second-sign-in-btn underline" title="Log In">
        Log in
      </button>
    </p>
  </div>
</section>

