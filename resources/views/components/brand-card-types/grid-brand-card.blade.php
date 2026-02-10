@php
  $viewCount = optional($brand->views)->count() ?? 0;
@endphp
<article class="popular-brands-card brand-card" data-brand-id="{{ $brand->id }}" >
  <div class="modal-wrapper">
    <div class="btn-wrapper">
      <button 
        class="btn preview-brand-btn modal-btn"
        aria-haspopup="dialog preview-brand-modal" 
        aria-controls="preview-brand-modal" 
        title="Preview Brand"
        aria-expanded="false">
        <span class="popular-brand-container" title="{{ $brand->title }}">
          <span 
            class="popular-brand-featured-image" 
            @if ($brand->featuredImage)
              style="background-image: url('{{ asset('storage/' . $brand->featuredImage->image_path) }}')"
            @else
              style="background-color: #ccc;"
            @endif
          >
            @if($brand->created_at->gt(now()->subWeek()))
            <span class="badge-new">New</span>
            @endif
            <span class="hover-content">
              <span class="hover-content-wrapper">
                <span class="title">
                  <span>{{ Str::limit($brand->title, 30, '...') }}</span>
                </span>
              </span>
            </span>
          </span>
        </span>
      </button>
      <x-interactions.saveV2 :brand="$brand"/>
    </div>
    
    <dialog id="preview-brand-modal" class="preview-brand-modal" data-brand-id="{{ $brand->id }}">
      <x-preview-brand-card :brand="$brand"/>
    </dialog>
  </div>
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
      <div class="popover-container context-menu-container">
        <button 
          class="btn context-menu-btn" 
          id="context-menu-btn" 
          popovertarget="brand-menu-{{ $brand->id }}" 
          popovertarget
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