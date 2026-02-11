<div class="social-interactions save" data-brand-id="{{ $brand->id }}">
  <div class="save context-item">
    <form method="POST" action="{{ route('brands.save', $brand) }}" class="action-form" data-action="save" data-brand-id="{{ $brand->id }}">
      @csrf
      <button class="btn save-btn card item-btn" type="submit" title="Save Brand" aria-label="save brand {{ $brand->title }}" data-close-details 
        data-brand-id="{{ $brand->id }}">
        {!! $brand->savers->contains(auth()->id()) 
          ? '<i class="fa-solid fa-bookmark"></i>' 
          : '<i class="fa-regular fa-bookmark"></i>' !!}
      </button>
    </form>
  </div>
</div>