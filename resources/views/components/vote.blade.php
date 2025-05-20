<div class="voting">
  <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}">
    @csrf
    <input type="hidden" name="vote" value="1">
    <button type="submit" class="btn vote-btn upvote">
      <i class="fa-solid fa-arrow-trend-up"></i>
    </button>
  </form>

  <span class="total-votes">
    <p>Total Votes: {{ $brand->total_votes }}</p>
  </span>

  <form class="action-form vote-form" method="POST" action="{{ route('brands.vote', $brand) }}">
    @csrf
    <input type="hidden" name="vote" value="-1">
    <button type="submit" class="btn vote-btn downvote">
      <i class="fa-solid fa-arrow-trend-down"></i>
    </button>
  </form>
</div>

<style>
  .voting{
    display: flex;
    gap: 20px
  }
</style>