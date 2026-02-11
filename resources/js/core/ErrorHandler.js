import { createNode } from '../utils/helpers.js';

class ErrorHandler {
  constructor() {
    this.errors = new Map(); // Track active errors by field
  }

  /**
   * Initialize error handler
   */
  init() {
    return true;
  }

  /**
   * Display errors from server response
   * 
   * @param {Object} response 
   * @param {HTMLFormElement} form - Form element
   *
   */
  displayErrors(response, form) {
    if (!form) {
      console.warn('[ErrorHandler] No form provided');
      return;
    }

    // Clear existing errors first
    this.clearFormErrors(form);

    if (!response.errors && !response.error) {
      return;
    }

    // Display general error if present
    if (response.error) {
      this.showGeneralError(response.error, form);
    }

    // Display field-specific errors
    if (response.errors) {
      this.showFieldErrors(response.errors, form);
    }

    this.scrollToFirstError(form);
    this.announceErrorsToScreenReader(response.errors || {});
  }

  /**
   * Clear all errors from a form
   * @param {HTMLFormElement} form - Form element
   */
  clearFormErrors(form) {
    if (!form) return;

    // Remove error classes from fields
    form.querySelectorAll('.error').forEach(el => {
      el.classList.remove('error');
      el.removeAttribute('aria-invalid');
      el.removeAttribute('aria-describedby');
    });

    // Remove error message elements
    form.querySelectorAll('.error-message').forEach(el => {
      el.remove();
    });

    this.errors.clear();
  }

  /**
   * Show error for a specific field
   * @param {string} fieldName - Field name
   * @param {string} message - Error message
   * @param {HTMLFormElement} form - Form element (optional)
   */
  showFieldError(fieldName, message, form = document) {
    const field = this.findField(fieldName, form);
    
    if (!field) {
      console.warn(`[ErrorHandler] Field not found: ${fieldName}`);
      return;
    }

    // Mark field as invalid
    field.classList.add('error');
    field.setAttribute('aria-invalid', 'true');

    // Create error message element
    const errorId = `error-${fieldName}`;
    const errorElement = this.createErrorElement(message, errorId);

    // Insert error message
    this.insertErrorMessage(field, errorElement, form);

    // Link error to field for accessibility
    field.setAttribute('aria-describedby', errorId);

    // Track error
    this.errors.set(fieldName, message);
  }

  /**
   * Clear error for a specific field
   * @param {string} fieldName - Field name
   * @param {HTMLFormElement} form - Form element (optional)
   */
  clearFieldError(fieldName, form = document) {
    const field = this.findField(fieldName, form);
    if (!field) return;

    // Remove error state
    field.classList.remove('error');
    field.removeAttribute('aria-invalid');
    field.removeAttribute('aria-describedby');

    // Remove error message
    const errorElement = form.querySelector(`#error-${fieldName}`);
    if (errorElement) {
      errorElement.remove();
    }

    // Remove from tracking
    this.errors.delete(fieldName);
  }

  handleSpecificError(message, field) {
    const errorEl = document.querySelector(`#error-${field}`);
    if (errorEl) {
      errorEl.textContent = message;
      errorEl.style.display = 'block';
      errorEl.style.color = 'red';
      errorEl.style.fontSize = '0.75rem';
      errorEl.setAttribute('role', 'alert');
    } else {

      this.showFieldError(field, message);
    }
  }

  /**
   * Legacy method - Clear specific error
   * Maintains compatibility with your old code
   * 
   * @param {string} field - Field name
   */
  clearSpecificError(field) {
    const errorElements = document.querySelectorAll(`#error-${field}`);
    if (errorElements.length > 0) {
      errorElements.forEach(el => {
        el.textContent = '';
        el.style.display = 'none';
      });
    } else {
      // Fallback to new method
      this.clearFieldError(field);
    }
  }

  /**
   * Show general (non-field-specific) error
   * @private
   */
  showGeneralError(message, form) {
    const errorElement = createNode('span', message, null, 'error-message');
    errorElement.setAttribute('role', 'alert');
    errorElement.setAttribute('aria-live', 'assertive');
    
    form.insertAdjacentElement('afterbegin', errorElement);
  }

  /**
   * Show multiple field errors
   * @private
   */
  showFieldErrors(errors, form) {
    Object.entries(errors).forEach(([fieldName, messages]) => {
      // Extract first message if array
      const message = Array.isArray(messages) ? messages[0] : messages;

      // Handle array fields (e.g., photos[])
      const arrayField = form.querySelector(`[name="${fieldName}[]"]`);
      if (arrayField) {
        this.handleArrayFieldError(arrayField, message, fieldName, form);
        return;
      }

      // Handle regular fields
      this.showFieldError(fieldName, message, form);
    });
  }

  /**
   * Handle error for array field (e.g., name="photos[]")
   * @private
   */
  handleArrayFieldError(field, message, fieldName, form) {
    // Mark field as invalid
    field.classList.add('error');
    field.setAttribute('aria-invalid', 'true');

    // Get container from data attribute
    const selector = field.dataset.selector;
    if (!selector) {
      console.warn(`[ErrorHandler] Array field missing data-selector: ${fieldName}`);
      return;
    }

    const container = document.querySelector(`.${selector}`);
    if (!container) {
      console.warn(`[ErrorHandler] Container not found: .${selector}`);
      return;
    }

    // Create and insert error message
    const errorElement = this.createErrorElement(message, `error-${fieldName}`);
    container.insertAdjacentElement('afterend', errorElement);

    // Track error
    this.errors.set(fieldName, message);
  }

  /**
   * Find a field in form by various selectors
   * @private
   */
  findField(fieldName, form) {
    // name="fieldName"
    let field = form.querySelector(`[name="${fieldName}"]`);
    if (field) return field;

    // name="fieldName[]"
    field = form.querySelector(`[name="${fieldName}[]"]`);
    if (field) return field;

    // id="fieldName"
    field = form.querySelector(`#${fieldName}`);
    if (field) return field;

    return null;
  }

  /**
   * Create error message element
   * @private
   */
  createErrorElement(message, id) {
    const errorElement = createNode('span', message, null, 'error-message', id);
    errorElement.setAttribute('role', 'alert');
    errorElement.style.display = 'block';
    errorElement.style.color = 'red';
    errorElement.style.fontSize = '0.75rem';
    return errorElement;
  }

  /**
   * Insert error message using multiple strategies
   * @private
   */
  insertErrorMessage(field, errorElement, form) {
    const label = form.querySelector(`label[for="${field.name}"], label[for="${field.id}"]`);
    if (label) {
      label.insertAdjacentElement('afterend', errorElement);
      return;
    }

    // After field wrapper
    const wrapper = field.closest('.form-group, .field-wrapper, .input-group');
    if (wrapper) {
      wrapper.appendChild(errorElement);
      return;
    }

    field.insertAdjacentElement('afterend', errorElement);
  }

  /**
   * Scroll to first error
   * @private
   */
  scrollToFirstError(form) {
    const firstError = form.querySelector('.error');
    if (!firstError) return;

    // Scroll into view
    firstError.scrollIntoView({
      behavior: 'smooth',
      block: 'center',
    });

    // Focus for keyboard users
    setTimeout(() => {
      if (firstError.focus) {
        firstError.focus();
      }
    }, 300);
  }

  /**
   * Announce errors to screen readers
   * @private
   */
  announceErrorsToScreenReader(errors) {
    const errorCount = Object.keys(errors).length;
    if (errorCount === 0) return;

    // Create/update screen reader announcer
    let announcer = document.querySelector('#error-announcer');
    if (!announcer) {
      announcer = createNode('div', null, document.body, 'sr-only', 'error-announcer');
      announcer.setAttribute('role', 'status');
      announcer.setAttribute('aria-live', 'polite');
      announcer.setAttribute('aria-atomic', 'true');
      announcer.style.position = 'absolute';
      announcer.style.left = '-10000px';
      announcer.style.width = '1px';
      announcer.style.height = '1px';
      announcer.style.overflow = 'hidden';
    }

    const message = errorCount === 1
      ? 'There is 1 error in the form'
      : `There are ${errorCount} errors in the form`;

    announcer.textContent = message;
  }

  /**
   * Check if form has any errors
   * @param {HTMLFormElement} form - Form element
   * @returns {boolean}
   */
  hasErrors(form) {
    return form?.querySelectorAll('.error').length > 0;
  }

  /**
   * Get all tracked errors
   * @returns {Map<string, string>}
   */
  getErrors() {
    return new Map(this.errors);
  }

  /**
   * Get error for specific field
   * @param {string} fieldName - Field name
   * @returns {string|null}
   */
  getFieldError(fieldName) {
    return this.errors.get(fieldName) || null;
  }

  /**
   * Get error count
   * @returns {number}
   */
  getErrorCount() {
    return this.errors.size;
  }
}

export const errorHandler = new ErrorHandler();
export default errorHandler;