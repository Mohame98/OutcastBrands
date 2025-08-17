import { updateActiveFilters } from './mainFilterSys';
import { createNode } from '../helpers.js';

async function fetchBrandCards(queryString, filters) {
  const brandsPerPage = 6;
  try { 
    let response;
    const path = window.location.pathname;
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
    handleLoadMoreButton(data.has_more_brands, filters);
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
    createNode('p', 'No Brands Found', messageContainer, 'no-brands');
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

function loadMoreHandler(filters) {
  const loadMoreBtn = document.querySelector(".load-more");
  loadMoreBtn.disabled = true;
  let page = parseInt(filters.get("page")) || 1;

  page++;
  filters.set("page", page);
  const queryString = filters.toString();

  window.history.replaceState(null, "", `${window.location.pathname}?${queryString}`);
  updateActiveFilters();
  fetchBrandCards(queryString)
  .finally(() => {
    loadMoreBtn.disabled = false;
  });
}

let loadMoreListenerAttached = false;
function handleLoadMoreButton(hasMoreBrands, filters) {
  const loadMoreBtn = document.querySelector(".load-more");
  if (!loadMoreBtn) return;
  if (hasMoreBrands) {
    loadMoreBtn.style.display = 'block';
    if (!loadMoreListenerAttached) {
      loadMoreBtn.addEventListener('click', () => loadMoreHandler(filters));
      loadMoreListenerAttached = true;
    }
  } else {
    loadMoreBtn.style.display = 'none';
  }
}

function initBrands(filters){
  updateActiveFilters();
  const initialQuery = filters.toString();
  fetchBrandCards(initialQuery, filters)
}

export {
  initBrands
};