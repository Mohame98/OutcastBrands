/**
 * MediaUploadManager - Handles single and multiple file uploads
 * Supports drag & drop, preview, validation, and file management
 */

import { CONFIG } from '../config.js';
import { createNode } from '../utils/helpers.js';
import errorHandler from '../core/ErrorHandler.js';

class MediaUploadManager {
  constructor() {
    this.allowedTypes = ['image/png', 'image/jpeg'];
    this.maxFileSize = 1048576; // 1MB for single uploads
    this.maxTotalSize = 4194304; // 4MB for multiple uploads
    this.maxFiles = 4;
    this.imageFiles = new Map(); // Store files per input
  }

  /**
   * Initialize all media inputs
   */
  init() {
    this.initSingleFileUploads();
    this.initMultipleFileUploads();
  }

  /**
   * Initialize single file upload inputs (profile images, etc.)
   */
  initSingleFileUploads() {
    const fileInputs = document.querySelectorAll('#id_media, #profile_image');
    
    fileInputs.forEach(fileInput => {
      const container = fileInput.closest('.media-input');
      if (!container) return;

      const preview = container.querySelector('.media-preview');
      const clearBtn = container.querySelector('.clear-media-btn');

      this.attachSingleFileListeners(fileInput, container, preview, clearBtn);
    });
  }

  /**
   * Attach event listeners for single file uploads
   */
  attachSingleFileListeners(fileInput, container, preview, clearBtn) {
    // File input change
    fileInput.addEventListener('change', (e) => {
      this.handleSingleFileSelect(e, fileInput, preview, container, clearBtn);
    });

    // Drag and drop
    container.addEventListener('dragover', (e) => {
      e.preventDefault();
      container.classList.add('dragging');
    });

    container.addEventListener('dragleave', (e) => {
      e.preventDefault();
      container.classList.remove('dragging');
    });

    container.addEventListener('drop', (e) => {
      e.preventDefault();
      container.classList.remove('dragging');

      const file = e.dataTransfer.files[0];
      if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        this.handleSingleFileSelect(e, fileInput, preview, container, clearBtn);
      }
    });

    // Clear button
    if (clearBtn) {
      clearBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.clearSingleFile(fileInput, preview, container, clearBtn);
      });
    }
  }

  /**
   * Handle single file selection
   */
  handleSingleFileSelect(e, input, preview, container, clearBtn) {
    e.preventDefault();
    
    const file = e.target.files?.[0] || e.dataTransfer?.files[0];
    if (!file) return;

    const validation = this.validateSingleFile(file);
    if (!validation.valid) {
      this.showError(validation.error, 'profile_image');
      this.disableSubmit();
      return;
    }

    this.clearError('profile_image');
    this.enableSubmit();
    this.showSingleFilePreview(file, preview, container, clearBtn);
  }

  /**
   * Validate single file
   */
  validateSingleFile(file) {
    if (!file.type.startsWith('image')) {
      return { valid: false, error: 'Please select an image file.' };
    }

    if (!this.allowedTypes.includes(file.type)) {
      return { valid: false, error: 'Only JPG and PNG images are allowed.' };
    }

    if (file.size > this.maxFileSize) {
      return { valid: false, error: 'File is too large. Choose image under 1MB.' };
    }

    return { valid: true };
  }

  /**
   * Show preview for single file
   */
  showSingleFilePreview(file, preview, container, clearBtn) {
    const reader = new FileReader();
    
    reader.onload = (e) => {
      const url = e.target.result;
      preview.style.backgroundImage = `url('${url}')`;
      container.classList.add('has-image');
      
      if (clearBtn) {
        clearBtn.style.display = 'block';
      }
    };
    
    reader.readAsDataURL(file);
  }

  /**
   * Clear single file upload
   */
  clearSingleFile(fileInput, preview, container, clearBtn) {
    this.clearError('profile_image');
    preview.style.backgroundImage = '';
    container.classList.remove('has-image');
    fileInput.value = '';
    
    if (clearBtn) {
      clearBtn.style.display = 'none';
    }
  }

  /**
   * Initialize multiple file upload inputs (brand images, etc.)
   */
  initMultipleFileUploads() {
    const fileInputs = document.querySelectorAll(
      '.media-input input[type="file"][data-multiple="true"]'
    );
    
    fileInputs.forEach(fileInput => {
      const container = fileInput.closest('.media-input.brand');
      if (!container) return;

      const preview = container.querySelector('.media-preview.brand');
      const clearBtn = container.querySelector('.clear-media-btn.brand');
      const inputId = fileInput.id || 'default';

      // Initialize storage for this input
      this.imageFiles.set(inputId, []);

      this.attachMultipleFileListeners(
        fileInput,
        container,
        preview,
        clearBtn,
        inputId
      );
    });
  }

  /**
   * Attach event listeners for multiple file uploads
   */
  attachMultipleFileListeners(fileInput, container, preview, clearBtn, inputId) {
    const maxFiles = parseInt(fileInput.dataset.maxFiles || this.maxFiles);
    const maxTotalSize = parseInt(fileInput.dataset.maxSize || this.maxTotalSize);

    // File input change
    fileInput.addEventListener('change', (e) => {
      this.handleMultipleFileSelect(
        e,
        fileInput,
        preview,
        container,
        clearBtn,
        inputId,
        maxFiles,
        maxTotalSize
      );
    });

    // Drag and drop
    container.addEventListener('dragover', (e) => {
      e.preventDefault();
      container.classList.add('dragging');
    });

    container.addEventListener('dragleave', (e) => {
      e.preventDefault();
      container.classList.remove('dragging');
    });

    container.addEventListener('drop', (e) => {
      e.preventDefault();
      container.classList.remove('dragging');

      this.handleMultipleFileDrop(
        e,
        fileInput,
        preview,
        container,
        clearBtn,
        inputId,
        maxFiles,
        maxTotalSize
      );
    });

    // Clear button
    if (clearBtn) {
      clearBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.clearMultipleFiles(fileInput, container, clearBtn, inputId);
      });
    }
  }

  /**
   * Handle multiple file selection
   */
  handleMultipleFileSelect(e, fileInput, preview, container, clearBtn, inputId, maxFiles, maxTotalSize) {
    const allFiles = Array.from(e.target.files);
    const newFiles = allFiles.filter(f => this.allowedTypes.includes(f.type));
    const currentFiles = this.imageFiles.get(inputId) || [];
    const proposedFiles = [...currentFiles, ...newFiles];

    const validation = this.validateMultipleFiles(
      proposedFiles,
      allFiles,
      newFiles,
      maxFiles,
      maxTotalSize
    );

    if (!validation.valid) {
      this.showError(validation.error, 'photos');
      return;
    }

    this.clearError('photos');
    this.updateFileCount(proposedFiles.length);
    this.imageFiles.set(inputId, proposedFiles);
    this.updateFileInput(fileInput, proposedFiles);
    this.updateMultiplePreview(proposedFiles, preview, container, clearBtn);
  }

  /**
   * Handle multiple file drop
   */
  handleMultipleFileDrop(e, fileInput, preview, container, clearBtn, inputId, maxFiles, maxTotalSize) {
    const allFiles = Array.from(e.dataTransfer.files);
    const newFiles = allFiles.filter(f => this.allowedTypes.includes(f.type));
    const currentFiles = this.imageFiles.get(inputId) || [];
    const proposedFiles = [...currentFiles, ...newFiles];

    const validation = this.validateMultipleFiles(
      proposedFiles,
      allFiles,
      newFiles,
      maxFiles,
      maxTotalSize
    );

    if (!validation.valid) {
      this.showError(validation.error, 'photos');
      return;
    }

    this.clearError('photos');
    this.updateFileCount(proposedFiles.length);
    this.imageFiles.set(inputId, proposedFiles);
    this.updateFileInput(fileInput, proposedFiles);
    this.updateMultiplePreview(proposedFiles, preview, container, clearBtn);
  }

  /**
   * Validate multiple files
   */
  validateMultipleFiles(proposedFiles, allFiles, validFiles, maxFiles, maxTotalSize) {
    if (proposedFiles.length > maxFiles) {
      return {
        valid: false,
        error: `You can only upload up to ${maxFiles} images.`,
      };
    }

    const totalSize = proposedFiles.reduce((sum, f) => sum + f.size, 0);
    if (totalSize > maxTotalSize) {
      return {
        valid: false,
        error: `Total size cannot exceed ${this.formatFileSize(maxTotalSize)}.`,
      };
    }

    if (validFiles.length !== allFiles.length) {
      return {
        valid: false,
        error: 'Only JPG and PNG images are allowed.',
      };
    }

    return { valid: true };
  }

  /**
   * Update file input with files
   */
  updateFileInput(fileInput, files) {
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    fileInput.files = dt.files;
  }

  /**
   * Update multiple file preview
   */
  updateMultiplePreview(images, preview, container, clearBtn) {
    this.clearSlickSlides();

    images.forEach(file => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const slideContainer = createNode('div', null, null, 'slide custom-slide');
        const url = e.target.result;
        const img = createNode('div', null, slideContainer, 'brand-image');
        img.style.backgroundImage = `url('${url}')`;
        
        // Add to slick carousel if available
        if (typeof $ !== 'undefined' && $('.media-preview.brand').slick) {
          $('.media-preview.brand').slick('slickAdd', slideContainer.outerHTML);
        } else {
          preview.appendChild(slideContainer);
        }
      };
      reader.readAsDataURL(file);
    });

    container.classList.add('has-image');
    if (clearBtn) {
      clearBtn.style.display = 'block';
    }
  }

  /**
   * Clear multiple files
   */
  clearMultipleFiles(fileInput, container, clearBtn, inputId) {
    this.imageFiles.set(inputId, []);
    this.clearSlickSlides();
    fileInput.value = '';
    this.clearError('photos');
    this.updateFileCount(0);
    container.classList.remove('has-image');
    
    if (clearBtn) {
      clearBtn.style.display = 'none';
    }
  }

  /**
   * Clear slick carousel slides
   */
  clearSlickSlides() {
    if (typeof $ === 'undefined') return;

    const $slider = $('.media-preview.brand');
    const indexesToRemove = [];
    
    $slider.find('.custom-slide').each(function () {
      const slickIndex = $(this).data('slick-index');
      if (typeof slickIndex !== 'undefined') {
        indexesToRemove.push(slickIndex);
      }
    });

    indexesToRemove.sort((a, b) => b - a);
    indexesToRemove.forEach(index => {
      $slider.slick('slickRemove', index);
    });
  }

  /**
   * Update file count display
   */
  updateFileCount(count) {
    const numberContainer = document.querySelector('.files-digit');
    if (numberContainer) {
      numberContainer.textContent = count;
    }
  }

  /**
   * Format file size for display
   */
  formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
  }

  /**
   * Show error message
   */
  showError(message, fieldName) {
    // Use your existing error handling system
    if (typeof errorHandler.handleSpecificError === 'function') {
      errorHandler.handleSpecificError(message, fieldName);
    } else {
      console.error(`${fieldName}: ${message}`);
    }
  }

  /**
   * Clear error message
   */
  clearError(fieldName) {
    // Use your existing error handling system
    if (typeof errorHandler.clearSpecificError === 'function') {
      errorHandler.clearSpecificError(fieldName);
    }
  }

  /**
   * Disable submit button
   */
  disableSubmit() {
    const submitBtn = document.querySelector('.profile-image-modal .update');
    if (submitBtn) {
      submitBtn.disabled = true;
    }
  }

  /**
   * Enable submit button
   */
  enableSubmit() {
    const submitBtn = document.querySelector('.profile-image-modal .update');
    if (submitBtn) {
      submitBtn.disabled = false;
    }
  }
}

export default MediaUploadManager;