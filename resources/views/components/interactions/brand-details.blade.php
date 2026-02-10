@props([
  'brand',
  'view_count' => 0,
  'save_count' => 0,
  'vote_count' => 0,
])

@if($brand)
<div class="brand-data-details item">
  <div class="modal-wrapper">
    <button 
      class="btn brand-details modal-btn item-btn"
      aria-haspopup="show brand details" 
      aria-controls="brand-details-modal" 
      title="Brand Details"
      aria-expanded="false">
      <i class="fa-regular fa-circle-question"></i>
    </button>
    <dialog id="brand-details-modal" class="brand-details-modal">
      <section class="brand-details">
        <header>
          <h3>{{ $brand->title ?? 'Brand Details' }}</h3>
          @include('components.close-modal')
        </header>
        
        @if($brand->created_at || $brand->launch_date)
          <div class="brand-essential-dates">
            @if($brand->created_at)
              <p>Posted {{ $brand->created_at->format('M d, Y') }}</p>
            @endif
            @if($brand->launch_date)
              <p>Launch Date {{ $brand->launch_date }}</p>
            @endif
          </div>
        @endif
        
        <div class="data-flex">
          <div class="data-container">
            <span>Views</span>
            <p>{{ $view_count ?? 0 }}</p>
          </div>
        
          <div class="data-container">
            <span>Saves</span>
            <p class="detail-save-count">{{ $save_count ?? 0 }}</p>
          </div>
        
          <div class="data-container">
            <span>Votes</span>
            <p class="detail-vote-count">{{ $vote_count ?? 0 }}</p>
          </div>
  
          <div class="data-container">
            <span>Comments</span>
            <p id="comment-count">{{ $brand->comments_count ?? 0 }}</p>
          </div>
        </div>
    
        @if($brand->website)
          <p>
            <a class="brand-site" href="{{ $brand->website }}" target="_blank" rel="noopener">
              <i class="fa-solid fa-link"></i>
              Brand Site
            </a>
          </p>
        @endif

        @if($brand->location)
          <p>{{ $brand->location }}</p>
        @endif

        @if($brand->categories && $brand->categories->count() > 0)
          <div class="categories">
            <p>Categories</p>
            <div class="category-list">
              @foreach($brand->categories as $category)
                @if($category->name)
                  <span class="category-item">
                    <a 
                      href="{{ route('search', ['category' => $category->name, 'page' => 1]) }}" 
                      class="category-link white-btn">{{ $category->name }}
                    </a>
                  </span>
                @endif
              @endforeach
            </div>
          </div>
        @endif
      </section>
    </dialog>
  </div>
</div>
@endif