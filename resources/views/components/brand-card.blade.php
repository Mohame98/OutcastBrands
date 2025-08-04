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
    <div class="left-brand-featured-image" style="background-image: url('{{ asset($brand->featuredImage->image_path) }}')">
    
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
        </div>
        <div class="middle-content">
          <div class="brand-text-content">
            <a href="{{ route('brand.show', $brand) }}" title="{{ $brand->title }}">
              <h2 class="title">{{ $brand->title }}</h2>
            </a>
            <p class="sub-title">{{ $brand->sub_title }}</p>
            <p class="description">{{ $brand->description }}</p>
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
@else
<article class="popular-brands-card">
  <a href="{{ route('brand.show', $brand) }}" class="popular-brand-container" title="{{ $brand->title }}">
    <div class="popular-brand-featured-image" style="background-image: url('{{ asset($brand->featuredImage->image_path) }}')">
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
          <span class="view-count">{{ $view_count }}</span>
        </div>
      </div>
    </div>
  </div>
</article>
@endif