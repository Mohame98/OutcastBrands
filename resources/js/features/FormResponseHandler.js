/**
 * FormResponseHandler - Handles responses from form submissions
 * Routes responses to appropriate handlers based on action type
 */

import { CONFIG } from '../config.js';
import { dom } from '../core/DOMManager.js';
import { errorHandler } from '../core/ErrorHandler.js';
import UIService from '../services/UIService.js';
import CommentsManager from './CommentsManager.js';

class FormResponseHandler {
  constructor() {
    this.commentsManager = new CommentsManager();
    this.actionHandlers = {
      [CONFIG.FORM_ACTIONS.VOTE]: this.handleVote.bind(this),
      [CONFIG.FORM_ACTIONS.SAVE]: this.handleSave.bind(this),
      [CONFIG.FORM_ACTIONS.LIKE_COMMENT]: this.handleLikeComment.bind(this),
      [CONFIG.FORM_ACTIONS.ADD_COMMENT]: this.handleAddComment.bind(this),
      [CONFIG.FORM_ACTIONS.EDIT_COMMENT]: this.handleEditComment.bind(this),
      [CONFIG.FORM_ACTIONS.DELETE_COMMENT]: this.handleDeleteComment.bind(this),
      [CONFIG.FORM_ACTIONS.DELETE_BRAND]: this.handleDeleteBrand.bind(this),
      [CONFIG.FORM_ACTIONS.CHANGE_PROFILE_IMAGE]: this.handleProfileImage.bind(this),
      [CONFIG.FORM_ACTIONS.CHANGE_USERNAME]: this.handleUsername.bind(this),
      [CONFIG.FORM_ACTIONS.CHANGE_BIO]: this.handleBio.bind(this),
      [CONFIG.FORM_ACTIONS.CHANGE_INSTAGRAM]: this.handleInstagram.bind(this),
      [CONFIG.FORM_ACTIONS.CHANGE_LOCATION]: this.handleLocation.bind(this),
      [CONFIG.FORM_ACTIONS.SIGN_UP]: this.handleAuth.bind(this),
      [CONFIG.FORM_ACTIONS.SIGN_IN]: this.handleAuth.bind(this),
      [CONFIG.FORM_ACTIONS.FORGOT_PASSWORD]: this.handleAuth.bind(this),
      [CONFIG.FORM_ACTIONS.SEND_CONTACT]: this.handleContact.bind(this),
      [CONFIG.FORM_ACTIONS.ADD_BRAND]: this.handleMultiStep.bind(this),
      [CONFIG.FORM_ACTIONS.REPORT]: this.handleMultiStep.bind(this),
    };
  }

  /**
   * Handle form response
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   * @param {string} actionUrl - Form action URL
   */
  handle(result, form, actionUrl) {
    const actionType = form.dataset.action;

    // Always clear errors first
    errorHandler.clearFormErrors(form);

    // Check for errors in various formats
    const hasErrors = result.errors || result.error;

    // Display errors if present
    if (hasErrors) {
      errorHandler.displayErrors(result, form);
      return; // Stop processing if there are errors
    }

    // Execute specific handler if exists
    const handler = this.actionHandlers[actionType];
    if (handler) handler(result, form, actionUrl);
  
    // Handle multi-step forms
    this.handleMultiStepProgress(result, form);

    // Show flash message if present
    if (result.message) {
      const type =
        result.success === true
          ? "success"
          : result.success === false
          ? "error"
          : "info";
      UIService.showFlashMessage(result.message, type);
    }

    // Close modal if submission is complete
    if (result.success && form.dataset.submission === 'true') {
      UIService.closeModal(form);
    }
  }

  /**
   * Handle save response
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleSave(result, form) {
    if (typeof result.saved === 'undefined') return;

    const brandId = form.dataset.brandId;
    if (!brandId) return;

    // Update all the save buttons for this brand
    const saveBtns = form.querySelectorAll(`.save-btn[data-brand-id="${brandId}"]`);

    saveBtns.forEach(btn => {
      UIService.updateSaveButton(result.saved, btn);
    });

    // Update save count if present
    document.querySelectorAll('.detail-save-count').forEach(el => {
      if (result.total_saves !== undefined) {
        el.textContent = result.total_saves;
      }
    });
  }

  handleVote(result, form) {
    if (result.total_votes === undefined || !result.action) return;
    const brandId = result.brand_id ?? form.dataset.brandId;
    if (!brandId) return;

    // Find all voting containers with this brand ID
    const containers = document.querySelectorAll(`.voting[data-brand-id="${brandId}"]`);

    // Return early if no containers are found
    if (containers.length === 0) return;  

    // Loop through all containers that have the matching brand ID
    containers.forEach(container => {
      UIService.updateVoteCount(result.total_votes, container);

      const upvoteBtns = container.querySelectorAll('.upvote');
      const downvoteBtns = container.querySelectorAll('.downvote');

      upvoteBtns.forEach(btn => btn.classList.remove(CONFIG.CLASSES.voted));
      downvoteBtns.forEach(btn => btn.classList.remove(CONFIG.CLASSES.voted));

      if (result.action === 'upvoted') {
        upvoteBtns.forEach(btn => btn.classList.add(CONFIG.CLASSES.voted));
      } else if (result.action === 'downvoted') {
        downvoteBtns.forEach(btn => btn.classList.add(CONFIG.CLASSES.voted));
      }
    });

    document.querySelectorAll('.detail-vote-count').forEach(el => {
      el.textContent = result.total_votes;
    });
  }

  /**
   * Handle like comment response
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleLikeComment(result, form) {
    if (typeof result.likes_count === 'undefined' || typeof result.liked === 'undefined') return;  
    const likeBtn = form.querySelector('.like-btn');
    const likeCount = form.closest('.comment-like-container')?.querySelector('.like-count');

    if (likeBtn) {
      likeBtn.innerHTML = result.liked
        ? '<i class="fa-solid fa-heart"></i>'
        : '<i class="fa-regular fa-heart"></i>';
      likeBtn.classList.toggle(CONFIG.CLASSES.liked, result.liked);
    }

    if (likeCount) {
      likeCount.textContent = result.likes_count;
    }
  }

  /**
   * Handle add comment response
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleAddComment(result, form) {
    this.commentsManager.addComment(result.html_comment, result.comments_count);
    form.reset();
  }

  /**
   * Handle edit comment response
   * @param {Object} result - Response data
   */
  handleEditComment(result) {
    this.commentsManager.updateComment(result.comment_id, result.html_comment);
  }

  /**
   * Handle delete comment response
   * @param {Object} result - Response data
   */
  handleDeleteComment(result) {
    this.commentsManager.removeComment(result.comment_id, result.comments_count);
  }

  /**
   * Handle delete brand response
   * @param {Object} result - Response data
   */
  handleDeleteBrand(result) {
    if (result.success && result.redirect_url) {
      window.location.href = result.redirect_url;
    }
  }

  /**
   * Handle profile image change
   * @param {Object} result - Response data
   */
  handleProfileImage(result, form) {
    const profile = document.querySelector('.main-nav .avatar');
    if (!profile) return;

    if (result.profile_image_url) {
      profile.style.backgroundImage = `url("${result.profile_image_url}")`;
      profile.style.backgroundColor = '';
      profile.textContent = '';
      profile.classList.replace('letter', 'img');
    } else {
      profile.style.backgroundImage = '';
      profile.classList.replace('img', 'letter');

      const seed = result.email || result.email_initial;
      profile.style.backgroundColor = UIService.pastelFromString(seed);
      profile.textContent = result.email_initial;
    }
  }

  /**
   * Handle username change
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleUsername(result, form) {
    if (!result.username) return;
    this.updateFormInput(form, '#username', result.username);

    const userDisplay = document.querySelector('.current-username');
    if (userDisplay) {
      userDisplay.textContent = `Username : ${result.username}`;
    }
  }

  /**
   * Handle bio change
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleBio(result, form) {
    this.updateFormInput(form, '#bio', result.bio);
  }

  /**
   * Handle instagram change
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleInstagram(result, form) {
    this.updateFormInput(form, '#instagram', result.instagram);
  }

  /**
   * Handle location change
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleLocation(result, form) {
    this.updateFormInput(form, '#user_location', result.user_location);
  }

  /**
   * Handle authentication actions (sign in, sign up, forgot password)
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleAuth(result, form) {
    if (!result.success) return;
    window.location.reload();
    form.reset();
  }

  /**
   * Handle contact form submission
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleContact(result, form) {
    if (result.success) form.reset();
  }

  /**
   * Handle multi-step form actions
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   * @param {string} actionUrl - Form action URL
   */
  handleMultiStep(result, form, actionUrl) {
    const actionType = form.dataset.action;
    
    const stepUrls = {
      [CONFIG.FORM_ACTIONS.ADD_BRAND]: '/add-brands/step-4',
      [CONFIG.FORM_ACTIONS.REPORT]: '/report/step-2',
    };

    const expectedUrl = stepUrls[actionType];
    
    if (result.success && expectedUrl && actionUrl === expectedUrl) {
      UIService.closeModal(form);
    }
  }

  /**
   * Handle multi-step form progress
   * @param {Object} result - Response data
   * @param {HTMLFormElement} form - Form element
   */
  handleMultiStepProgress(result, form) {
    if (!result.success || !result.multi_step) return;

    const fieldsets = form.querySelectorAll('.multi-field');
    const current = form.querySelector('.multi-field.active');

    if (!current) {
      console.warn('No active fieldset found.');
      return;
    }

    const currentIndex = Array.from(fieldsets).indexOf(current);
    
    if (currentIndex >= 0 && currentIndex < fieldsets.length - 1) {
      fieldsets[currentIndex].classList.remove(CONFIG.CLASSES.active);
      fieldsets[currentIndex + 1].classList.add(CONFIG.CLASSES.active);
    }
  }

  /**
   * Update a form input value
   * @param {HTMLFormElement} form - Form element
   * @param {string} selector - Input selector
   * @param {string} value - New value
   */
  updateFormInput(form, selector, value) {
    if (!form) return;

    const input = form.querySelector(selector);
    if (input) input.value = value;
    else {
      console.warn(`Input ${selector} not found`);
    }
  }
}

export default FormResponseHandler;