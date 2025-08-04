
@php $brandUser = $comment->user ?? null; @endphp
@php $isLiked = $comment->likes->contains('user_id', auth()->id()); @endphp

<div class="comment " id="comment-{{ $comment->id }}" data-comment-id="{{ $comment->id }}">
    <div class="comment-container">
        <div class="comment-user">
            @if($brandUser)
            <div class="brand-author">
                <a href="{{ route('profile.show', $brandUser) }}">
                    @if($brandUser->profile_image)
                    <div 
                        class="brand-author-profile image" 
                        style="background-image: url('{{ asset('storage/app/public/' . $brandUser->profile_image) }}')">
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

        <div class="comment-flex">
            <div class="comment-details">
                <div class="comment-user-date">
                    <div>
                        <a href="{{ route('profile.show', $brandUser) }}"> 
                            <p class="brand-author-username">{{ $brandUser->username }}</p>
                        </a>
                        <i class="fa-solid fa-circle"></i>
                        <span>{{ shortTimeDiff($comment->created_at) }}</span>
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
                            <ul>
                                {{-- <li class="context-item">Copy Url</li> --}}
                                <x-interactions.report type="comment" :model="$comment" />
                            </ul>
                        </nav>
                    </div>
                    @endauth
                </div>
                <p>{{ $comment->comment_text }}</p>   
            </div>

            <div class="comment-actions">
                <div class="comment-like-container">
                    <form class="action-form like-form" method="POST" action="{{ route('comments.like', $comment) }}" data-action="like-comment">
                        @csrf
                        <button 
                            type="submit" 
                            class="btn like-btn {{ $isLiked ? 'liked' : '' }}"
                            aria-label="{{ $isLiked ? 'Remove like from comment' : 'Like this comment' }}"
                        >
                            {!! $isLiked 
                                ? '<i class="fa-solid fa-heart"></i>' 
                                : '<i class="fa-regular fa-heart"></i>' !!}
                        </button>
                    </form>

                    <span class="total-likes" aria-label="Total likes: {{ $comment->likes_count }}">
                        <p class="like-count">{{ $comment->likes_count }}</p>
                    </span>
                </div>

                @if(auth()->id() === $comment->user_id)
                <div class="modal-wrapper">
                    <button 
                    class="btn edit-comment-btn modal-btn"
                    aria-haspopup="show get in touch form" 
                    aria-controls="edit-comment-modal" 
                    aria-expanded="false">
                        Edit
                    </button>
                    <dialog id="edit-comment-modal" class="edit-comment-modal">
                        <form method="POST" action="{{ route('comments.edit', $comment) }}" class="edit-comment-form action-form" data-action="edit-comment">
                            <fieldset class="edit-comment-field" id="edit-comment-field">
                                <header class="modal-headers">
                                    <h1>Edit or Update Comment</h1>
                                    @include('components.close-modal')
                                </header>

                                @csrf
                                @method('PUT')
                                 <div class="form-group">
                                    <label for="commentText-{{ $comment->id }}">
                                        <span>Edit Comment</span>
                                        <textarea 
                                            name="comment_text" 
                                            id="commentText-{{ $comment->id }}" 
                                            required
                                        >
                                            {{ old('comment_text', $comment->comment_text) }}
                                        </textarea>
                                    </label>
                                    <x-form-error name="comment_text" />
                                </div>
                                <div class="btn-container">
                                    <button type="button" class="btn cancel close-modal">
                                        Cancel
                                    </button>
                                    <button class="edit-comment-btn btn update" type="submit">Save</button>
                                </div>
                            </fieldset>
                        </form>
                    </dialog>
                </div>

                <div class="modal-wrapper">
                    <button 
                        class="btn delete-comment-btn modal-btn"
                        aria-haspopup="show get in touch form" 
                        aria-controls="delete-comment-modal" 
                        aria-expanded="false"
                    >
                        Delete
                    </button>
                    <dialog id="delete-comment-modal" class="delete-comment-modal">
                        <form method="POST" action="{{ route('comments.delete', $comment) }}" class="delete-comment-form action-form" data-action="delete-comment">
                            <fieldset class="delete-comment-field" id="delete-comment-field">
                                <header class="modal-headers">
                                    <h1>Delete Comment</h1>
                                    @include('components.close-modal')
                                </header>

                                @csrf
                                @method('DELETE')
                                <P>Are you sure you want to delete this comment?</P>
                                <p>{{ $comment }}</p>
                                <div class="btn-container">
                                    <button type="button" class="btn cancel close-modal">
                                        Cancel
                                    </button>
                                    <button class="btn delete-comment-btn update btn" type="submit">Yes Delete Comment</button>
                                </div>
                            </fieldset>
                        </form>
                    </dialog>
                </div>
            @endif
            </div>
        </div>
    </div>  
</div>

<script>
</script>

