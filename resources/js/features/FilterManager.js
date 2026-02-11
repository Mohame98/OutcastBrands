/**
 * FilterManager - Manages all filtering operations
 * Coordinates between state, API, and UI for filtering functionality
 */

import { CONFIG } from "../config.js";
import { state } from "../core/StateManager.js";
import { dom } from "../core/DOMManager.js";
import APIService from "../services/APIService.js";
import UIService from "../services/UIService.js";
import { createNode } from "../utils/helpers.js";

class FilterManager {
  constructor() {
    this.resetSearchBtn = createNode("i", null, null, "fa-solid fa-xmark");
  }

  /**
   * Initialize filter system
   */
  init() {
    this.attachEventListeners();
    this.updateActiveFiltersDisplay();
    this.updateButtonStates();
  }

  /**
   * Attach all event listeners
   */
  attachEventListeners() {
    this.handleSearchInput();
    this.handleFilterButtons();
    this.handleFilterCheckboxes();
    this.handleSortSelect();
    this.handleResetSearch();
  }

  /**
   * Handle search input with debouncing
   */
  handleSearchInput() {
    const searchInput = dom.get("searchInput");
    if (!searchInput) return;

    searchInput.addEventListener("input", (e) => {
      const searchValue = e.target.value; 

      state.debounce(
        "search",
        () => {
          // 2. Only trim when we are actually ready to apply the filter
          const cleanValue = searchValue.trim();
          
          if (cleanValue) {
            state.setFilter("search", cleanValue);
          } else {
            state.deleteFilter("search");
          }
          this.applyFilters();
        },
        CONFIG.SEARCH_DEBOUNCE_MS,
      );
    });
  }

  /**
   * Handle filter button clicks
   */
  handleFilterButtons() {
    const buttons = dom.getAll(CONFIG.SELECTORS.filterBtn);

    buttons.forEach((button) => {
      button.addEventListener("click", (e) => {
        if (button.classList.contains(CONFIG.CLASSES.active)) return;

        e.preventDefault();
        const [key, value] = button.dataset.filter.split(":");

        if (value === "all") {
          state.deleteFilter(key);
        } else {
          state.setFilter(key, value);
        }

        this.applyFilters();
      });
    });
  }

  /**
   * Handle filter checkbox changes
   */
  handleFilterCheckboxes() {
    const checkboxes = dom.getAll(CONFIG.SELECTORS.filterCheckbox);

    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener("change", () => {
        const [filterName, filterValue] =
          checkbox.dataset.filter.split(":");
        const currentValues = this.getFilterValues(filterName);

        if (checkbox.checked) {
          if (!currentValues.includes(filterValue)) {
            currentValues.push(filterValue);
          }
        } else {
          const index = currentValues.indexOf(filterValue);
          if (index > -1) currentValues.splice(index, 1);
        }

        if (currentValues.length > 0) {
          state.setFilter(filterName, currentValues.join(","));
        } else {
          state.deleteFilter(filterName);
        }
        this.applyFilters();
      });
    });
  }

  /**
   * Handle sort select changes
   */
  handleSortSelect() {
    const sortSelect = dom.get("sortSelect");
    if (!sortSelect) return;

    sortSelect.addEventListener("change", () => {
      const value = sortSelect.value;

      if (value === "featured") {
        state.deleteFilter("sort");
      } else {
        state.setFilter("sort", value);
      }
      this.applyFilters();
    });
  }

  /**
   * Handle reset search button
   */
  handleResetSearch() {
    const searchInput = dom.get("searchInput");
    const searchContainer = dom.get("searchContainer");

    if (!searchInput || !searchContainer) return;

    this.resetSearchBtn.addEventListener("click", () => {
      state.clearDebounce("search");
      state.deleteFilter("search");
      searchInput.value = "";
      searchInput.focus();
      this.resetSearchBtn.remove();
      this.applyFilters();
    });

    searchInput.addEventListener("input", (e) => {
      const value = e.target.value;
      if (value.length > 0 && !searchContainer.contains(this.resetSearchBtn)) {
        searchContainer.appendChild(this.resetSearchBtn);
      } else if (value.length === 0 && searchContainer.contains(this.resetSearchBtn)) {
        this.resetSearchBtn.remove();
      }
    });
  }

  /**
   * Apply all active filters
   */
  async applyFilters() {
    // Reset to page 1
    state.setFilter("page", 1);
    state.resetPage("brands");
    state.resetPage("comments");

    // Update URL
    state.syncToURL();

    // Create abort controller
    const abortController = state.createAbortController();
    const signal = abortController.signal;

    // Clear containers
    const brandsContainer = dom.get("brandsContainer");
    const commentsContainer = dom.get("commentsContainer");

    if (brandsContainer) {
      brandsContainer.innerHTML = "";
      UIService.showLoading(brandsContainer);
      await this.fetchAndRenderBrands(signal);
      UIService.clearLoading(brandsContainer);
    }

    if (commentsContainer) {
      commentsContainer.innerHTML = "";
      UIService.showLoading(commentsContainer);
      await this.fetchAndRenderComments(signal);
      UIService.clearLoading(commentsContainer);
    }

    // Update UI states
    this.updateActiveFiltersDisplay();
    this.updateButtonStates();
  }

  /**
   * Fetch and render brands
   * @param {AbortSignal} signal - Abort signal
   */
  async fetchAndRenderBrands(signal) {
    const queryString = state.getQueryString();
    const result = await APIService.fetchBrands(queryString, signal);

    if (result.aborted) return;

    if (!result.success) {
      UIService.showError(result.error || CONFIG.MESSAGES.FETCH_ERROR);
      return;
    }

    const { html_cards, has_more_brands, total } = result.data;
    UIService.renderBrands(html_cards.join(""));
    UIService.updateLoadMoreButton(has_more_brands);
    if (typeof total === "number") UIService.updateBrandsCount(total);
  }

  /**
   * Fetch and render comments
   * @param {AbortSignal} signal - Abort signal
   */
  async fetchAndRenderComments(signal) {
    const commentsContainer = dom.get("commentsContainer");
    if (!commentsContainer) return;

    const brandId = commentsContainer.dataset.brandId;
    if (!brandId) return;

    const queryString = state.getQueryString();
    const paginatedQuery = `${queryString}&page=1&limit=${CONFIG.COMMENTS_PER_PAGE}`;

    const result = await APIService.fetchComments(
      brandId,
      paginatedQuery,
      signal,
    );

    if (result.aborted) return;

    if (!result.success) {
      UIService.showError(result.error || CONFIG.MESSAGES.FETCH_ERROR);
      return;
    }

    const { html_comments, has_more_comments } = result.data;
    UIService.renderComments(html_comments.join(""));
    UIService.updateLoadMoreButton(
      has_more_comments,
      "loadMoreCommentsBtn",
    );
  }

  /**
   * Get filter values as array
   * @param {string} filterName - Filter name
   * @returns {Array<string>}
   */
  getFilterValues(filterName) {
    const value = state.getFilter(filterName);
    return value ? value.split(",") : [];
  }

  /**
   * Update active filters display
   */
  updateActiveFiltersDisplay() {
    const container = dom.get("activeFilters");
    if (!container) return;

    container.textContent = "";
    const filters = state.getFilters();

    filters.forEach((value, key) => {
      value.split(",").forEach((val) => {
        // Skip page filter on initial load
        if (key === "page" && val === "1") return;

        const filterBtn = createNode(
          "button",
          `${key}: ${val}`,
          container,
          "active-filter-btn",
        );
        const closeIcon = createNode(
          "i",
          null,
          filterBtn,
          "fa-solid fa-xmark",
        );

        filterBtn.addEventListener("click", () =>
          this.removeFilterValue(key, val),
        );
      });
    });

    this.addResetButton();
  }

  /**
   * Remove a specific filter value
   * @param {string} key - Filter key
   * @param {string} value - Filter value to remove
   */
  removeFilterValue(key, value) {
    const currentValues = this.getFilterValues(key);
    const updatedValues = currentValues.filter((v) => v !== value);

    if (updatedValues.length > 0) {
      state.setFilter(key, updatedValues.join(","));
    } else {
      state.deleteFilter(key);
      // Clear input if it's a search filter
      if (key === "search") {
        const searchInput = dom.get("searchInput");
        if (searchInput) searchInput.value = "";
        this.resetSearchBtn.remove();
      }
    }

    this.applyFilters();
  }

  /**
   * Add reset all button
   */
  addResetButton() {
    const container = dom.get("activeFilters");
    if (!container) return;

    const filters = state.getFilters();

    let filterCount = 0;
    filters.forEach((value, key) => {
      if (key !== "page" || value !== "1") filterCount++;
    });

    if (filterCount >= 2) {
      const resetBtn = createNode(
        "button",
        "Clear",
        container,
        "reset-btn",
      );
      resetBtn.addEventListener("click", () => this.resetAllFilters());
    }
  }

  /**
   * Reset all filters
   */
  resetAllFilters() {
    state.clearDebounce("search");
    state.resetFilters();

    // Clear all inputs
    const searchInput = dom.get("searchInput");
    if (searchInput) searchInput.value = "";
    this.resetSearchBtn.remove();

    dom.getAll('input[type="checkbox"]').forEach((input) => {
      input.checked = false;
    });

    this.applyFilters();
  }

  /**
   * Update button states
   */
  updateButtonStates() {
    this.updateFilterButtonStates();
    this.updateCheckboxStates();
    this.updateSortState();
    this.updateSearchState();
  }

  /**
   * Update filter button active states
   */
  updateFilterButtonStates() {
    const buttons = dom.getAll(CONFIG.SELECTORS.filterBtn);

    buttons.forEach((button) => {
      const [key, value] = button.dataset.filter.split(":");
      const hasFilter = state.hasFilter(key);

      if (hasFilter) {
        button.classList.toggle(
          CONFIG.CLASSES.active,
          state.getFilter(key) === value,
        );
      } else {
        button.classList.toggle(CONFIG.CLASSES.active, value === "all");
      }
    });
  }

  /**
   * Update checkbox states
   */
  updateCheckboxStates() {
    const checkboxes = dom.getAll(CONFIG.SELECTORS.filterCheckbox);

    checkboxes.forEach((checkbox) => {
      const [filterName, filterValue] =
        checkbox.dataset.filter.split(":");
      const currentValues = this.getFilterValues(filterName);
      checkbox.checked = currentValues.includes(filterValue);
    });
  }

  /**
   * Update sort select state
   */
  updateSortState() {
    const sortSelect = dom.get("sortSelect");
    if (!sortSelect) return;

    const currentSort = state.getFilter("sort");
    sortSelect.value = currentSort || "featured";
  }

  /**
   * Update search input state
   */
  updateSearchState() {
    const searchInput = dom.get("searchInput");
    if (!searchInput) return;
    if (document.activeElement === searchInput) return;

    const searchValue = state.getFilter("search");
    searchInput.value = searchValue || "";
  }
}

export default FilterManager;
