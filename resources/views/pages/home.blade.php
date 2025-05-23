<x-main-layout>
  <section class="home-page">
    <div class="container">
      <header class="home-page-header">
        <span>
          <i class="fa-regular fa-calendar"></i>
          {{ ucfirst(\Carbon\Carbon::now()->format('l, F j')) }}
        </span>
      </header>

      @if ($featuredBrand)
        <x-brand-card :brand="$featuredBrand" :featured="true" />
      @endif

      @if ($otherBrands->isNotEmpty())
      <div class="popular-brands-top-layer">
        <header class="popular-brands-header">
          <h2>Popular Brands</h2>
        </header>
        @foreach ($otherBrands as $brand)
          <x-brand-card :brand="$brand" :featured="false" />
        @endforeach
      </div>
      @endif
    </div>
  </section>
</x-main-layout>