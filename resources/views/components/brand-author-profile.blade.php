<div class="brand-author">
  <a href="{{ route('profile.show', $brand->user) }}">
   @if($brand->user->profile_image)
      <div 
        class="brand-author-profile image" 
        style="background-image: url('{{ asset('storage/app/public' . $brand->user->profile_image) }}')">
      </div>
    @else
      <div class="brand-author-profile letter">
        {{ strtoupper(substr($brand->user->email, 0, 1)) }}
      </div>
    @endif
    <p class="brand-author-username">{{ $brand->user->username }}</p>
  </a>
</div>         