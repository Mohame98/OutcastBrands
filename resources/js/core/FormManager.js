/**
 * FormManager - Handles all form submissions
 * Centralized form handling with validation and response processing
 */

import { CONFIG } from '../config.js';
import APIService from '../services/APIService.js';
import UIService from '../services/UIService.js';
import FormResponseHandler from '../features/FormResponseHandler.js';

class FormManager {
  constructor() {
    this.responseHandler = new FormResponseHandler();
    this.MIN_LOADER_DURATION = CONFIG.MIN_LOADER_DURATION || 300;
  }

  /**
   * Initialize form handling
   */
  init() {
    this.attachFormListener();
  }

  /**
   * Attach global form submit listener
   */
  attachFormListener() {
    document.body.addEventListener('submit', (event) => {
      if (event.target?.matches('.action-form')) {
        this.handleFormSubmit(event);
      }
    });
  }

  /**
   * Handle form submission
   * @param {Event} event - Submit event
   */
  async handleFormSubmit(event) {
    event.preventDefault();

    const form = event.target.closest('form');
    if (!form) return;

    const submitter = event.submitter || form.querySelector('[type="submit"]');
    const startTime = Date.now();

    // Pass the button reference directly
    this.showButtonSpinner(submitter); 

    const formData = new FormData(form);
    const method = form.method || 'POST';
    const actionUrl = this.getFormActionURL(event, form);
    const csrfToken = this.getCSRFToken();

    await this.submitForm(actionUrl, formData, method, csrfToken, form, submitter, startTime);
}

  /**
   * Submit form to server
   * @param {string} url - Action URL
   * @param {FormData} formData - Form data
   * @param {string} method - HTTP method
   * @param {string} csrfToken - CSRF token
   * @param {HTMLFormElement} form - Form element
   */

  async submitForm(url, formData, method, csrfToken, form, submitter, startTime) {
    try {
      const result = await APIService.submitForm(url, formData, method, csrfToken);

      // Handle the Minimum Loader Time delay here
      const elapsedTime = Date.now() - startTime;
      const remainingDelay = Math.max(0, this.MIN_LOADER_DURATION - elapsedTime);
      if (remainingDelay > 0) {
        await new Promise(resolve => setTimeout(resolve, remainingDelay));
      }

      // Restore button state 
      this.hideButtonSpinner(submitter); 

      if (!result.success) {
        this.handleError(result, form);
        return;
      }

      // Clear errors and handle response SECOND
      // Because hideButtonSpinner ran, responseHandler will find the original <i> tags
      UIService.clearFormErrors(form);
      this.responseHandler.handle(result.data, form, url);

    } catch (error) {
      console.error('API Submission Error:', error);
      this.hideButtonSpinner(submitter);
      UIService.showError('A network error occurred.');
    }
  }

  /**
   * Handle form submission errors
   * @param {Object} result - API result
   * @param {HTMLFormElement} form - Form element
   */
  handleError(result, form) {
    const { status, error } = result;

    switch (status) {
      case CONFIG.HTTP_STATUS.UNAUTHORIZED:
        UIService.showFlashMessage(CONFIG.MESSAGES.UNAUTHORIZED);
        break;

      case CONFIG.HTTP_STATUS.UNPROCESSABLE:
        UIService.showFormErrors(error, form);
        break;

      case CONFIG.HTTP_STATUS.FORBIDDEN:
        window.location.href = '/email/verify';
        break;

      default:
        console.error('Unexpected error:', error);
        UIService.showError(error?.message || 'An unexpected error occurred');
    }
  }

  /**
   * Get form action URL (handles multi-step forms)
   * @param {Event} event - Submit event
   * @param {HTMLFormElement} form - Form element
   * @returns {string}
   */
  getFormActionURL(event, form) {
    const button = event.submitter || event.target.closest('button');
    const step = button?.dataset?.step;
    const baseUrl = form.dataset.formBase;

    if (step && baseUrl) return `${baseUrl}/step-${step}`;
    return form.action;
  }

  /**
   * Get CSRF token from meta tag
   * @returns {string}
   */
  getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
  }

  /**
   * Updated to accept a specific button element
   */
  showButtonSpinner(button) {
    if (!button) return;
    button.disabled = true;
    button.dataset.originalHtml = button.innerHTML;
    UIService.createSpinner(button, null);
  }

  /**
   * Updated to restore the specific button element
   */
  hideButtonSpinner(button) {
    if (!button || !button.dataset.originalHtml) return;
    button.disabled = false;
    button.innerHTML = button.dataset.originalHtml;
    delete button.dataset.originalHtml;
  }
}

export default FormManager;