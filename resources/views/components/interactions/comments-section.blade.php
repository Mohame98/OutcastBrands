<div class="comments item" id="comments-section">
  <button class="btn comment-section-btn" id="comment-section-btn" popovertarget="comment-section" popovertargetaction="toggle" aria-haspopup="menu" aria-expanded="false" aria-controls="comment-section">
    <i class="fa-solid fa-comments"></i>
  </button>

  <div class="main-comment-count-container">
    <span id="comment-count">{{ $brand->comments_count }}</span> 
  </div>

  <div class="comment-section comment-side-panel popover" aria-label="comment-section"  id="comment-section" popover>
    <header class="comment-section-header">
      
      <div class="comment-image-title">
        @if ($brand->featuredImage)   
          <div class="brand-featured-image" style="background-image: url('{{ asset('storage/app/public' . $brand->featuredImage->image_path) }}')"></div>
        @endif
        
        <div>
          <p class="title">{{ $brand->title }}</p>
          <div class="comment-count-container">
            <span id="comment-count">{{ $brand->comments_count }}</span>
            @if ($brand->comments_count == 1)
              Comment
            @else
              Comments
            @endif
          </div>
        </div>
      </div>
    
      <button popovertargetaction="close" class="close-comment-section close btn" popovertarget="comment-section">
        <i class="fa-solid fa-xmark"></i>
        <div class="hover-caption">Close</div>
      </button>
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
        
        <select name="sort" id="sort-by">
          <option value="featured">Featured</option>
          <option value="newest">Newest</option>
          <option value="oldest">Oldest</option>
          <option value="most liked">Most Liked</option>
        </select>
      </div>

      <div class="active-filters">
        <div id="active-filters"></div>
      </div>
    </div>
      
    <div 
      class="comments-container" 
      id="comments-container" 
      data-brand-id="{{ $brand->id }}">
    </div>

    @auth
      <form
        id="comment-form"
        action="{{ route('comment.add', $brand) }}"
        method="POST"
        class="action-form"
        data-action="add-comment"
        data-brand-id="{{ $brand->id }}"
      >
        @csrf
        <input type="hidden" name="brand_id" value="{{ $brand->id }}">
        <textarea 
          name="comment_text" 
          id="comment_text" 
          rows="3" 
          required
          placeholder="Write your comment here..."
        >
        </textarea>
        <button 
          class="btn add-comment-btn white-btn" 
          type="submit" 
          aria-label="Post comment for {{ $brand->title }}"
        >
          Add Comment
        </button>
      </form>
    @endauth
    <button class="load-more-comments btn white-btn">
      Load More Comments
    </button>
    {{-- <div class="spinner" style="display: none;"></div> --}}
  </div>
</div>