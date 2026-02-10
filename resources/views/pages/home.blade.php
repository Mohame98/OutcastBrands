<x-main-layout>
  <section class="home-page">
    <div class="container">
      @if ($featuredBrand)
      <x-brand-card-types.featured-card :brand="$featuredBrand" :featured="true" />
      @endif

      @if ($otherBrands->isNotEmpty())
      <section class="popular-brands-top-layer">
        <header class="popular-brands-header">
          <h2>Popular Brands</h2>
        </header>
        <div class="grid">
          @foreach ($otherBrands as $brand)
          <x-brand-card-types.grid-brand-card :brand="$brand" />
          @endforeach
        </div>
        <a class="view-all-brands-btn white-btn" href="{{ route('search') }}">View All Brands</a>
      </section>
      @endif
    </div>
  </section>
  @include('layouts.newsletter')
</x-main-layout>