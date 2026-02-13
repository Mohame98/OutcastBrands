
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
            <input type="text" name="username" id="username" value="{{ old('username') }}" autocomplete="username"
              placeholder="Enter username" aria-label="Enter username" autofocus
              required
              minlength="3"
              maxlength="30"
              pattern="^[a-zA-Z0-9_-]+$"
              title="letters, numbers, underscores and dashes"
            >
          </label>  
          <x-form-error name='username'></x-form-error>    
        </div>
        
        <div class="form-group">
          <label for="signup_email">
            <span>Email address</span>
            <input type="email" name="signup_email" id="signup_email" value="{{ old('email') }}" autocomplete="email" placeholder="eg@example.com" aria-label="Enter email" 
              required
              maxlength="255"
              autocomplete="email"
              pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$"
              title="Please enter a valid email address"
            >
          </label>    
          <x-form-error name='signup_email'></x-form-error>      
        </div>

        <div class="form-group password-field "> 
          <label for="signup_password">
            <span>Password</span>
            <div class="password-input-container">
              <input type="password" name="signup_password" id="signup_password"
                placeholder="Enter password" aria-label="Enter password" class="password-input" required
                minlength="8"
                maxlength="64"
                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
                title="Include 8 characters, uppercase, lowercase, number, and special character"
                autocomplete="new-password">
              @include('components.toggle-password')
            </div>
          </label>
          <x-form-error name='signup_password'></x-form-error>
        </div>
        <button class="btn white-btn log" type="submit">Create account</button>
      </fieldset>
    </form>
    <p>
      Already have an account? 
      <button 
        data-dialog-open="#signin-modal"
        class="btn second-sign-in-btn underline" 
        title="Log In"
        type="button"
        data-target="login-modal"
      >
        Log in
      </button>
    </p>
  </div>
</section>

