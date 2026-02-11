/**
 * Application Configuration
 * Central location for all constants and configuration values
 */

export const CONFIG = {
  // Pagination
  BRANDS_PER_PAGE: 6,
  COMMENTS_PER_PAGE: 5,

  // Timing
  SEARCH_DEBOUNCE_MS: 500,
  ANIMATION_DURATION_MS: 200,
  FLASH_MESSAGE_DURATION_MS: 5000,
  MIN_LOADER_DURATION: 300,

  // HTTP Status Codes
  HTTP_STATUS: {
    OK: 200,
    UNAUTHORIZED: 401,
    FORBIDDEN: 403,
    UNPROCESSABLE: 422,
    SERVER_ERROR: 500,
  },

  // API Routes
  ROUTES: {
    profile: {
      pattern: /^\/profile\/(\d+)/,
      getUrl: (path, query) => {
        const userId = path.match(/\/profile\/(\d+)/)[1];
        return `/api/profile/${userId}/brands?${query}`;
      },
    },
    savedBrands: {
      pattern: /^\/saved-brands\/profile$/,
      getUrl: (_, query) => `/api/saved-brands/profile?${query}`,
    },
    search: {
      pattern: /^\/search$/,
      getUrl: (_, query) => `/api/brands/search?${query}`,
    },
  },

  // Selectors
  SELECTORS: {
    searchInput: '#search-input',
    brandsContainer: '#brands-container',
    commentsContainer: '#comments-container',
    activeFilters: '#active-filters',
    loadMoreBtn: '#load-more-brands',
    loadMoreCommentsBtn: '#load-more-comments',
    filterBtn: '.filter-btn',
    filterCheckbox: '.filter-checkbox',
    sortSelect: '#sort-by',
    searchContainer: '.search-container',
    flashMessage: '.flash-message',
    commentCount: '#comment-count',
    modal: '.modal',
  },

  // Form Actions
  FORM_ACTIONS: {
    VOTE: 'vote',
    SAVE: 'save',
    LIKE_COMMENT: 'like-comment',
    ADD_COMMENT: 'add-comment',
    EDIT_COMMENT: 'edit-comment',
    DELETE_COMMENT: 'delete-comment',
    DELETE_BRAND: 'delete-brand',
    CHANGE_PROFILE_IMAGE: 'change-profile-image',
    CHANGE_USERNAME: 'change-username',
    CHANGE_BIO: 'change-bio',
    CHANGE_INSTAGRAM: 'change-instagram',
    CHANGE_LOCATION: 'change-location',
    SIGN_UP: 'sign-up',
    SIGN_IN: 'sign-in',
    FORGOT_PASSWORD: 'forgot-password',
    SEND_CONTACT: 'send-contact-message',
    ADD_BRAND: 'add-brand',
    REPORT: 'report',
  },

  // CSS Classes
  CLASSES: {
    active: 'active',
    error: 'error',
    errorMessage: 'error-message',
    fadeIn: 'fade-in',
    voted: 'voted',
    liked: 'liked',
    noBrands: 'no-brands',
    loading: 'loading',
  },

  // Error Messages
  MESSAGES: {
    NETWORK_ERROR: 'Network error. Please check your connection.',
    FETCH_ERROR: 'Failed to load content. Please try again.',
    UNAUTHORIZED: 'Unauthorized. Please log in',
    NO_BRANDS_FOUND: 'No Brands Found',
    NO_COMMENTS_FOUND: 'No Comments Found',
    UNEXPECTED_ERROR: 'An unexpected error occured, please try again later'
  },
};

export default CONFIG;