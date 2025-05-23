<div class="voting">
  <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}"  data-action="vote">
    @csrf
    <input type="hidden" name="vote" value="1">
    <button type="submit" class="btn vote-btn upvote" aria-label="Upvote brand">
      <i class="fa-solid fa-arrow-trend-up"></i>
      <span class="hover-caption">Upvote +1</span>
    </button>
  </form>

  <span class="total-votes" aria-label="total votes is {{ $brand->total_votes }}">
    <p class="vote-count">{{ $brand->total_votes }}</p>
  </span>

  <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}" data-action="vote">
    @csrf
    <input type="hidden" name="vote" value="-1">
    <button type="submit" class="btn vote-btn downvote" aria-label="downvote brand">
      <i class="fa-solid fa-arrow-trend-down"></i>
      <span class="hover-caption">Downvote -1</span>
    </button>
  </form>
</div>