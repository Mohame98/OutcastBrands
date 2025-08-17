import { createNode } from '../helpers.js';
import { initComments } from './filterComments.js';
import { initBrands } from './filterBrands.js';

const resetSearchBtn = createNode("i", null, null, "fa-solid fa-xmark");
const filters = new URLSearchParams(window.location.search);

function updateQuery() {
  filters.set("page", 1);
  const queryString = filters.toString();
  const newUrl = `${window.location.pathname}?${queryString}`;
  window.history.replaceState(null, "", newUrl);

  const brandsContainer = document.querySelector("#brands-container");
  if (brandsContainer) {
    brandsContainer.innerHTML = '';
    initBrands(filters);
  }

  const commentsContainer = document.querySelector('#comments-container');
  if (commentsContainer) {
    commentsContainer.innerHTML = '';
    initComments(filters);
  }
}

function handleFilterButtons() {
  const filterButtons = document.querySelectorAll(".filter-btn");
  filterButtons.forEach((button) => {
    button.addEventListener("click", (e) => {
      if (button.classList.contains("active")) return;
      e.preventDefault();
      const [key, value] = button.dataset.filter.split(":");
      if (value === "all") {
        filters.delete(key);
      } else {
        filters.set(key, value);
      }
      updateQuery();
    });
  });
}

function handleSearchInput() {
  const searchInput = document.querySelector("#search-input");
  if (!searchInput) return;
  searchInput.addEventListener("input", (e) => {
    const searchValue = e.target.value;
    if (searchValue) {
      filters.set("search", searchValue);
    } else {
      filters.delete("search");
    }
    updateQuery();
  });
}

function handleFilterCheckboxes() {
  const filterCheckboxes = document.querySelectorAll(".filter-checkbox");
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
  const sortSelect = document.querySelector("#sort-by");
	if (!sortSelect) return;
  sortSelect.addEventListener("change", () => {
    filters.set("sort", sortSelect.value);
    if (sortSelect.value === 'featured') filters.delete('sort');
    updateQuery();
  });
}

function searchState(){
  const searchInput = document.querySelector("#search-input");
  if (!searchInput) return;
  const value = filters.get('search')
  searchInput.value = value
}

function sortDropdownActiveState() { 
  const sortSelect = document.querySelector("#sort-by");
  const currentSort = filters.get("sort");
  if (currentSort) {
    sortSelect.value = currentSort; 
  } else {
    sortSelect.value = "featured";
  }
}

function filterBtnActiveState() {
  const filterButtons = document.querySelectorAll(".filter-btn");
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
  const activeFiltersContainer = document.querySelector("#active-filters");
	if (!activeFiltersContainer) return;
  activeFiltersContainer.textContent = "";
  filters.forEach((value, key) => {
    value.split(",").forEach((val) => {
      const x = createNode("i", null, null, "fa-solid fa-xmark");
      const filterElement = createNode("button", `${key}: ${val}`, activeFiltersContainer, 'active-filter-btn');
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
  const activeFiltersContainer = document.querySelector("#active-filters");
  if (filters.size >= 2) {
    const resetButton = createNode("button", "Clear", activeFiltersContainer, "reset-btn");
    resetButton.addEventListener("click", resetFilters);
  }
}

function resetSearch() {
  const searchInput = document.querySelector("#search-input");
  const searchContainer = document.querySelector('.search-container');
  if (!searchInput || !searchContainer) return;
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
}

function checkboxFilterBtnState() {
  const filterCheckboxes = document.querySelectorAll(".filter-checkbox");
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

function initFilterSystem() {
  handleFilterCheckboxes();
  handleFilterButtons();
  handleSearchInput();
  handleSortSelect();
  resetSearch();
  updateActiveFilters();

  initComments(filters);
  initBrands(filters);
}

export {
  initFilterSystem,
  updateActiveFilters
};