@php
$vote = $brand->voters->firstWhere('id', auth()->id())?->pivot->vote ?? null;
@endphp

<x-main-layout>
  @section('title', 'Brand - ' . $brand->title)
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
            <x-interactions.brand-details 
              :brand="$brand"
              :view_count="$view_count"
              :save_count="$save_count"
              :vote_count="$vote_count"
              />
          </div>

          <div class="social-item">
            <x-interactions.comments-section :brand="$brand" />
          </div>

          @auth
          <div class="main-brand-report social-item">
            <x-interactions.report :model="$brand" type="brand" />
          </div>  
          @endauth

          @if(auth()->id() === $brand->user_id)
          <div class="modal-wrapper social-item">
            <button 
            class="btn delete-brand-btn modal-btn item-btn"
            aria-haspopup="Delete brand confirmation" 
            aria-controls="delete-brand-modal" 
            title="Delete Brand"
            aria-expanded="false">
              <i class="fa-solid fa-trash-can"></i>
            </button>
            <dialog id="delete-brand-modal" class="delete-brand-modal">
              <form method="POST" action="{{ route('brand.delete', $brand) }}" class="delete-brand-form action-form" data-action="delete-brand">
                <fieldset class="delete-brand-field" id="delete-brand-field">
                  <header class="modal-headers">
                    <h1>Delete this brand</h1>
                    @include('components.close-modal')
                  </header>

                  <div class="warning-card">
                    <h3>Warning</h3>
                    <p>Keep in mind that upon deleting your post all of the information will be deleted permanently.</p>

                    @csrf
                    @method('DELETE')
                    <div class="btn-container">
                      <button type="button" class="btn cancel close-modal">
                        Cancel
                      </button>
                      <button class="delete-brand-btn btn update" type="submit">
                        Delete Post
                      </button>
                    </div>
                  </div>
                </fieldset>
              </form>
            </dialog>
          </div>
          @endif
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
            {{ $brand->description }}
          </div>
        </div>
        @endif
          
        <div class="profile-promotion">
          <div class="brand-author">
            <a href="{{ route('profile.show', $brand->user) }}">
              @if($brand->user->profile_image)
                <div class="brand-author-profile image" style="background-image: url('{{ asset('storage/' . $brand->user->profile_image) }}')"></div>
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
            <h3>More Brands By <span class="color-blue">{{ $brand->user->username }}</span></h3>
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