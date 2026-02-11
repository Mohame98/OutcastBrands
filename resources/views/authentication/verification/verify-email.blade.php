<x-main-layout>
  @section('title', 'Verify Email')
  <section class="verify-email">
    <div class="container">
      <div>
        <p>{{ __('Please verify your email address before accessing your account.') }}</p>
        <p>{{ __('A verification link has been sent to your email address. Please check your inbox and verify.') }}</p>

        <form method="POST" action="{{ route('verification.send') }}">
          @csrf
          <button type="submit" class="btn white-btn">
            {{ __('Resend Verification Email') }}
          </button>
        </form>
      </div>
    </div>
  </section>
</x-main-layout>
