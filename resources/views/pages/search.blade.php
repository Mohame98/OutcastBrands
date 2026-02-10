<x-main-layout>
  <section class="search-page" id="search-page">
    <div class="container">
      @php
      $categories = [
        'Footwear', 'Accessories', 'Outerwear', 'Casual', 'Formal',
        'Activewear', 'Streetwear', 'Minimalist', 'Vintage', 'Preppy',
        'Seasonal', 'Luxury', 'Sustainable'
      ];
      @endphp

      <div class="modal-wrapper category-filter">
        <button 
          class="btn category-filter-btn modal-btn white-btn"
          aria-haspopup="show get in touch form" 
          aria-controls="category-filter-modal" 
          aria-expanded="false">
          <i class="fa-solid fa-filter"></i>
          Filter by Category
        </button>
        <dialog id="category-filter-modal" class="category-filter-modal">
          <section id="filters">
            <header class="modal-headers">
							<h1>Brand Categories</h1>
							@include('components.close-modal')
						</header>
            <p>Select from a range of categories.</p>
            <div class="category-filters">
              @foreach ($categories as $category)
              <div class="filter-container">
                <input type="checkbox" name="category" value="{{ $category }}"
                  class="filter-checkbox" id="{{ $category }}"
                  data-filter="category:{{ $category }}">
                </input>
                <label class="category-button" for="{{ $category }}">{{ $category }}</label>
              </div>
              @endforeach
            </div>
            <div class="btn-container">
              <button type="button" class="btn cancel close-modal">
                Apply
              </button>
					  </div>
          </section>
        </dialog>
      </div>
      <div class="filter-container">
        <div class="filter-section">
          <div class="filter-btns">
            <button name="filter" value="all" data-filter="filter:all"
              class="filter-btn active">
              All Brands
            </button>

            <button name="filter" value="past-week" data-filter="filter:past-week"
              class="filter-btn">
              Past Week
            </button>

            <button name="filter" value="past-month" data-filter="filter:past-month"
              class="filter-btn">
              Past Month
            </button>

            <button name="filter" value="past-3-months" data-filter="filter:past-3-months"
              class="filter-btn">
              Past 3 Months
            </button>

            <button name="filter" value="past-year" data-filter="filter:past-year"
              class="filter-btn">
              Past Year
            </button>
          </div>

        </div>

        <div class="search-input">
          <div id="search" class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input name="search" type="text" id="search-input" placeholder="Search" />
          </div>

          <select name="sort" id="sort-by" class="sort-desktop">
            <option value="featured">Default</option>
            <option value="newest">Newest</option>
            <option value="oldest">Oldest</option>
            <option value="most popular">Most Popular</option>
          </select>
        </div>
        <div class="active-filters">
          <div id="active-filters">

          </div>
        </div>
      </div>

      <section class="popular-brands-top-layer">
        <p class="brands-count" id="brands-count" aria-live="polite"></p>
        <div class="grid" id="brands-container"></div>
        <button type="button" id="load-more-brands" class="load-more btn white-btn">Load more</button>
      </section>
      
    </div>
  </section>
  @include('layouts.newsletter')
</x-main-layout>