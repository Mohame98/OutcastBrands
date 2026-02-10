<div class="social-interactions save">
  <div class="save context-item">
    <form method="POST" action="{{ route('brands.save', $brand) }}" class="action-form" data-action="save">
      @csrf
      <button class="btn save-btn card item-btn" type="submit" title="Save Brand" aria-label="save brand {{ $brand->title }}" data-close-details>
        {!! $brand->savers->contains(auth()->id()) 
          ? '<i class="fa-solid fa-bookmark"></i>' 
          : '<i class="fa-regular fa-bookmark"></i>' !!}
      </button>
    </form>
  </div>
</div>