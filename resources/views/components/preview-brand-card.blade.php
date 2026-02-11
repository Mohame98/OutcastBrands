<section class="preview-brand-details">
  <article class="brand-preview-card">
    <!-- Media Section -->
    <a href="{{ route('brand.show', $brand) }}" class="card-media" title="{{ $brand->title }}">
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

    <!-- Content Section -->
    <div class="card-content">
      <div class="card-header">
        @if($brand->title)
        <h3 class="brand-title">{{ Str::limit($brand->title, 25, '...') }}</h3>
        @endif
        <div class="location-tag">
          <i class="fa-solid fa-location-dot"></i>
          @if($brand->location)
          <small class="location">{{ Str::limit($brand->location, 20, '...') }}</small>
          @endif
        </div>
      </div>

      <!-- Multi-Category Row -->
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
      @if(trim(strip_tags($brand->user->bio)) != '')
      <div class="brand-description">
        {!! $brand->user->bio !!}
      </div>
      @endif
      @auth
      <div class="preview-interactions">
        <x-interactions.vote :brand="$brand"/>
      </div>
      @endauth
      <div class="card-footer">
        <a href="{{ route('brand.show', $brand) }}" class="main-cta btn white-btn">
          View Details
        </a>
      </div>
    </div>
  </article>
</section>