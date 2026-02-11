<div class="modal-wrapper comments-section-wrapper">
  <button 
    class="btn comment-section-btn modal-btn item-btn"
    aria-haspopup="dialog comment-section-modal" 
    aria-controls="comment-section-modal" 
    title="Comments Section"
    aria-expanded="false">
    <i class="fa-solid fa-comments"></i>
  </button>

  <div class="main-comment-count-container">
    <span id="comment-count">
    {{ $brand->comments_count ?? 0 }}
    </span>
  </div>

  <dialog id="comment-section-modal" class="comment-section-modal" data-brand-id="{{ $brand->id }}">
    <section class="comments-section">
      <header class="modal-headers comments-header">
        <legend>
          <div class="comment-image-title">
            @if ($brand->featuredImage)   
            <div class="brand-featured-image" style="background-image: url('{{ asset('storage/' . $brand->featuredImage->image_path) }}')">
            </div>
            @endif
            <div>
              <h1 class="title">{{ $brand->title }}</h1>
              <div class="comment-count-container">
                <span id="comment-count">{{ $brand->comments_count }}</span>
                @if ($brand->comments_count == 1)
                  <h1>Comment</h1>
                @else
                  <h1>Comments</h1>
                @endif
              </div>
            </div>
          </div>
        </legend>
        @include('components.close-modal')
      </header>

      <div class="filter-container">
        <div class="filter-section">
          <div class="filter-btns">
            <button name="filter" value="all" data-filter="filter:all" class="filter-btn btn active">
              All Comments
            </button>
            @auth
            <button name="filter" value="liked" data-filter="filter:liked" class="filter-btn btn">
              Liked
            </button>
            @endauth
          </div>
          
          @if ($brand->comments_count)
          <select name="sort" id="sort-by">
            <option value="featured">Default</option>
            <option value="newest">Newest</option>
            <option value="oldest">Oldest</option>
            <option value="most liked">Most Liked</option>
          </select>
          @endif
        </div>
        <div id="active-filters" class="active-filters"></div>
      </div>
    
      <div class="comments-list">
        <div 
          class="comments-container" 
          id="comments-container" 
          data-brand-id="{{ $brand->id }}">
        </div>
        <button id="load-more-comments" class="load-more-comments btn white-btn">
          Load More
        </button>
      </div>

      <footer class="comment-input-container">
        <div class="comment-user">
          @if($brand->user)
            <div class="brand-author">
              <a href="{{ route('profile.show', $brand->user) }}">
                @if($brand->user->profile_image)
                <div 
                  class="brand-author-profile image" 
                  style="background-image: url('{{ asset('storage/' . $brand->user->profile_image) }}')">
                </div>
                @else
                <div class="brand-author-profile letter">
                  {{ strtoupper(substr($brand->user->email, 0, 1)) }}
                </div>
                @endif
              </a>
            </div>
          @endif
        </div>
        <form
          id="comment-form"
          action="{{ route('comment.add', $brand) }}"
          method="POST"
          class="action-form add-comment-form"
          data-action="add-comment"
          data-brand-id="{{ $brand->id }}"
        >
          <fieldset class="add-comment-field" id="add-comment-field">
            @csrf
            <div class="form-group">
              <input type="hidden" name="brand_id" value="{{ $brand->id }}">
              <label for="add_comment_text">
                <div class="input-wrapper"> 
                  <input autofocus type="text" placeholder="Add a comment..."  name="add_comment_text" id="add_comment_text" 
                  required
                  maxlength="400"
                  title="add a comment"
                >
                </div>
              </label>
              <x-form-error name="add_comment_text" />
            </div>
          </fieldset>   
        </form>
      </footer>
    </section>
  </dialog>
</div>