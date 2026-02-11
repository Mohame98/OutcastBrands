/**
 * Main Application Entry Point
 * Initializes all application modules
 */
import { dom } from './core/DOMManager.js';
import { state } from './core/StateManager.js';
import { errorHandler } from './core/ErrorHandler.js';
import MultiStepFormManager from './core/MultiStepFormManager.js';
// import ValidationManager from './core/ValidationManager.js';
import NavigationManager from './core/NavigationManager.js';
import FormManager from './core/FormManager.js';
import UIService from "./services/UIService";

import FilterManager from './features/FilterManager.js';
import BrandsManager from './features/BrandsManager.js';
import CommentsManager from './features/CommentsManager.js';
import QuillEditorManager from './features/QuillEditorManager.js';
import MediaUploadManager from './features/MediaUploadManager.js';
import SliderManager from './features/SliderManager.js';
import ModalManager from './features/ModalManager.js';
import PopoverManager from './features/PopoverManager.js';

// Utility functions
import {
  setupPasswordToggles,
  trimInputs,
  selectOnlyThreeCategories,
} from './utils/helpers.js';

class Application {
  constructor() {
    // Core features (always initialized)
    this.errorHandler = errorHandler; // Singleton
    this.formManager = new FormManager();
    this.modalManager = new ModalManager();
    this.navigationManager = new NavigationManager();
    // this.validationManager = new ValidationManager();
    this.multiStepFormManager = new MultiStepFormManager();
    
    // Conditional features (lazy initialization)
    this.filterManager = null;
    this.brandsManager = null;
    this.commentsManager = null;
    this.quillEditor = null;
    this.mediaUpload = null;
    this.sliderManager = null;
    this.popoverManager = null;
    
    // Store initialized modules
    this.modules = [];
  }

  /**
   * Initialize the application
   */
  init() {
    // Attach global UI listeners FIRST
    UIService.initFlashMessages();

    // Sync state from URL
    state.syncFromURL();

    // Initialize core features (always needed)
    this.initModule('errorHandler', this.errorHandler);
    this.initModule('formManager', this.formManager);
    this.initModule('modalManager', this.modalManager);
    this.initModule('navigationManager', this.navigationManager);
    // this.initModule('validationManager', this.validationManager);
    this.initModule('multiStepFormManager', this.multiStepFormManager);

    // Initialize filter system if filter elements exist
    if (this.hasFilterElements()) {
      this.filterManager = new FilterManager();
      this.initModule('filterManager', this.filterManager);
    }

    // Initialize brands if brands container exists
    if (dom.exists('brandsContainer')) {
      this.brandsManager = new BrandsManager();
      this.initModule('brandsManager', this.brandsManager);
    }

    // Initialize comments if comments container exists
    if (dom.exists('commentsContainer')) {
      this.commentsManager = new CommentsManager();
      this.initModule('commentsManager', this.commentsManager);
    }

    // Initialize Quill editor if editor container exists
    if (document.querySelector('#editor-container')) {
      this.quillEditor = new QuillEditorManager();
      this.initModule('quillEditor', this.quillEditor);
    }

    // Initialize media upload if media inputs exist
    if (this.hasMediaInputs()) {
      this.mediaUpload = new MediaUploadManager();
      this.initModule('mediaUpload', this.mediaUpload);
    }

    // Initialize sliders if slider elements exist
    if (this.hasSliders()) {
      this.sliderManager = new SliderManager();
      this.initModule('sliderManager', this.sliderManager);
    }

    // Initialize popovers if popover elements exist
    if (document.querySelector('[popover]') || document.querySelector('.popover')) {
      this.popoverManager = new PopoverManager();
      this.initModule('popoverManager', this.popoverManager);
    }

    // Initialize utility functions
    this.initUtilityFunctions();

    // Setup global handlers
    this.setupErrorHandler();
    this.setupVisibilityHandler();

    // Log initialized modules
    this.logInitialization();
  }

  /**
   * Initialize utility functions
   */
  initUtilityFunctions() {
    setupPasswordToggles();
    trimInputs();
    selectOnlyThreeCategories();
  }

  /**
   * Initialize a module and track it
   * @param {string} name - Module name
   * @param {Object} module - Module instance
   */
  initModule(name, module) {
    if (module && typeof module.init === 'function') {
      try {
        const result = module.init();
        this.modules.push({ name, module, initialized: result !== false });
        console.log(`✓ ${name} initialized`);
      } catch (error) {
        console.error(`✗ ${name} failed to initialize:`, error);
      }
    }
  }

  /**
   * Check if filter elements exist on the page
   * @returns {boolean}
   */
  hasFilterElements() {
    return (
      dom.exists('searchInput') ||
      dom.exists('sortSelect') ||
      dom.getAll('.filter-btn').length > 0 ||
      dom.getAll('.filter-checkbox').length > 0
    );
  }

  /**
   * Check if media inputs exist on the page
   * @returns {boolean}
   */
  hasMediaInputs() {
    return (
      document.querySelector('#id_media') !== null ||
      document.querySelector('#profile_image') !== null ||
      document.querySelector('.media-input input[type="file"][data-multiple="true"]') !== null
    );
  }

  /**
   * Check if sliders exist on the page
   * @returns {boolean}
   */
  hasSliders() {
    return (
      document.querySelector('.brand-image-slider') !== null ||
      document.querySelector('.media-preview.brand') !== null
    );
  }

  /**
   * Setup global error handler
   */
  setupErrorHandler() {
    window.addEventListener('error', (event) => {
      console.error('Global error:', event.error);
    });

    window.addEventListener('unhandledrejection', (event) => {
      console.error('Unhandled promise rejection:', event.reason);
    });
  }

  /**
   * Setup page visibility handler
   * Cancel pending requests when page becomes hidden
   */
  setupVisibilityHandler() {
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        state.cancelPendingRequest();
        state.clearAllDebounce();
      }
    });
  }

  /**
   * Log initialization status
   */
  logInitialization() {
    console.log('=== Application Initialized ===');
    console.log(`Modules loaded: ${this.modules.length}`);
    this.modules.forEach(({ name, initialized }) => {
      console.log(`  ${initialized ? '✓' : '✗'} ${name}`);
    });
    console.log('===============================');
  }

  /**
   * Get a specific module
   * @param {string} name - Module name
   * @returns {Object|null}
   */
  getModule(name) {
    return this[name] || null;
  }

  /**
   * Cleanup and reset application state
   */
  cleanup() {
    // Cleanup each module
    this.modules.forEach(({ module }) => {
      if (typeof module.destroy === 'function') {
        module.destroy();
      }
    });

    // Reset core state
    state.reset();
    dom.clear();
    
    // Clear module list
    this.modules = [];
  }
}

// Initialize application when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const app = new Application();
  app.init();

  // Make app instance globally available
  window.app = app;
});

export default Application;