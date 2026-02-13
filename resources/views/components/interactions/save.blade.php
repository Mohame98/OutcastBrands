<li class="save context-item" data-brand-id="{{ $brand->id }}">
  <form method="POST" action="{{ route('brands.save', $brand) }}" class="action-form" data-action="save" data-brand-id="{{ $brand->id }}">
    @csrf
    <button class="btn save-btn item-btn context-btn" type="submit" aria-label="save brand {{ $brand->title }}" data-close-details
        data-brand-id="{{ $brand->id }}"
      >
      {!! $brand->savers->contains(auth()->id()) ? '<i class="fa-solid fa-bookmark"></i> Saved' : '<i class="fa-regular fa-bookmark"></i> Save' !!}
    </button>
  </form>
</li>