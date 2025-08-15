<div class="settings">
  <ul class="settings-links">
    <x-nav-links href="{{ route('account.edit') }}" :active="request()->is('account/edit')"> 
      Account
    </x-nav-links>

    <x-nav-links href="{{ route('account.profile') }}" :active="request()->is('account/profile')">
      Profile
    </x-nav-links>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="btn logout-btn link" type="submit">
        <i class="fa-solid fa-arrow-right-to-bracket"></i>
        Log Out
      </button>
    </form>
  </ul>
</div>