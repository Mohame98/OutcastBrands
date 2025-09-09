<div class="popover-container comments item" id="comments-section">
  <button class="btn comment-section-btn item-btn" id="comment-section-btn" popovertarget="comment-section" popovertargetaction="toggle" aria-haspopup="menu" aria-expanded="false" aria-controls="comment-section" title="Comment Section">
    <i class="fa-solid fa-comments"></i>
  </button>

  <div class="main-comment-count-container">
    <span id="comment-count">{{ $brand->comments_count }}</span> 
  </div>

  <aside class="comment-section comment-side-panel popover" aria-label="comment-section"  id="comment-section" popover open>
    <header class="comment-section-header">
      <div class="comment-image-title">
        @if ($brand->featuredImage)   
          <div class="brand-featured-image" style="background-image: url('{{ asset('storage/' . $brand->featuredImage->image_path) }}')"></div>
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

    @auth
    <div class="modal-wrapper add-comment">
      <button 
        class="btn add-comment-modal-btn modal-btn white-btn"
        aria-haspopup="add-comment" 
        title="Add a Comment"
        aria-controls="add-comment"  
        aria-expanded="false">
          <i class="fa-solid fa-comment-dots"></i>
          <span class="btn-text">Add Comment</span>
      </button>
      <dialog id="add-comment" class="add-comment-modal add-comment">
        <form
          id="comment-form"
          action="{{ route('comment.add', $brand) }}"
          method="POST"
          class="action-form"
          data-action="add-comment"
          data-brand-id="{{ $brand->id }}"
        >
          <fieldset class="add-comment-field" id="add-comment-field">
            <header class="modal-headers">
              <h1>Add Comment</h1>
              @include('components.close-modal')
            </header>
            <p>Speak your mind (respectfully 😇)</p>
            @csrf
            <div class="form-group">
              <input type="hidden" name="brand_id" value="{{ $brand->id }}">
              <label for="add_comment_text">
                <span>Add Comment</span>
                <textarea 
                  name="add_comment_text" 
                  id="add_comment_text" 
                  rows="3" 
                  required
                  placeholder="Write your comment here..."
                  autofocus
                  >
                </textarea>
              </label>
              <x-form-error name="add_comment_text" />
            </div>
            <div class="btn-container">
              <button type="button" class="btn cancel close-modal">
                Cancel
              </button>
              <button 
              class="btn add-comment-btn update" 
              type="submit" 
              aria-label="Post comment for {{ $brand->title }}"
              >
                Add
              </button>
            </div>
          </fieldset>   
        </form>
      </dialog>
    </div>
    @endauth

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
          <option value="featured">Default</option>
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

    <button class="load-more-comments btn white-btn">
      Load More Comments
    </button>
  </aside>
</div>