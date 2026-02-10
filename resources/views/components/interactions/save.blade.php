<li class="save context-item">
  <form method="POST" action="{{ route('brands.save', $brand) }}" class="action-form" data-action="save">
    @csrf
    <button class="btn save-btn item-btn" type="submit" aria-label="save brand {{ $brand->title }}" data-close-details>
      {!! $brand->savers->contains(auth()->id()) ? '<i class="fa-solid fa-bookmark"></i> Saved' : '<i class="fa-regular fa-bookmark"></i> Save' !!}
    </button>
  </form>
</li>