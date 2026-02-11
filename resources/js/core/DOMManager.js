/**
 * DOMManager - Singleton class for caching and managing DOM elements
 * Prevents repeated querySelector calls and provides type-safe access
 */
import { CONFIG } from '../config.js';

class DOMManager {
  constructor() {
    if (DOMManager.instance) {
      return DOMManager.instance;
    }

    this.elements = new Map();
    this.initializeElements();
    DOMManager.instance = this;
  }

  /**
   * Initialize commonly used DOM elements
   */
  initializeElements() {
    Object.entries(CONFIG.SELECTORS).forEach(([key, selector]) => {
      this.elements.set(key, document.querySelector(selector));
    });
  }

  /**
   * Get a cached DOM element
   * @param {string} key - The element key from CONFIG.SELECTORS
   * @returns {HTMLElement|null}
   */
  get(key) {
    return this.elements.get(key);
  }

  /**
   * Get all elements matching a selector (not cached)
   * @param {string} selector - CSS selector
   * @returns {NodeList}
   */
  getAll(selector) {
    return document.querySelectorAll(selector);
  }

  /**
   * Query a selector (not cached)
   * @param {string} selector - CSS selector
   * @returns {HTMLElement|null}
   */
  query(selector) {
    return document.querySelector(selector);
  }

  /**
   * Refresh a specific cached element
   * @param {string} key - The element key
   */
  refresh(key) {
    const selector = CONFIG.SELECTORS[key];
    if (selector) {
      this.elements.set(key, document.querySelector(selector));
    }
  }

  /**
   * Refresh all cached elements
   */
  refreshAll() {
    this.initializeElements();
  }

  /**
   * Check if an element exists
   * @param {string} key - The element key
   * @returns {boolean}
   */
  exists(key) {
    return this.elements.has(key) && this.elements.get(key) !== null;
  }

  /**
   * Clear the DOM cache
   */
  clear() {
    this.elements.clear();
  }
}

export const dom = new DOMManager();
export default dom;