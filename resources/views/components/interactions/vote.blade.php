@php
  $vote = $brand->voters->firstWhere('id', auth()->id())?->pivot->vote ?? null;
@endphp

<div class="voting" data-brand-id="{{ $brand->id }}">
  <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}"  data-action="vote" data-brand-id="{{ $brand->id }}">
    @csrf
    <input type="hidden" name="vote" value="1">
      <button class="btn vote-btn upvote {{ $vote === 1 ? 'voted' : '' }}"
      aria-label="{{ $vote === 1 ? 'Remove upvote from brand ' . $brand->title : 'Upvote brand ' . $brand->title }}" type="submit">
        <i class="fa-solid fa-arrow-trend-up"></i>
        <span class="hover-caption">Upvote +1</span>
      </button>  
  </form>

  <span class="total-votes" aria-label="total votes is {{ $brand->total_votes }}">
    <p class="vote-count">{{ $brand->total_votes }}</p>
  </span>

  <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}" data-action="vote" data-brand-id="{{ $brand->id }}">
    @csrf
    <input type="hidden" name="vote" value="-1">
    <button class="btn vote-btn downvote {{ $vote === -1 ? 'voted' : '' }}"
        aria-label="{{ $vote === -1 ? 'Remove downvote from brand ' . $brand->title : 'Downvote brand ' . $brand->title }}" type="submit">
      <i class="fa-solid fa-arrow-trend-down"></i>
      <span class="hover-caption">Downvote -1</span>
    </button>
  </form>
</div>