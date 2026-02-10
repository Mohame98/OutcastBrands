@php 
  $brandUser = $comment->user;
  $isLiked = (bool) ($comment->is_liked ?? false); 
@endphp
<article class="comment-item" id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
  <div class="comment-user">
    @if($brandUser)
      <div class="brand-author">
        <a href="{{ route('profile.show', $brandUser) }}">
          @if($brandUser->profile_image)
            <div 
              class="brand-author-profile image" 
              style="background-image: url('{{ asset('storage/' . $brandUser->profile_image) }}')">
            </div>
          @else
            <div class="brand-author-profile letter">
              {{ strtoupper(substr($brandUser->email, 0, 1)) }}
            </div>
          @endif
        </a>
      </div>
    @endif
  </div>
  <div class="comment-content">
    <div class="comment-meta">
      <span class="username">
        @if($brandUser)
        <a href="{{ route('profile.show', $brandUser) }}"> 
          <p class="brand-author-username">{{ Str::limit($brandUser->username, 12, '...') }}</p>
        </a>
        @endif
      </span>
      <span class="timestamp">{{ shortTimeDiff($comment->created_at) }}</span>
    </div>
    <p class="comment-text">{!! $comment->comment_text !!}</p>
    <div class="comment-actions">
      <div class="comment-like-container">
        <form class="action-form like-form" method="POST" action="{{ route('comments.like', $comment) }}" data-action="like-comment">
          @csrf
          <button 
            type="submit" 
            class="btn like-btn {{ $isLiked ? 'liked' : '' }} action-btn"
            aria-label="{{ $isLiked ? 'Remove like' : 'Like' }}"
          >
            <i class="fa-{{ $isLiked ? 'solid' : 'regular' }} fa-heart"></i>
          </button>
        </form>

        <span class="total-likes" aria-label="Total likes: {{ $comment->likes_count }}">
          <p class="like-count">{{ $comment->likes_count ?? 0 }}</p>
        </span>
      </div>

      @if(auth()->id() === $comment->user_id)
      <div class="modal-wrapper">
        <button class="btn edit-comment-btn modal-btn" aria-controls="edit-comment-modal">
          Edit
        </button>
        <dialog id="edit-comment-modal" class="edit-comment-modal">
          <form method="POST" action="{{ route('comments.edit', $comment) }}" class="edit-comment-form action-form" data-action="edit-comment" data-submission="true">
            <fieldset class="edit-comment-field">
              <header class="modal-headers">
                <h1>Update Comment</h1>
                @include('components.close-modal')
              </header>
              @csrf
              @method('PUT')
              <div class="form-group">
                <label for="commentText-{{ $comment->id }}">
                  <span>Edit Comment</span>
                  <textarea name="comment_text" id="commentText-{{ $comment->id }}" required>{{ $comment->comment_text }}</textarea>
                </label>
                <x-form-error name="comment_text" />
              </div>
              <div class="btn-container">
                <button type="button" class="btn cancel close-modal">Cancel</button>
                <button class="edit-comment-btn btn update" type="submit">Save</button>
              </div>
            </fieldset>
          </form>
        </dialog>
      </div>

      <div class="modal-wrapper">
        <button class="btn delete-comment-btn modal-btn" aria-controls="delete-comment-modal">
          Delete
        </button>
        <dialog id="delete-comment-modal" class="delete-comment-modal">
          <form method="POST" action="{{ route('comments.delete', $comment) }}" class="delete-comment-form action-form" data-action="delete-comment" data-submission="true">
            <fieldset class="delete-comment-field">
              <header class="modal-headers">
                <h1>Delete Comment</h1>
                @include('components.close-modal')
              </header>
              @csrf
              @method('DELETE')
              <p>Are you sure you want to delete this comment?:</p>                              
              <p class="color-blue">{{ $comment->comment_text }}</p>
              <div class="btn-container">
                <button type="button" class="btn cancel close-modal">Cancel</button>
                <button class="btn delete-comment-btn update" type="submit">Yes Delete</button>
              </div>
            </fieldset>
          </form>
        </dialog>
      </div>
      @endif
    </div>
  </div>
  @auth
  <div class="context-menu-container">
    <button 
      class="btn context-menu-btn" 
      id="context-menu-btn" 
      popovertarget="comment-menu-{{ $comment->id }}" 
      popovertargetaction="toggle" 
      aria-haspopup="menu" 
      title="context-menu" 
      aria-expanded="false" 
      aria-controls="comment-menu-{{ $comment->id }}">    
        <i class="fa-solid fa-ellipsis"></i>
    </button>
    <nav class="context-menu popover" id="comment-menu-{{ $comment->id }}" aria-label="context menu" popover>
      <ul class="comment-ul">
        <x-interactions.report type="comment" :model="$comment" />
      </ul>
    </nav>
  </div>
  @endauth
</article>