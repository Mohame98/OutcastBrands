<x-main-layout>
  <section class="">
    <div class="container">
      <h1>{{ $brand->title }}</h1>
      <p><strong>Description:</strong> {{ $brand->description }}</p>
      <p><strong>Created at:</strong> {{ $brand->created_at->format('M d, Y') }}</p>
    </div>
  </section>
</x-main-layout>