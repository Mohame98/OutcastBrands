/**
 * Helper Utilities
 * Pure utility functions used across the application
 */

import { errorHandler } from '../core/ErrorHandler.js';
import { CONFIG } from '../config.js'

/**
 * Create a DOM node with optional properties
 * @param {string} type - Element type
 * @param {string} text - Text content (or src for img)
 * @param {HTMLElement} parentNode - Parent element
 * @param {string} className - CSS class name
 * @param {string} id - Element ID
 * @param {string} href - Href attribute
 * @param {Object} attributes - Additional attributes
 * @returns {HTMLElement}
 */
export function createNode(type, text = null, parentNode = null, className = null, id = null, href = null, attributes = {}) {
  const node = document.createElement(type);
  
  if (text && type !== 'img') {
    node.appendChild(document.createTextNode(text));
  }
  
  if (className) node.className = className;
  if (id) node.id = id;
  if (href) node.href = href;
  if (type === 'img') node.src = text;
  
  for (const [key, value] of Object.entries(attributes)) {
    node.setAttribute(key, value);
  }
  
  if (parentNode) parentNode.appendChild(node);
  
  return node;
}

/**
 * Setup password toggle buttons
 * Shows/hides password text
 */
export function setupPasswordToggles() {
  const toggleButtons = document.querySelectorAll('.toggle-password');
  
  toggleButtons.forEach(button => {
    button.addEventListener('click', function () {
      const wrapper = button.closest('.password-field');
      const passwordInput = wrapper.querySelector('.password-input');
      const icon = button.querySelector('i');
      const caption = button.querySelector('.hover-caption');
      const isPassword = passwordInput.type === 'password';

      passwordInput.type = isPassword ? 'text' : 'password';
      icon?.classList.toggle('fa-eye');
      icon?.classList.toggle('fa-eye-slash');
      
      if (caption) {
        caption.textContent = isPassword ? 'Hide' : 'Show';
      }
      
      button.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
      button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
    });
  });
}

/**
 * Show specific error message
 * LEGACY WRAPPER - Calls ErrorHandler
 * @param {string} message - Error message
 * @param {string} field - Field name
 */
export function handleSpecificError(message, field) {
  errorHandler.handleSpecificError(message, field);
}

/**
 * Clear specific error message
 * LEGACY WRAPPER - Calls ErrorHandler
 * @param {string} field - Field name
 */
export function clearSpecificError(field) {
  errorHandler.clearSpecificError(field);
}

/**
 * Close modal after successful form submission
 * @param {Object} result - Form submission result
 * @param {HTMLFormElement} form - Form element
 */
export function closeModal(result, form) {
  const dialog = form.closest('dialog');
  if (!result.success) return;
  
  form.reset();
  
  if (!dialog) return;
  
  dialog.close();
  
  if (document.body.classList.contains('no-scroll')) {
    document.body.classList.remove('no-scroll');
  }
  
  Array.from(document.body.children).forEach(el => {
    el.removeAttribute('inert');
  });
}

/**
 * Setup category selection limit
 * Limits number of checkboxes that can be selected
 */
export function selectOnlyThreeCategories() {
  const container = document.querySelector('.category-list');
  if (!container) return;
  
  const checkboxes = container.querySelectorAll('.category-checkbox');
  const maxAllowed = parseInt(container.dataset.limit || 3);

  function updateCheckboxStates() {
    const checkedCount = container.querySelectorAll('.category-checkbox:checked').length;
    
    if (checkedCount >= maxAllowed) {
      checkboxes.forEach(cb => {
        if (!cb.checked) cb.disabled = true;
      });
    } else {
      checkboxes.forEach(cb => cb.disabled = false);
    }
  }
  
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', updateCheckboxStates);
  });
  
  updateCheckboxStates();
}

/**
 * Trim all inputs and textareas on blur
 */
export function trimInputs() {
  document.addEventListener('focusout', (e) => {
    if (e.target.matches('input, textarea')) {
      e.target.value = e.target.value.trim();
    }
  });
}

/**
 * Format a number with commas
 * @param {number} num - Number to format
 * @returns {string}
 */
export function formatNumber(num) {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/**
 * Safely parse JSON
 * @param {string} json - JSON string
 * @param {*} fallback - Fallback value
 * @returns {*}
 */
export function safeJSONParse(json, fallback = null) {
  try {
    return JSON.parse(json);
  } catch (e) {
    console.error('JSON parse error:', e);
    return fallback;
  }
}

/**
 * Check if element is in viewport
 * @param {HTMLElement} element - Element to check
 * @returns {boolean}
 */
export function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

/**
 * Scroll to element smoothly
 * @param {HTMLElement} element - Element to scroll to
 * @param {number} offset - Offset from top
 */
export function scrollToElement(element, offset = 0) {
  const elementPosition = element.getBoundingClientRect().top;
  const offsetPosition = elementPosition + window.pageYOffset - offset;

  window.scrollTo({
    top: offsetPosition,
    behavior: 'smooth'
  });
}

/**
 * Debounce a function
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in milliseconds
 * @returns {Function}
 */
export function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Throttle a function
 * @param {Function} func - Function to throttle
 * @param {number} limit - Time limit in milliseconds
 * @returns {Function}
 */
export function throttle(func, limit) {
  let inThrottle;
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}