<x-main-layout>
  <section class="home-page">
    <div class="container">
      <header>
        <span>
          <i class="fa-regular fa-calendar"></i>
          {{ ucfirst(\Carbon\Carbon::now()->format('l, F j')) }}
        </span>
      </header>
      
      <x-brand-card :brand="$featuredBrand" :featured="true" />

      @foreach ($otherBrands as $brand)
      <x-brand-card :brand="$brand" :featured="false" />
      @endforeach
    </div>
  </section>
</x-main-layout>