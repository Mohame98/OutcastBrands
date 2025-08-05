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
  <div class="brand-container"> 
    <div class="left-brand-featured-image" 
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
   
    <div class="brand-content">
      <div class="brand-content-container">
        <div class="top-content">
          <div class="user-menu-container">
            @include('components.brand-author-profile')
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
          </div>
        </div>
        <div class="middle-content">
          <div class="brand-text-content">
            <a href="{{ route('brand.show', $brand) }}" title="{{ $brand->title }}">
              <h2 class="title">{{ $brand->title }}</h2>
            </a>
            <p class="sub-title">{{ $brand->sub_title }}</p>
            {{-- <p class="description">{!! $brand->description !!}</p> --}}
          </div>
        </div>
        
        <div class="bottom-content">
          <small class="location">{{ $brand->location }}</small>
          <x-interactions.vote :brand="$brand"/>
        </div>
      </div>
    </div>
  </div>
</section>
@endif