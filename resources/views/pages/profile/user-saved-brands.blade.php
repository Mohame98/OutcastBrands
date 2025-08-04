<x-main-layout>
  <section class="user-saved-brands" id="user-saved-brands">
    <div class="container">
      <header class="home-page-header">
       <h1>Saved Brands</h1>
      </header>

      <div class="filter-container">

        <div class="filter-section">
          <div class="search-input">
            <div id="search" class="search-container">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input name="search" type="text" id="search-input" placeholder="Search" />
            </div>
          </div>

          <select name="sort" id="sort-by">
            <option value="featured">Featured</option>
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
        <div class="grid" id="brands-container"></div>
        <button type="button" class="load-more btn white-btn">Load more</button>
      </section>
    </div>
  </section>
  @include('layouts.newsletter')
</x-main-layout>