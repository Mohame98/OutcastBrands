<span class="avatar-container">
  @if(auth()->user()->profile_image)
  <span class="avatar img" style="background-image: url('{{ asset('storage/app/public/' . auth()->user()->profile_image) }}');">
  </span>
  @else
  <span class="avatar letter">
    {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
  </span>
  @endif
  <span class="user-email">{{ auth()->user()->email }}</span>
</span>
 