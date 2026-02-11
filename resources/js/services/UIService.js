/**
 * UIService - Handles all UI updates and rendering
 * Separates presentation logic from business logic
 */

import { CONFIG } from "../config.js";
import { dom } from "../core/DOMManager.js";
import { createNode } from "../utils/helpers.js";

class UIService {
  /**
   * Render brand cards
   * @param {string} cardsHTML - HTML string of cards
   * @param {boolean} append - Whether to append or replace
   */
  static renderBrands(cardsHTML, append = false) {
    const container = dom.get("brandsContainer");
    if (!container) return;

    if (!append) {
      container.innerHTML = "";
    }

    const parser = new DOMParser();
    const doc = parser.parseFromString(cardsHTML, "text/html");
    const cards = Array.from(doc.body.children);

    if (cards.length === 0 && !append) {
      this.showEmptyState(container, CONFIG.MESSAGES.NO_BRANDS_FOUND);
      return;
    }

    cards.forEach((card) => {
      card.classList.add(CONFIG.CLASSES.fadeIn);
      container.appendChild(card);

      card.addEventListener(
        "animationend",
        () => {
          card.classList.remove(CONFIG.CLASSES.fadeIn);
        },
        { once: true },
      );
    });
  }

  /**
   * Render comments
   * @param {string} commentsHTML - HTML string of comments
   * @param {boolean} append - Whether to append or replace
   */
  static renderComments(commentsHTML, append = false) {
    const container = dom.get("commentsContainer");
    if (!container) return;

    if (!append) {
      container.innerHTML = "";
    }

    const parser = new DOMParser();
    const doc = parser.parseFromString(commentsHTML, "text/html");
    const comments = Array.from(doc.body.children);

    // if (comments.length === 0 && !append) {
    //     this.showEmptyState(container, CONFIG.MESSAGES.NO_COMMENTS_FOUND);
    //     return;
    // }

    comments.forEach((comment) => container.appendChild(comment));
  }

  /**
   * Show empty state message
   * @param {HTMLElement} container - Container element
   * @param {string} message - Message to display
   */
  static showEmptyState(container, message) {
    const messageContainer = createNode(
      "div",
      null,
      container,
      CONFIG.CLASSES.noBrands,
    );
    createNode("p", message, messageContainer);
  }

  /**
   * Show loading state
   * @param {HTMLElement} container - Container element
   */
  static showLoading(container) {
    if (!container) return;
    const loader = createNode(
      "div",
      null,
      container,
      CONFIG.CLASSES.loading,
    );
    createNode("p", "Loading...", loader);
  }

  /**
   * Clear loading state
   * @param {HTMLElement} container - Container element
   */
  static clearLoading(container) {
    if (!container) return;
    const loader = container.querySelector(`.${CONFIG.CLASSES.loading}`);
    if (loader) loader.remove();
  }

  /**
   * Update load more button visibility
   * @param {boolean} hasMore - Whether more content exists
   * @param {string} buttonKey - Button key from dom manager
   */
  static updateLoadMoreButton(hasMore, buttonKey = "loadMoreBtn") {
    const btn = dom.get(buttonKey);
    if (!btn) return;
    btn.style.display = hasMore ? "block" : "none";
  }

  /**
   * Set load more button loading state (spinner + disabled)
   * @param {boolean} loading - Whether button is loading
   * @param {string} buttonKey - Button key from dom manager
   */
  static setLoadMoreButtonLoading(loading, buttonKey = "loadMoreBtn") {
    const btn = dom.get(buttonKey);
    if (!btn) return;

    if (loading) {
      if (!btn.dataset.loadMoreOriginal) {
        btn.dataset.loadMoreOriginal = btn.innerHTML.trim();
      }
      btn.disabled = true;
      btn.innerHTML =
        '<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Loading...';
    } else {
      btn.disabled = false;
      btn.innerHTML = btn.dataset.loadMoreOriginal || "Load more";
    }
  }

  /**
   * Set form submit button loading state (spinner + disabled)
   * Standardized loader for all form submissions (Sign In, Sign Up, Account Updates, etc.)
   * @param {HTMLButtonElement} button - Submit button element
   * @param {boolean} loading - Whether button is loading
   * @param {string} loadingText - Optional custom loading text (default: original text)
   */
  static setFormButtonLoading(button, loading, loadingText = null) {
    if (!button) return;

    if (loading) {
      // Store original content
      if (!button.dataset.originalContent) {
        button.dataset.originalContent = button.innerHTML.trim();
      }
      button.disabled = true;
      
      const text = loadingText || button.textContent.trim();
      button.innerHTML = `<i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> ${text}`;
    } else {
      button.disabled = false;
      button.innerHTML = button.dataset.originalContent || button.innerHTML;
      delete button.dataset.originalContent;
    }
  }

  /**
   * Update brands count display (search / saved brands pages)
   * @param {number} total - Total number of brands
   */
  static updateBrandsCount(total) {
    const el = document.getElementById("brands-count");
    if (!el) return;
    if (total === 0) {
      el.textContent = null;
    } else {
      el.textContent = `${total} brand${total === 1 ? "" : "s"}`;
    }
  }

  /**
   * Decrement brands count by 1 (e.g. when unsaving on saved brands page)
   */
  static decrementBrandsCount() {
    const el = document.getElementById("brands-count");
    if (!el) return;
    const text = el.textContent.trim();
    const current =
      text === "No brands found" ? 0 : parseInt(text, 10) || 0;
    this.updateBrandsCount(Math.max(0, current - 1));
  }

  static initFlashMessages() {
    if (UIService._flashCloseAttached) return;

    const flashParent =
      document.getElementById("flash-message-wrapper") ||
      document.body;

    flashParent.addEventListener("click", (e) => {
      if (!e.target.closest?.(".flash-message .fa-xmark")) return;
      const msg = e.target.closest(".flash-message");
      if (msg) msg.remove();
    });

    UIService._flashCloseAttached = true;
  }

  /**
   * Show flash message
   * @param {string} message - Message to display
   * @param {string} type - Message type (info, success, error)
   */
  static showFlashMessage(message, type = "info") {
    UIService.initFlashMessages();

    dom.getAll(CONFIG.SELECTORS.flashMessage).forEach((msg) =>
      msg.remove()
    );

    const flashParent =
      document.getElementById("flash-message-wrapper") || document.body;

    const flashContainer = createNode(
      "section",
      null,
      flashParent,
      CONFIG.SELECTORS.flashMessage.slice(1),
      null,
      null,
      {
        "data-action": "flash-message",
        role: "alert",
        "aria-live": "assertive",
      }
    );

    const innerDiv = createNode("div");
    createNode("i", null, innerDiv, "fa-solid fa-info");
    createNode("p", message, innerDiv, "flash-text");
    flashContainer.appendChild(innerDiv);

    createNode(
      "i",
      null,
      flashContainer,
      "fa-solid fa-xmark"
    );

    setTimeout(() => {
      flashContainer.remove();
    }, CONFIG.FLASH_MESSAGE_DURATION_MS);
  }
  
  /**
   * Show error message
   * @param {string} message - Error message
   */
  static showError(message) {
    this.showFlashMessage(message, "error");
  }

  /**
   * Clear all error messages from a form
   * @param {HTMLFormElement} form - Form element
   */
  static clearFormErrors(form) {
    if (!form) return;

    form.querySelectorAll(`.${CONFIG.CLASSES.error}`).forEach((el) => {
      el.classList.remove(CONFIG.CLASSES.error);
    });

    // Clear and hide existing error placeholders (Blade #error-*), don't remove them
    form.querySelectorAll(`.${CONFIG.CLASSES.errorMessage}`).forEach(
      (el) => {
        el.textContent = "";
        el.style.display = "none";
      },
    );
  }

  /**
   * Display form errors
   * @param {Object} errors - Error object
   * @param {HTMLFormElement} form - Form element
   */
  static showFormErrors(errors, form) {
    if (!form) return;

    this.clearFormErrors(form);

    // General error message
    if (errors.error) {
      const errorElement = createNode(
        "span",
        errors.error,
        null,
        CONFIG.CLASSES.errorMessage,
      );
      form.insertAdjacentElement("afterbegin", errorElement);
    }

    // Field-specific errors
    if (errors.errors) {
      Object.entries(errors.errors).forEach(([fieldName, messages]) => {
        const messageText = Array.isArray(messages)
          ? messages[0]
          : messages;
        // Match #error-{name} (Blade placeholder); array keys like "photos.0" → #error-photos
        const baseFieldName = fieldName.split(".")[0];
        const existingError =
          form.querySelector(
            `#error-${fieldName.replace(/\./g, "\\.")}`,
          ) || form.querySelector(`#error-${baseFieldName}`);
        const arrayField = form.querySelector(
          `[name="${fieldName}[]"]`,
        );
        const field = form.querySelector(
          `[name="${fieldName}"], [name="${fieldName}[]"]`,
        );

        // Use Blade placeholder when it exists (auth, add-brand, etc.)
        if (existingError) {
          existingError.textContent = messageText;
          existingError.style.display = "block";
          if (field) field.classList.add(CONFIG.CLASSES.error);
          return;
        }

        if (arrayField) {
          const selector = arrayField.dataset.selector;
          const container =
            form.querySelector(`.${selector}`) ||
            document.querySelector(`.${selector}`);
          const errorElement = createNode(
            "span",
            messageText,
            null,
            CONFIG.CLASSES.errorMessage,
            `error-${fieldName}`,
          );
          arrayField.classList.add(CONFIG.CLASSES.error);
          container?.insertAdjacentElement("afterend", errorElement);
        } else if (field) {
          const label = form.querySelector(
            `label[for="${field.id || field.name}"]`,
          );
          const errorElement = createNode(
            "span",
            messageText,
            null,
            CONFIG.CLASSES.errorMessage,
            `error-${fieldName}`,
          );
          field.classList.add(CONFIG.CLASSES.error);
          label?.insertAdjacentElement("afterend", errorElement);
        }
      });
    }
  }

  /**
   * Update vote count display
   * @param {number} count - Vote count
   * @param {HTMLElement} container - Container element
   */
  static updateVoteCount(count, container) {
    if (!container) return;
    const voteCountEl = container.querySelector(".vote-count");
    if (voteCountEl) voteCountEl.textContent = count;
  }

  /**
   * Update save button state with robust icon switching
   * @param {boolean} isSaved - Current saved state from server
   * @param {HTMLElement} button - The button element
   */
  static updateSaveButton(isSaved, button) {
    if (!button) return;

    const brandCard = button.closest('.brand-card');
    const isSavedPage = window.location.pathname.includes('saved');
    
    this.rebuildButtonContent(button, isSaved);

    if (brandCard && isSavedPage) {
      if (!isSaved) {
        this.showUndoOverlay(brandCard, button);
      } else {
        this.removeUndoOverlay(brandCard);
      }
      this.updatePageBrandCount(isSaved);
    }
    button.dataset.saved = isSaved;
  }

  /**
   * Creates the "Undo" overlay using createNode 
   */
  static showUndoOverlay(brandCard, originalButton) {
    brandCard.classList.add('is-unsaved');

    // Check if overlay already exists to prevent duplicates
    if (brandCard.querySelector('.undo-overlay')) return;

    // Create Overlay Container
    const overlay = createNode('div', null, brandCard, 'undo-overlay');

    // Create the Undo Button
    const undoBtn = createNode('button', null, overlay, 'undo-btn', null, null, {
      type: 'button',
      title: 'Restore this brand'
    });

    // Add Icon and Text to the Undo Button
    createNode('i', null, undoBtn, 'fa-solid fa-rotate-left');
    undoBtn.appendChild(document.createTextNode('Undo'));

    // Proxy Click: When this new button is clicked, click the hidden original one
    undoBtn.addEventListener('click', () => {
      originalButton.click();
    });
  }

  /**
   * Removes the overlay and restores card visibility
   */
  static removeUndoOverlay(brandCard) {
    brandCard.classList.remove('is-unsaved');
    const overlay = brandCard.querySelector('.undo-overlay');
    if (overlay) overlay.remove();
  }

  /**
   * Internal helper to rebuild the basic icon/text
   */
  static rebuildButtonContent(button, isSaved) {
    button.innerHTML = '';
    const iconClass = isSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark';
    createNode('i', null, button, iconClass);
    
    if (!button.classList.contains('card')) {
      const text = isSaved ? " Saved" : " Save";
      button.appendChild(document.createTextNode(text));
    }
  }

  /**
   * Update the global brands counter (usually found on Saved Brands or Search pages)
   * @param {boolean} isSaved - Whether the action was a save or unsave
   */
  static updatePageBrandCount(isSaved) {
    const counterEl = document.getElementById('brands-count');
    if (!counterEl) return;

    // Helper to get current number from "12 brands"
    const text = counterEl.textContent.trim();
    const currentCount = text === "No brands found" ? 0 : parseInt(text, 10) || 0;
    
    const newCount = isSaved ? currentCount + 1 : Math.max(0, currentCount - 1);

    if (newCount === 0) {
      counterEl.textContent = "No brands found";
    } else {
      counterEl.textContent = `${newCount} brand${newCount === 1 ? "" : "s"}`;
    }
  }

  /**
   * Get current brands count from display
   * @returns {number}
   */
  static getCurrentBrandsCount() {
    const el = document.getElementById("brands-count");
    if (!el) return 0;
    const text = el.textContent.trim();
    return text === null ? 0 : parseInt(text, 10) || 0;
  }

  /**
   * Update comment count display
   * @param {number} count - Comment count
   */
  static updateCommentCount(count) {
    dom.getAll(CONFIG.SELECTORS.commentCount).forEach((el) => {
      el.textContent = count;
    });

    const container = dom.query(".comment-count-container");
    if (container) {
      container.textContent =
        count === 1 ? `${count} Comment` : `${count} Comments`;
    }
  }

  /**
   * Close modal dialog handles both <dialog> and custom modal
   * @param {HTMLElement} form - Form element inside modal
   */
  static closeModal(form) {
    if (!form) return;

    const dialog = form.closest("dialog");
    if (dialog && dialog.close) {
      const modalManager = window.app?.getModule?.("modalManager");
      if (modalManager?.closeDialog) {
        modalManager.closeDialog(dialog);
      } else {
        dialog.close();
      }
      return;
    }

    const modal = form.closest(CONFIG.SELECTORS.modal);
    if (modal) modal.remove();
  }

  /**
   * create a pastel color from a string
   * @param {string} str - Input string to generate color from
   * @returns 
   */
  static pastelFromString(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
      hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    const hue = Math.abs(hash) % 360;
    return `hsl(${hue}, 65%, 82%)`;
  }


  /**
    * Create a spinner element inside a button    
    * @param {HTMLButtonElement} button - The button to add the spinner to
    * @returns {HTMLElement|null} - The spinner container element or null if button is invalid
    */ 
  static createSpinner(button, text = null) {
    if (!button) return null;

    button.textContent = '';

    // <span class="spinner-container">
    const spinnerContainer = 
      createNode('span', null, button, 'spinner-container');

    // <i class="fa-solid fa-spinner fa-spin"></i>
    createNode('i', null, spinnerContainer, 'fa-solid fa-spinner fa-spin', null, null,
      { 'aria-hidden': 'true' }
    );

    if (text) { 
      createNode( 'span', text, spinnerContainer,'spinner-text');
    }

    return spinnerContainer;
  }
}

export default UIService;