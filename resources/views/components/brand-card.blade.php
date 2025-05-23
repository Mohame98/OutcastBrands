@if($featured)
<section class="featured-brand">
  <header class="featured-brand-header">
    <span>TODAY</span>
    <h1>Featured Brand</h1>
  </header>
  <div class="brand-container">
    <div class="left-brand-featured-image" style="background-image: url({{ $brand->featuredImage->image_path }})">
    </div>
    <div class="brand-content">
      <div class="brand-content-container">
        <div class="top-content">
          <div class="user-menu-container">
            @include('components.brand-author-profile')
            @auth
              <x-context-menu :brand="$brand" type="brand" />
            @endauth
          </div>
          <a href="{{ route('brand.show', $brand) }}">Brand</a>
          <div class="brand-text-content">
            <h2 class="title">{{ $brand->title }}</h2>
            <p class="sub-title">{{ $brand->sub_title }}</p>
            <p class="description">{{ $brand->description }}</p>
          </div>
        </div>
        <div class="bottom-content">
          <p>{{ $brand->location }}</p>
          <x-vote :brand="$brand"/>
        </div>
      </div>
    </div>
  </div>
</section>
@else
<section class="popular-brands">
  <div class="popular-brand-container">
    <div class="popular-brand-featured-image" style="background-image: url({{ $brand->featuredImage->image_path }})">
    </div>
    <div class="popular-brand-title">
      <h3>{{ $brand->title }}</h3>
      <p>{{ $brand->sub_title }}</p>
    </div>
    <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}"  data-action="vote">
      @csrf
      <input type="hidden" name="vote" value="1">
      <button type="submit" class="btn vote-btn upvote" aria-label="Upvote brand">
        <i class="fa-solid fa-arrow-trend-up"></i>
        <span class="total-votes" aria-label="total votes is {{ $brand->total_votes }}">
          <p class="vote-count">{{ $brand->total_votes }}</p>
        </span>
      </button>
    </form>
  </div>
</section>
@endif