@php
  $vote = $brand->voters->firstWhere('id', auth()->id())?->pivot->vote ?? null;
@endphp

<div class="social-interactions voting" data-brand-id="{{ $brand->id }}">
  <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}" data-action="vote" data-brand-id="{{ $brand->id }}">
    @csrf
    <input type="hidden" name="vote" value="1">
    <button 
      class="btn vote-btn upvote {{ $vote === 1 ? 'voted' : '' }}"
      aria-label="{{ $vote === 1 ? 'Remove vote from brand ' . $brand->title : 'Upvote brand ' . $brand->title }}" 
      title="{{ $vote === 1 ? 'Remove vote' : 'Upvote' }}"
      type="submit"
      >
      <i class="fa-solid fa-arrow-trend-up"></i>
    </button>
  </form>
  <span class="total-votes" aria-label="total votes is {{ $brand->total_votes }}">
    <p class="vote-count">
      {{ $brand->total_votes }} 
    </p>
  </span>
</div>