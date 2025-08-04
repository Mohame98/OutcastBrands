@php
$vote = $brand->voters->firstWhere('id', auth()->id())?->pivot->vote ?? null;
@endphp

<x-main-layout>
  <section class="show-brand">
    <div class="container">
    
      <header class="main-header">
        <div class="wrapper">
          @include('components.brand-author-profile')
          <h1 class="title">{{ $brand->title }}</h1>
        </div>
      </header>

      <div class="wrapper-main">
        <div class="brand-image-slider slider">
          @foreach ($brand->images->take(4) as $index => $image)
          <div class="slide">
            <a href="{{ asset('storage/' . $image->image_path) }}" data-caption="Brand Image {{ $index + 1 }}" data-fancybox="brandImages">
              <div class="brand-images" style="background-image: url('{{ asset('storage/' . $image->image_path) }}')"></div>
            </a>
          </div>
          @endforeach
        </div>

        <div class="social-interactions brand-detail">
          <div class="social-item">
            <x-interactions.saveV2 :brand="$brand"/>
          </div>
          
          <div class="social-item">
            <div class="brand-data-details item">
              <div class="modal-wrapper">
                <button 
                  class="btn brand-details modal-btn"
                  aria-haspopup="show brand details" 
                  aria-controls="brand-details-modal" 
                  aria-expanded="false">
                  <i class="fa-regular fa-circle-question"></i>
                </button>
                <dialog id="brand-details-modal" class="brand-details-modal">
                  <header>
								    <h3>{{ $brand->title }}</h3>
                    @include('components.close-modal')
                  </header>
                  
                  <div class="brand-essential-dates">
                    <p>Posted {{ $brand->created_at }}</p>
                    <p>Launch Date {{ $brand->launch_date }}</p>
                  </div>
                  
                  <div class="data-flex">
                    <div class="data-container">
                      <span>Views</span>
                      <p>{{ $view_count }}</p>
                    </div>

                    <div class="data-container">
                      <span>Saves</span>
                      <p>{{ $save_count }}</p>
                    </div>

                    <div class="data-container">
                      <span>Votes</span>
                      <p>{{ $vote_count }}</p>
                    </div>

                    <div class="data-container">
                      <span>Comments</span>
                      <p>{{ $brand->comments_count }}</p>
                    </div>
                  </div>
              
                  <p>
                    <a class="brand-site" href="{{ $brand->website }}">
                      <i class="fa-solid fa-link"></i>
                      Brand Site
                    </a>
                  </p>
                  <p>{{ $brand->location }}</p>
                  <div class="categories">
                    <p>Categories</p>
                    <div class="category-list">
                      @foreach($brand->categories as $category)
                      <span class="category-item">
                        <a 
                          href="{{ route('search', ['category' => $category->name, 'page' => 1]) }}" 
                          class="category-link white-btn">{{ $category->name }}
                        </a>
                      </span>
                      @endforeach
                    </div>
                  </div>
                </dialog>
              </div>
            </div>
          </div>

          <div class="social-item">
            <x-interactions.comments-section :brand="$brand" />
          </div>

          @auth
          <div class="main-brand-report social-item">
            <x-interactions.report :model="$brand" type="brand" />
          </div>  
          @endauth
        </div>
      </div>
      
      <div class="content-container">
        <div class="brand-sub-title-container">
          <div class="wrapper">
            <h3 class="sub-title">{{ $brand->sub_title }}</h3> 
            <x-interactions.vote :brand="$brand"/>
          </div>
        </div>

        @if(trim(strip_tags($brand->description)) != '')
        <div class="ql-container ql-snow">
          <div class="ql-editor">
            {!! $brand->description !!}
          </div>
        </div>
        @endif

        {{-- @if($brand->description)
        <div class="ql-container ql-snow">
          <div class="ql-editor">
            {!! $brand->description !!}
          </div>
        </div>
        @endif --}}
          
        <div class="profile-promotion">
          <div class="brand-author">
            <a href="{{ route('profile.show', $brand->user) }}">
              @if($brand->user->profile_image)
                <div class="brand-author-profile image" style="background-image: url('{{ asset($brand->user->profile_image) }}')"></div>
              @else
                <div class="brand-author-profile letter">
                  {{ strtoupper(substr($brand->user->email, 0, 1)) }}
                </div>
              @endif
            </a>
          </div>      
          
          <a href="{{ route('profile.show', $brand->user) }}">
            <span class="username">{{ $brand->user->username }}</span>  
          </a>
          <span class="bio">{{ $brand->user->bio }}</span>
        </div>
      </div>

      <div class="more-from-user">
      @if ($relatedBrands->isNotEmpty())
        <div class="related-brands">
          <header class="popular-brands-header">
            <h3>More Brands By {{ $brand->user->username }}</h3>
            <a class="white-btn view-profile-btn" href="{{ route('profile.show', $brand->user) }}">
              <span class="profile-link">View Profile</span>  
            </a>
          </header>
          
          <section class="popular-brands-top-layer">
            <div class="grid">
              @foreach ($relatedBrands as $brand)
              <x-brand-card-types.grid-brand-card :brand="$brand" />
              @endforeach
            </div>
          </section>
        </div>
      @endif
      </div>
    </div>
  </section>
  @include('layouts.newsletter')
</x-main-layout>