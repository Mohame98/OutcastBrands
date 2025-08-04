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