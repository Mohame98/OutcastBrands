@php
  $authId = auth()->id();
  $vote = $authId ? $brand->voters?->firstWhere('id', $authId)?->pivot->vote : null;
  $viewCount = optional($brand->views)->count() ?? 0;
@endphp

<article class="popular-brands-card">
  <a href="{{ route('brand.show', $brand) }}" class="popular-brand-container" title="{{ $brand->title }}">
    <div 
      class="popular-brand-featured-image" 
      @if ($brand->featuredImage)
        style="background-image: url('{{ asset('storage/app/public' . $brand->featuredImage->image_path) }}')"
      @else
        style="background-color: #ccc;"
      @endif
    >
      @if($brand->created_at->gt(now()->subWeek()))
      <span class="badge-new">New</span>
      @endif
      <div class="hover-content">
        <div class="title">
          <p>{{ $brand->title }}</p>
        </div>
        <x-interactions.saveV2 :brand="$brand"/>
      </div>
    </div>
  </a>

  <div class="card-author-info">
    @include('components.brand-author-profile')
    <div class="wrapper">
      <x-interactions.voteV2 :brand="$brand"/>
      <div class="view-count-container" title="Views">
        <i class="fa-solid fa-eye"></i>
        <div class="views">
          <span class="view-count">{{ $viewCount }}</span>
        </div>
      </div>
      @auth
      <div class="context-menu-container">
        <button 
          class="btn context-menu-btn" 
          id="context-menu-btn" 
          popovertarget="brand-menu-{{ $brand->id }}" 
          popovertargetaction="toggle" 
          aria-haspopup="menu" 
          title="Menu" 
          aria-expanded="false" 
          aria-controls="brand-menu-{{ $brand->id }}">
            <i class="fa-solid fa-ellipsis-vertical"></i>
        </button>
        <nav 
          class="context-menu popover" 
          id="brand-menu-{{ $brand->id }}" 
          aria-label="context menu" 
          popover>
          <ul>
            {{-- <li class="context-item">Copy Url</li> --}}
            <x-interactions.report :model="$brand" type="brand" />
          </ul>
        </nav>
      </div>
      @endauth
    </div>
  </div>
</article>