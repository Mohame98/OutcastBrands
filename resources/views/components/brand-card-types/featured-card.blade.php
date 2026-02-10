@php
  $vote = $brand->voters->firstWhere('id', auth()->id())?->pivot->vote ?? null;
  $view_count = $brand->views()->count();
@endphp

@if($featured)
<section class="featured-brand">
  <header class="featured-brand-header">
    <span>TODAY</span>
    <h1>Featured Brand</h1>
  </header>
  <article class="brand-container"> 
    <a class="featured-image-container" href="{{ route('brand.show', $brand) }}" title="{{ $brand->title }}">
      <div class="featured-image" 
        @if ($brand->featuredImage)
          style="background-image: url('{{ asset('storage/' . $brand->featuredImage->image_path) }}')"
        @else
          style="background-color: #ccc;"
        @endif
      >
        @if($brand->created_at->gt(now()->subWeek()))
          <span class="badge-new">New</span>
        @endif
      </div>
    </a>
    
    <div class="brand-content">
      @include('components.brand-author-profile')
      @auth
      <div class="popover-container context-menu-container">
        <button 
          class="btn context-menu-btn" 
          id="context-menu-btn" 
          popovertarget="brand-menu-{{ $brand->id }}" 
          popovertargetaction="toggle" 
          aria-haspopup="menu" 
          title="Menu" 
          aria-expanded="false" 
          aria-controls="brand-menu-{{ $brand->id }}">
            <i class="fa-solid fa-ellipsis"></i>
        </button>
        <nav 
          class="context-menu popover" 
          id="brand-menu-{{ $brand->id }}" 
          aria-label="context menu" 
          popover>
          <ul>
            <x-interactions.save :brand="$brand"/>
            <x-interactions.report :model="$brand" type="brand" />
          </ul>
        </nav>
      </div>
      @endauth
      <div class="brand-content-container">
        <a class="content-container" href="{{ route('brand.show', $brand) }}">
          <div style="height: 30px"></div>
          <div class="middle-content">
            <div class="brand-text-content">
              @if($brand->title)
              <h2 class="title">{{ Str::limit($brand->title, 30, '...') }}</h2>
              @endif
              @if($brand->sub_title)
              <p class="sub-title">{{ Str::limit($brand->sub_title, 50, '...') }}</p>
              @endif
            </div>
          </div>
        
          <div class="bottom-content">
            @if($brand->location)
            <small class="location">{{ Str::limit($brand->location, 30, '...') }}</small>
            @endif
            <x-interactions.vote :brand="$brand"/>
          </div>
        </a>
      </div>
    </div>
  </article>
</section>
@endif