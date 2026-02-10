/**
 * QuillEditorManager - Manages Quill rich text editor instances
 * Handles initialization, formatting, and content synchronization
 */

import { dom } from '../core/DOMManager.js';

class QuillEditorManager {
  constructor() {
    this.quillInstance = null;
    this.editorSelector = '#editor-container';
    this.contentInputSelector = 'input[name=description]';
    this.deleteButtonSelector = '#quillDeleteButton';
  }

  /**
   * Initialize Quill editor if container exists
   * @returns {boolean} Whether initialization was successful
   */
  init() {
    const container = document.querySelector(this.editorSelector);
    if (!container) return false;

    this.initializeQuill();
    this.setupContentSync();
    this.setupToolbarFocusFix();
    this.setupDeleteButton();

    return true;
  }

  /**
   * Initialize Quill instance with configuration
   */
  initializeQuill() {
    this.quillInstance = new Quill(this.editorSelector, {
      theme: 'snow',
      modules: {
        toolbar: {
          container: this.getToolbarConfig(),
          handlers: this.getCustomHandlers(),
        },
      },
    });
  }

  /**
   * Get toolbar configuration
   * @returns {Array} Toolbar configuration
   */
  getToolbarConfig() {
    return [
      [{ 'font': [] }],
      [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
      [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'align': [] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'color': [] }, { 'background': [] }],
      ['link', 'video', 'image', { 'code-block': true }],
    ];
  }

  /**
   * Get custom toolbar handlers
   * @returns {Object} Handler functions
   */
  getCustomHandlers() {
    return {
      bold: (value) => this.applyFormat('bold', value),
      italic: (value) => this.applyFormat('italic', value),
      underline: (value) => this.applyFormat('underline', value),
      strike: (value) => this.applyFormat('strike', value),
    };
  }

  /**
   * Apply formatting and refocus editor
   * @param {string} format - Format to apply
   * @param {*} value - Format value
   */
  applyFormat(format, value) {
    if (!this.quillInstance) return;
    
    this.quillInstance.format(format, value);
    setTimeout(() => this.quillInstance.focus(), 0);
  }

  /**
   * Setup content synchronization with hidden input
   */
  setupContentSync() {
    const form = document.querySelector('form');
    const contentInput = document.querySelector(this.contentInputSelector);
    
    if (!form || !contentInput) return;

    form.addEventListener('submit', () => {
      if (this.quillInstance) {
        contentInput.value = this.quillInstance.root.innerHTML;
      }
    });
  }

  /**
   * Prevent toolbar buttons from stealing focus
   */
  setupToolbarFocusFix() {
    const toolbarElements = document.querySelectorAll(
      '.ql-toolbar button, .ql-toolbar span'
    );
    
    toolbarElements.forEach(el => {
      el.addEventListener('mousedown', e => e.preventDefault());
    });
  }

  /**
   * Setup delete button to clear editor content
   */
  setupDeleteButton() {
    const deleteBtn = document.querySelector(this.deleteButtonSelector);
    const contentInput = document.querySelector(this.contentInputSelector);
    
    if (!deleteBtn) return;

    deleteBtn.addEventListener('click', () => {
      if (this.quillInstance) {
        this.quillInstance.setText('');
      }
      if (contentInput) {
        contentInput.value = '';
      }
    });
  }

  /**
   * Get current editor content as HTML
   * @returns {string} HTML content
   */
  getContent() {
    return this.quillInstance?.root.innerHTML || '';
  }

  /**
   * Set editor content
   * @param {string} html - HTML content to set
   */
  setContent(html) {
    if (this.quillInstance) {
      this.quillInstance.root.innerHTML = html;
    }
  }

  /**
   * Clear editor content
   */
  clear() {
    if (this.quillInstance) {
      this.quillInstance.setText('');
    }
    
    const contentInput = document.querySelector(this.contentInputSelector);
    if (contentInput) {
      contentInput.value = '';
    }
  }

  /**
   * Check if editor has content
   * @returns {boolean}
   */
  hasContent() {
    if (!this.quillInstance) return false;
    
    const text = this.quillInstance.getText().trim();
    return text.length > 0;
  }

  /**
   * Enable/disable editor
   * @param {boolean} enabled - Whether to enable the editor
   */
  setEnabled(enabled) {
    if (this.quillInstance) {
      this.quillInstance.enable(enabled);
    }
  }

  /**
   * Destroy the editor instance
   */
  destroy() {
    if (this.quillInstance) {
      // Quill doesn't have a built-in destroy method
      const container = document.querySelector(this.editorSelector);
      if (container) {
        container.innerHTML = '';
      }
      this.quillInstance = null;
    }
  }
}

export default QuillEditorManager;