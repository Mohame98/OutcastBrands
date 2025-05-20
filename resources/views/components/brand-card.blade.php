@if($featured)
<section class="featured-brand">
  <header>
    <span>TODAY</span>
    <h1>Featured Brand</h1>
  </header>
  <div class="brand-container">
    <div class="left-brand-featured-image" style="background-image: url({{ $brand->featuredImage->image_path }})">
    </div>
    <div class="brand-content">
      <div class="brand-content-container">
        <div class="top-content">
          <div class="brand-author">
            <div class="brand-author-profile-image" style="background-image: url({{ $brand->user->profile_image }})"></div>
            <p class="brand-author-username">{{ $brand->user->username }}</p>
          </div>
          <h2> title {{ $brand->title }}</h2>
          <p>sub title: {{ $brand->sub_title }}</p>
          <p>description: {{ $brand->description }}</p>
        </div>
        <div class="bottom-content">
          <p>location: {{ $brand->location }}</p>
          <x-vote :brand="$brand"/>
        </div>
      </div>
    </div>
  </div>
</section>
@else
<section class="following-brands">
  <span class="badge">{{ $brand->title }}</span>
  <p>Total Votes: {{ $brand->total_votes }}</p>
</section>
@endif