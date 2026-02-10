/**
 * StateManager - Centralized state management
 * Handles all application state including filters, pagination, and request lifecycle
 */

class StateManager {
  constructor() {
    if (StateManager.instance) {
      return StateManager.instance;
    }

    this.filters = new URLSearchParams(window.location.search);
    this.abortController = null;
    this.debounceTimers = new Map();
    this.eventListeners = new Map();
    this.pageStates = {
      brands: 1,
      comments: 1,
    };

    StateManager.instance = this;
  }

  // Filter Management
  /**
   * Set a filter value
   * @param {string} key - Filter key
   * @param {string|number} value - Filter value
   */
  setFilter(key, value) {
    this.filters.set(key, String(value));
  }

  /**
   * Get a filter value
   * @param {string} key - Filter key
   * @returns {string|null}
   */
  getFilter(key) {
    return this.filters.get(key);
  }

  /**
   * Delete a filter
   * @param {string} key - Filter key
   */
  deleteFilter(key) {
    this.filters.delete(key);
  }

  /**
   * Check if filter exists
   * @param {string} key - Filter key
   * @returns {boolean}
   */
  hasFilter(key) {
    return this.filters.has(key);
  }

  /**
   * Get all filters as URLSearchParams
   * @returns {URLSearchParams}
   */
  getFilters() {
    return this.filters;
  }

  /**
   * Get filters as query string
   * @returns {string}
   */
  getQueryString() {
    return this.filters.toString();
  }

  /**
   * Reset all filters
   */
  resetFilters() {
    this.filters = new URLSearchParams();
  }

  /**
   * Update filters from URL
   */
  syncFromURL() {
    this.filters = new URLSearchParams(window.location.search);
  }

  /**
   * Update URL from filters
   * @param {boolean} replaceState - Use replaceState instead of pushState
   */
  syncToURL(replaceState = true) {
    const queryString = this.getQueryString();
    const newUrl = `${window.location.pathname}${queryString ? '?' + queryString : ''}`;
    
    if (replaceState) {
      window.history.replaceState(null, '', newUrl);
    } else {
      window.history.pushState(null, '', newUrl);
    }
  }

  // Request Lifecycle Management
  /**
   * Cancel any pending request
   */
  cancelPendingRequest() {
    if (this.abortController) {
      this.abortController.abort();
      this.abortController = null;
    }
  }

  /**
   * Create a new abort controller for requests
   * @returns {AbortController}
   */
  createAbortController() {
    this.cancelPendingRequest();
    this.abortController = new AbortController();
    return this.abortController;
  }

  /**
   * Get current abort signal
   * @returns {AbortSignal|null}
   */
  getAbortSignal() {
    return this.abortController?.signal || null;
  }

  // Debounce Management
  /**
   * Set a debounce timer
   * @param {string} key - Timer key
   * @param {Function} callback - Function to execute
   * @param {number} delay - Delay in milliseconds
   */
  debounce(key, callback, delay) {
    this.clearDebounce(key);
    const timer = setTimeout(callback, delay);
    this.debounceTimers.set(key, timer);
  }

  /**
   * Clear a specific debounce timer
   * @param {string} key - Timer key
   */
  clearDebounce(key) {
    const timer = this.debounceTimers.get(key);
    if (timer) {
      clearTimeout(timer);
      this.debounceTimers.delete(key);
    }
  }

  /**
   * Clear all debounce timers
   */
  clearAllDebounce() {
    this.debounceTimers.forEach(timer => clearTimeout(timer));
    this.debounceTimers.clear();
  }

  // Page State Management
  /**
   * Set page number for a content type
   * @param {string} type - 'brands' or 'comments'
   * @param {number} page - Page number
   */
  setPage(type, page) {
    this.pageStates[type] = page;
  }

  /**
   * Get page number for a content type
   * @param {string} type - 'brands' or 'comments'
   * @returns {number}
   */
  getPage(type) {
    return this.pageStates[type] || 1;
  }

  /**
   * Increment page number
   * @param {string} type - 'brands' or 'comments'
   * @returns {number} New page number
   */
  incrementPage(type) {
    this.pageStates[type] = (this.pageStates[type] || 1) + 1;
    return this.pageStates[type];
  }

  /**
   * Reset page number
   * @param {string} type - 'brands' or 'comments'
   */
  resetPage(type) {
    this.pageStates[type] = 1;
  }

  // Event Listener Management
  /**
   * Track event listener to prevent duplicates
   * @param {string} key - Listener key
   * @param {boolean} attached - Whether listener is attached
   */
  setListenerState(key, attached) {
    this.eventListeners.set(key, attached);
  }

  /**
   * Check if event listener is already attached
   * @param {string} key - Listener key
   * @returns {boolean}
   */
  isListenerAttached(key) {
    return this.eventListeners.get(key) === true;
  }

  /**
   * Reset all state
   */
  reset() {
    this.resetFilters();
    this.cancelPendingRequest();
    this.clearAllDebounce();
    this.pageStates = { brands: 1, comments: 1 };
    this.eventListeners.clear();
  }
}

// Export singleton instance
export const state = new StateManager();
export default state;