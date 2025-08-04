<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.css"
    integrity="sha512-wR4oNhLBHf7smjy0K4oqzdWumd+r5/+6QO/vDda76MW5iug4PT7v86FoEkySIJft3XA0Ae6axhIvHrqwm793Nw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Include Quill stylesheet -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

	<link href="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.1/dist/jquery.fancybox.min.css" rel="stylesheet" />
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<title>{{ $title ?? config('app.name', 'Laravel') }}</title>
</head>

<body>
	@include('components.flash-message')
	@include('layouts.nav')
	<main>
	{{ $slot }}
	</main>
	@include('layouts.footer')

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
		integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
		crossorigin="anonymous" referrerpolicy="no-referrer">
	</script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.js"
		integrity="sha512-eP8DK17a+MOcKHXC5Yrqzd8WI5WKh6F1TIk5QZ/8Lbv+8ssblcz7oGC8ZmQ/ZSAPa7ZmsCU4e/hcovqR8jfJqA=="
		crossorigin="anonymous" referrerpolicy="no-referrer">
	</script>

	<script src="https://cdn.jsdelivr.net/npm/@fancyapps/fancybox@3.5.1/dist/jquery.fancybox.min.js"></script>

	<script>
		

		const filters = new URLSearchParams(window.location.search);
		const filterButtons = document.querySelectorAll(".filter-btn");
		const activeFiltersContainer = document.querySelector("#active-filters");
		const searchInput = document.querySelector("#search-input");
		const sortSelect = document.querySelector("#sort-by");
        const resetSearchBtn = createNode("i", null, null, "fa-solid fa-xmark");
        const searchContainer = document.querySelector('.search-container');
        const filterCheckboxes = document.querySelectorAll(".filter-checkbox");
    
		function createNode(type, text, parentNode, className, href) {
            let node = document.createElement(type);
            if (text) node.appendChild(document.createTextNode(text));
            if (className) node.className = className;
            if (parentNode) parentNode.append(node);
            if (type === "img") node.src = text;
            if (href) node.href = href;
            return node;
        }

function updateQuery() {
    currentPage = 1;
    commentsPage = 1
    filters.set("page", currentPage);
    filters.set("page", commentsPage);

    const queryString = filters.toString();

    console.log(queryString)

    const newUrl = `${window.location.pathname}?${queryString}`;

    console.log(newUrl)
    window.history.replaceState(null, "", newUrl);

    const brandsContainer = document.querySelector("#brands-container");
    if (brandsContainer) {
        brandsContainer.innerHTML = '';
        fetchBrandCards(queryString, currentPage);
    }

    const commentsContainer = document.querySelector('#comments-container');
    if (commentsContainer) {
        const brandId = commentsContainer.dataset.brandId;
        commentsContainer.innerHTML = '';
        fetchComments(brandId, queryString, commentsPage);
    }
}

function handleFilterButtons() {
    filterButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            if (button.classList.contains("active")) return;
            e.preventDefault();
            const [key, value] = button.dataset.filter.split(":");
            if (value === "all") {
                filters.delete(key);
                console.log(filters, key);
            } else {
                filters.set(key, value);
            }
            updateQuery();
        });
    });
}

function handleSearchInput() {
	if (!searchInput) return;
    searchInput.addEventListener("input", (e) => {
        const searchValue = e.target.value.trim();
        if (searchValue.length <= 2) return;
        if (searchValue) {
            filters.set("search", searchValue);
        } else {
            filters.delete("search");
        }
        updateQuery();
    });
}

function handleFilterCheckboxes() {
    filterCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", () => {
            const filterData = checkbox.getAttribute("data-filter");
            const [filterName, filterValue] = filterData.split(":");
            const existingValues = getExistingValues(filterName);
            updateValues(existingValues, filterValue, checkbox.checked, filterName);
            updateQuery();
        });
    });
}

function getExistingValues(filterName) {
    return filters.get(filterName)?.split(",") || [];
}

function updateValues(existingValues, filterValue, isChecked, filterName) {
    if (isChecked) {
        if (!existingValues.includes(filterValue)) {
            existingValues.push(filterValue);
        }
    } else {
        const index = existingValues.indexOf(filterValue);
        if (index > -1) {
            existingValues.splice(index, 1);
        }
    }
    if (existingValues.length > 0) {
        filters.set(filterName, existingValues);
    } else {
        filters.delete(filterName);
    }
}

function handleSortSelect() {
	if (!sortSelect) return;
    sortSelect.addEventListener("change", () => {
        filters.set("sort", sortSelect.value);
        if (sortSelect.value === 'featured') filters.delete('sort');
        updateQuery();
    });
}

function searchState(){
    if (!searchInput) return;
    const value = filters.get('search')
    searchInput.value = value
}

function sortDropdownActiveState() { 
    const currentSort = filters.get("sort");
    if (currentSort) {
        sortSelect.value = currentSort; 
    } else {
        sortSelect.value = "featured";
    }
}

function filterBtnActiveState() {
    filterButtons.forEach((button) => {
        const [key, value] = button.dataset.filter.split(":");
        if (filters.has(key)) {
            button.classList.toggle("active", filters.get(key) === value);
        } else {
            button.classList.toggle("active", value === "all");
        }
    });
}

function updateActiveFilters() {
	if (!activeFiltersContainer) return;
    activeFiltersContainer.textContent = "";
    filters.forEach((value, key) => {
        value.split(",").forEach((val) => {
            const x = createNode("i", null, null, "fa-solid fa-xmark");
            const filterElement = createNode("button", `${key}: ${val}`, activeFiltersContainer, 'filter-btn');
            filterElement.append(x);
            
            if (key === 'page' && val === '1') filterElement.remove(); 
            
            filterElement.addEventListener("click", () => {
                removeFilterValue(key, val);
            });
        });
    });
    addResetBtn();
    updateButtonStates();
}

function removeFilterValue(key, value) {
    const currentValues = filters.get(key)?.split(",") || [];
    const updatedValues = currentValues.filter((v) => v !== value);
    if (updatedValues.length > 0) {
        filters.set(key, updatedValues.join(","));
    } else {
        filters.delete(key);  
        if (key === 'search') resetSearchBtn.remove();
        const inputVal = document.querySelectorAll(`input[name="${key}"]`);
        inputVal.forEach((input) => input.value = "");
    }
    updateQuery();
}

function addResetBtn() {
    if (filters.size >= 2) {
        const resetButton = createNode("button", "Clear", activeFiltersContainer, "reset-btn");
        resetButton.addEventListener("click", resetFilters);
    }
}

function resetSearch() {
    if (!searchInput || !resetSearchBtn || !searchContainer) return;
    resetSearchBtn.addEventListener("click", function () {
        filters.delete('search');
        searchInput.value = '';
        searchInput.focus();
        resetSearchBtn.remove();
        updateQuery();  
    });

    searchInput.addEventListener('input', function (e) {
        const value = e.target.value.trim();
        if (value && !searchContainer.contains(resetSearchBtn)) {
            searchContainer.append(resetSearchBtn);
        } else if (!value && searchContainer.contains(resetSearchBtn)) {
            resetSearchBtn.remove();
        }
    });
}

function resetFilters() {
    while (filters.size > 0) {
        filters.forEach((_, key) => {
            filters.delete(key)
            const inputVal = document.querySelectorAll(`input[name="${key}"]`);
            inputVal.forEach((input) => input.value = "");
            if (key === 'search') resetSearchBtn.remove();
        });
    }
    updateQuery();
    updateActiveFilters();
}

function checkboxFilterBtnState() {
    filterCheckboxes.forEach((checkbox) => {
        const filterData = checkbox.getAttribute("data-filter");
        const [filterName, filterValue] = filterData.split(":");
        const currentValues = filters.get(filterName) ? filters.get(filterName).split(",") : [];
        checkbox.checked = currentValues.includes(filterValue);
    });
}

function updateButtonStates() {
    filterBtnActiveState();

    checkboxFilterBtnState(); 
   
    sortDropdownActiveState();
   
    searchState();
}

let currentPage = parseInt(filters.get("page")) || 1;
const brandsPerPage = 6;
let loadMoreListenerAttached = false;
let currentQueryString = "";

async function fetchBrandCards(queryString, page) {
    try {
    
        let response;
        const path = window.location.pathname;
        currentQueryString = queryString;
        const paginatedQuery = `${queryString}&limit=${brandsPerPage}`;
        
        switch (true) {
            case /^\/profile\/\d+/.test(path):
                const userId = path.match(/\/profile\/(\d+)/)[1];
                response = await fetch(`/api/profile/${userId}/brands?${paginatedQuery}`);
                break;

            case path === '/saved-brands/profile':
                response = await fetch(`/api/saved-brands/profile?${paginatedQuery}`);
                break;

            case path === '/search':
                response = await fetch(`/api/brands/search?${paginatedQuery}`);
                break;

            default:
                return;
        }

        if (!response || !response.ok) throw new Error("Failed to fetch brand cards");

        const data = await response.json();
        renderBrandCards(data.html_cards.join(""));
        handleLoadMoreButton(data.has_more_brands);
        updateActiveFilters();

        console.log("Page:", currentPage, "Brands per page:", brandsPerPage);
    } catch (error) {
        console.error("Error fetching brand cards:", error);
    }
}

function renderBrandCards(cardsHtml) {
    const container = document.querySelector("#brands-container");
    if (!container) return;

    const parser = new DOMParser();
    const doc = parser.parseFromString(cardsHtml, 'text/html');
    const cards = doc.body.children;

    if (cards.length === 0) {
        const messageContainer = createNode('div', null, container, 'no-brands');
        const message = createNode('p', 'No Brands Found', messageContainer, 'no-brands');
        return;
    }

    Array.from(cards).forEach(el => {
        el.classList.add('fade-in');
        container.appendChild(el);
        el.addEventListener('animationend', () => {
            el.classList.remove('fade-in');
        });
    });
}

// Define the handler once
function loadMoreHandler() {
    const loadMoreBtn = document.querySelector(".load-more");
    loadMoreBtn.disabled = true;

    currentPage++;
    filters.set("page", currentPage);
    const queryString = filters.toString();
    window.history.replaceState(null, "", `${window.location.pathname}?${queryString}`);

    fetchBrandCards(queryString, currentPage)
    
    .finally(() => {
        loadMoreBtn.disabled = false;
    });
}

function handleLoadMoreButton(hasMoreBrands) {
    const loadMoreBtn = document.querySelector(".load-more");

    if (!loadMoreBtn) return;

    if (hasMoreBrands) {
        loadMoreBtn.style.display = 'inline-block';
        if (!loadMoreListenerAttached) {
            loadMoreBtn.addEventListener('click', loadMoreHandler);
            loadMoreListenerAttached = true;
        }
    } else {
        loadMoreBtn.style.display = 'none';
    }
}


	function initFilterSystem() {

    handleFilterCheckboxes();
    handleFilterButtons();
    handleSearchInput();
    handleSortSelect();
   
    resetSearch();

    const initialQuery = filters.toString();
    fetchBrandCards(initialQuery, currentPage);
    updateActiveFilters();
    // updateQuery();
   
}

document.addEventListener("DOMContentLoaded", initFilterSystem);

let commentsPage = 1;
const commentsPerPage = 5;

async function fetchComments(brandId, queryString, page) {
  const paginatedQuery = `${queryString}&page=${page}&limit=${commentsPerPage}`;

  const response = await fetch(`/api/brands/${brandId}/comments?${paginatedQuery}`);
  if (!response.ok) throw new Error("Failed to fetch comments");

  const data = await response.json();

  renderComments(data.html_comments.join(""), page);
  handleLoadMoreCommentsButton(data.has_more_comments, brandId, queryString);
  updateActiveFilters();
  console.log("Page:", commentsPage, "comments per page:", commentsPerPage);
}

function renderComments(html, page) {
  const container = document.querySelector("#comments-container");
  if (!container) return;

    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const comments = doc.body.children;

    Array.from(comments).forEach(el => container.appendChild(el));
}

function initComments() {
  const commentsContainer = document.querySelector('#comments-container');
  if (!commentsContainer) return;

  updateActiveFilters();

  const brandId = commentsContainer.dataset.brandId;
//   commentsPage = 1;
//   filters.set("page", commentsPage);

  const initialQuery = filters.toString();
  fetchComments(brandId, initialQuery, commentsPage);
}

let loadMoreCommentsListenerAttached = false;

function handleLoadMoreCommentsButton(hasMore, brandId, queryString) {
  const btn = document.querySelector(".load-more-comments");
//   const spinner = document.querySelector(".spinner");
  if (!btn) return;

  if (hasMore) {
    btn.style.display = 'block';
    if (!loadMoreCommentsListenerAttached) {
      btn.addEventListener("click", () => {
        btn.disabled = true;
        // spinner.style.display = 'block'; 
        commentsPage++;
        filters.set("page", commentsPage);
        const queryString = filters.toString();
        window.history.replaceState(null, "", `${window.location.pathname}?${queryString}`);
        fetchComments(brandId, queryString, commentsPage)
          .finally(() => {
            btn.disabled = false;
            // spinner.style.display = 'none';
        });
      });
      loadMoreCommentsListenerAttached = true;
    }
  } else {
    btn.style.display = 'none';
  }
}

document.addEventListener("DOMContentLoaded", initComments);


document.querySelectorAll('input, textarea').forEach(input => {
    input.value = input.value.trim();
});

	</script>

   

    <!-- Include Quill JS -->
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('#editor-container');
        if (!container) return;
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ 'font': [] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'align': [] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        ['link', 'video', 'image', { 'code-block': true }],
                    ],
                    handlers: {
                        bold: function(value) { applyFormat.call(this, 'bold', value); },
                        italic: function(value) { applyFormat.call(this, 'italic', value); },
                        underline: function(value) { applyFormat.call(this, 'underline', value); },
                        strike: function(value) { applyFormat.call(this, 'strike', value); }
                    }
                }
            }
        });

        function applyFormat(format, value) {
            this.quill.format(format, value);
            setTimeout(() => this.quill.focus(), 0);
        }

        const content = document.querySelector('input[name=description]');
        document.querySelector('form').addEventListener('submit', function(e) {
            content.value = quill.root.innerHTML;
        });

        document.querySelectorAll('.ql-toolbar button, .ql-toolbar span').forEach(el => {
            el.addEventListener('mousedown', e => e.preventDefault());
        });

        document.querySelector('#quillDeleteButton').addEventListener('click', () => {
            quill.setText('');
            content.value = '';
        });
    });

        
    </script>
</body>
</html>
